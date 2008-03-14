<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/drivers/lmbDbConnection.interface.php');
lmb_require(dirname(__FILE__) . '/lmbLinterQueryStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbLinterDropStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbLinterInsertStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbLinterManipulationStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbLinterStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbLinterDbInfo.class.php');
lmb_require(dirname(__FILE__) . '/lmbLinterTypeInfo.class.php');

/**
 * class lmbLinterConnection.
 *
 * @package dbal
 * @version $Id: $
 */
class lmbLinterConnection implements lmbDbConnection
{
  const CURSOR_POOL_LIMIT = 64;
  const LINTER_EMPTY_DATASET = -18;
  
  protected $connectionId;
  protected $config;
  protected $transactionCount = 0;
  protected $cursorPool = array();
  protected $mode = null;
  protected $useConnection = false;
  

  function __construct($config)
  {
    $this->config = $config;
  }

  function getType()
  {
    return 'linter';
  }

  function getConnectionId()
  {
    if(!isset($this->connectionId))
      $this->connect();
      
    return $this->connectionId;
  }

  function getHash()
  {
    return crc32(serialize($this->config));
  }

  function getConfigValue($param = null)
  {
    if (is_null($param))
      return $this->config;
    elseif (array_key_exists($param, $this->config))
      return $this->config[$param];
    else
      return false;
  }
  
  function connect()
  {
    $persistent = null;

    if(isset($this->config['charset']) && ($charset = $this->config['charset']))
      $r = linter_set_codepage($this->map_charset($this->config['charset']));
    
    $port = $this->config['port'];
    $database = addslashes($this->config['database']);
    $user = addslashes($this->config['user']);
    $password = addslashes($this->config['password']);
    
    if (is_null($this->mode))
      $this->mode = isset($this->config['extra']['mode']) ? $this->config['extra']['mode'] : TM_AUTOCOMMIT;
      
    $conn = @linter_open_connect($user, $password, $database, $this->mode);
    $this->connectionId = $conn;
    
    if ($conn < 0)
    {
      $this->_raiseError($conn);
      $this->connectionId = null;
    }
	


  }
  
  

  function __wakeup()
  {
    $this->connectionId = null;
  }

  function disconnect()
  {
    if($this->connectionId > 0)
    {
      linter_close_connect($this->connectionId);
      $this->connectionId = null;
    }
  }

  function closeCursor($cursorId)
  {
    linter_close_cursor($cursorId);
    unset($this->cursorPool[$cursorId]);
  }
  
  function _raiseError($code, $conn_id = null, $args=array())
  {
    if (is_null($conn_id))
      $conn_id = $this->connectionId;
      
    if ($code >= 0 && $this->connectionId >= 0)
      return false;
      
    if ($this->connectionId < 0)
    {
      $lin_err = linter_last_error($this->connectionId, LINTER_ERROR);
      $sys_err = linter_last_error($this->connectionId, SYSTEM_ERROR);
      $err_message = sprintf("Linter connect error %s, system error %s\n", $lin_err, $sys_err);
      $err_code = $sys_err;
    }
    elseif($code == LPE_INVALID_CONNECT)
    {
      $err_code = -1;
      $err_message = "Invalid connect";
    }
    elseif($code == LPE_LINTER_ERROR)
    {
      $lin_err = linter_last_error($conn_id, LINTER_ERROR);
      $sys_err = linter_last_error($conn_id, SYSTEM_ERROR);
      $err_message = linter_error_msg($conn_id);
      $err_code = $lin_err;
      if ($err_code <= 2 && $err_code > 0) return self::LINTER_EMPTY_DATASET;
      if ($err_code >= 2000 && $err_code < 3000)
      {
        $err_row = $sys_err & 0xFFFF;
        $err_pos = $sys_err >> 16;
        $err_message .= sprintf(" at row %d, position %d", $err_row, $err_pos);
      }
      else
        $err_message .= sprintf(", system error %d", $sys_err);
    }
    else
    {
      $err_message = sprintf("Linter extension error %d", $code);
      $err_code = $code;
    }
    $err_message .= ". " . count($this->cursorPool)." cursors opened";
    throw new lmbException("Database error: No:" . $err_code.". Description: ".$err_message);
    return true;
  }

  protected function prepare_sql($sql)
  {
    $sql = trim($sql);
    $sql = $this->escapeTableName($sql);
    $sql = $this->escapeInsertFields($sql);
    return $sql;    
  }
  
  protected function handle_cursor_pool()
  {
    if (!$this->useConnection)
    {
      if (count($this->cursorPool) > self::CURSOR_POOL_LIMIT)
      {
        $cursors = array_keys($this->cursorPool);
        $this->closeCursor($cursors[0]);
      }
      $result = linter_open_cursor($this->getConnectionId());
      
      if ($result < 0)
        $this->_raiseError($result);
        
      linter_set_cursor_opt($result, CO_DT_FORMAT, "YYYY-MM-DD HH:MI:SS");
      linter_set_cursor_opt($result, CO_FETCH_BLOBS_AS_USUAL_DATA, 1);

      $this->cursorPool[$result] = "opened";
    }
    else
      $result = true;
      
    return $result;
  }
  
  protected function handle_call_result($res, $cur, $sql)
  {
    if ($res < 0)
    {
      $res = $this->_raiseError($res, $this->useConnection ? $this->connectionId : $cur, array('sql' => $sql));
//      if ($res != self::LINTER_EMPTY_DATASET)
//      {
//        var_dump($sql);
//        var_dump($res);
//      }
      if (!$this->useConnection)
      {
        linter_close_cursor($cur);
        unset($this->cursorPool[$cur]);
      }
      return $res;
    }
    else
      return $cur;
  }
  
  function prepare($sql)
  {
    $result = null;
    $sql = $this->prepare_sql($sql);
    
    $result = $this->handle_cursor_pool();
    
    $res = linter_prepare($this->useConnection ? $this->connectionId : $result, $sql);
    return $this->handle_call_result($res, $result, $sql);
  }
  
  function execute($sql)
  {
    $result = null;
    $sql = $this->prepare_sql($sql);
    
    $result = $this->handle_cursor_pool();
    $res = linter_exec_direct($this->useConnection ? $this->connectionId : $result, $sql);
    
    return $this->handle_call_result($res, $result, $sql);
  }
  
  function pexecute($curId, $args)
  {
    $result = linter_execute($curId, $args);
    return $this->handle_call_result($result, $curId, "");
  }
  
  function cexecute($sql)
  {
    $this->useConnection = true;
    $res = $this->execute($sql);
    $this->useConnection = false;
    return $res;
  }

  function beginTransaction()
  {
    $this->check_mode();
    $this->transactionCount += 1;
    return $this->cexecute('set savepoint sp' . $this->transactionCount . ';');
  }

  function commitTransaction()
  {
    $this->check_mode();
    $result = $this->cexecute('commit to savepoint sp' . $this->transactionCount . ';');
    if ($this->transactionCount) $this->transactionCount -= 1;
    return $result;
  }

  function rollbackTransaction()
  {
    if ($this->transactionCount === 0) return false;
    $this->check_mode();
    $result = $this->cexecute('rollback to savepoint sp' . $this->transactionCount . ';');
    if ($this->transactionCount) $this->transactionCount -= 1;
    return $result;
  }

  function check_mode()
  {
    if (!($this->mode & TM_EXCLUSIVE))
    {
      $this->disconnect();
      $this->mode |= TM_EXCLUSIVE;
      $this->connect();
    }
  }
  
  function newStatement($sql)
  {
    if(preg_match('/^\s*\(*\s*(\w+).*$/m', $sql, $match))
      $statement = $match[1];
    else
      $statement = $sql;
      
    switch(strtoupper($statement))
    {
      case 'SELECT':
      case 'SHOW':
      case 'DESCRIBE':
      case 'EXPLAIN':
      return new lmbLinterQueryStatement($this, $sql);
      case 'DROP':
      return new lmbLinterDropStatement($this, $sql);
      case 'INSERT':
      return new lmbLinterInsertStatement($this, $sql);
      case 'UPDATE':
      case 'DELETE':
      return new lmbLinterManipulationStatement($this, $sql);
      default:
      return new lmbLinterStatement($this, $sql);
    }
  }

  function getTypeInfo()
  {
    return new lmbLinterTypeInfo();
  }

  function getDatabaseInfo()
  {
    return new lmbLinterDbInfo($this, $this->config['database'], true);
  }

  function quoteIdentifier($id)
  {
    if(!$id)
      return '';
      
    $pieces = explode('.', $id);
    $quoted = '"' . $pieces[0] . '"';
    if(isset($pieces[1]))
       $quoted .= '."' . $pieces[1] . '"';
       
    return $quoted;
  }

  function escape($string)
  {
    $string = str_replace("'", "''", $string);
    return $string;
  }
  
  function escapeTableName($sql)
  {
    if (preg_match("#(insert into|create table|from|alter table|update)(\s+)([a-zA-Z_]+)(\s|$)#siU", $sql, $matches, PREG_OFFSET_CAPTURE))
    {
      if ($sql[$matches[3][1]] != '"' && $sql[$matches[3][1]+strlen($matches[1][0])] != '#')
        $sql = preg_replace("#(insert into|create table|from|alter table|update)(\s+){$matches[3][0]}#siU", '$1$2"'.$matches[3][0].'"', $sql);
    }
    return $sql;
  }

  function escapeInsertFields($sql)
  {
    if (preg_match("#insert into .+\((.+)\) values#siU", $sql, $matches))
    {
      $fields_str = $matches[1];
      $fields_arr = explode(",", $fields_str);
      foreach ($fields_arr as $key => $field)
      {
        $field = trim($field);
        if ($field{0} != '"' && $field{strlen($field)-1} != '"')
          $fields_arr[$key] = '"' . $field . '"';
      }
      $sql = preg_replace("#(insert into .+\()(.+)(\) values)#siU", "\\1" . implode(", ", $fields_arr) . "\\3", $sql);
    }
    return $sql;
  }
  
  function getSequenceValue($table, $colname)
  {
    return (int)($this->newStatement("SELECT last_autoinc")->getOneValue());
  }
  
  
  protected function map_charset($charset)
  {
    switch ($charset)
    {
      case "utf-8":
      case "utf8":
      case "UTF-8":
      case "UTF8":
      case "unicode":
      case "UNICODE":
        return "UTF-8";
        break;
      case "windows-1251";
      case "Windows-1251":
      case "cp1251":
      case "CP1251":
      default:
        return "CP1251";
        break;
    }
  }
  
  function getMbCharset()
  {
    if (!isset($this->config['charset']))
    {
      return 'Windows-1251';
    }
    switch ($this->config['charset'])
    {
      case "utf-8":
      case "utf8":
      case "UTF-8":
      case "UTF8":
      case "unicode":
      case "UNICODE":
        return "UTF-8";
        break;
      case "windows-1251";
      case "Windows-1251":
      case "cp1251":
      case "CP1251":
      default:
        return "Windows-1251";
        break;
    }
    
  }
}


