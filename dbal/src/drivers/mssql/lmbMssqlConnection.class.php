<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/dbal/src/drivers/lmbDbBaseConnection.class.php');
lmb_require(dirname(__FILE__) . '/lmbMssqlDbInfo.class.php');
lmb_require(dirname(__FILE__) . '/lmbMssqlQueryStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbMssqlInsertStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbMssqlManipulationStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbMssqlStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbMssqlTypeInfo.class.php');
lmb_require(dirname(__FILE__) . '/lmbMssqlRecord.class.php');
lmb_require(dirname(__FILE__) . '/lmbMssqlRecordSet.class.php');

/**
 * class lmbMssqlConnection.
 *
 * @package dbal
 * @version $Id: lmbMssqlConnection.class.php,v 1.1.1.1 2009/06/08 11:57:21 mike Exp $
 */
class lmbMssqlConnection extends lmbDbBaseConnection
{
  protected $connectionId;
  protected $transactionCount = 0;

  function getType()
  {
    return 'mssql';
  }

  function getConnectionId()
  {
    if(!isset($this->connectionId))
    {
      $this->connect();
    }
    return $this->connectionId;
  }

  function connect()
  {
    ini_set("mssql.datetimeconvert", "Off");
    ini_set("mssql.textsize", "2147483647");
    ini_set("mssql.textlimit", "2147483647");
    $this->connectionId = mssql_connect($this->config['host'],
                                        $this->config['user'],
                                        $this->config['password']
                                        );

    if($this->connectionId === false)
    {
      $this->_raiseError();
    }

    if(mssql_select_db($this->config['database'], $this->connectionId) === false)
    {
      $this->_raiseError();
    }
    mssql_query("SET QUOTED_IDENTIFIER ON");
    mssql_query("SET ANSI_NULL_DFLT_ON ON");
    mssql_query("SET DATEFORMAT ymd");

    //fixme
//    if(isset($this->config['charset']) && $charset = $this->config['charset'])
//    {
//      mysql_query("SET NAMES '$charset'",  $this->connectionId);
//    }
  }

  function __wakeup()
  {
    $this->connectionId = null;
  }

  function disconnect()
  {
    if($this->connectionId)
    {
      mssql_close($this->connectionId);
      $this->connectionId = null;
    }
  }

  function _raiseError($sql = null)
  {
    if(!$this->getConnectionId())
      throw new lmbDbException('Could not connect to host "' . $this->config['host'] . '" and database "' . $this->config['database'] . '"');

    $errstr = mssql_get_last_message();
    $id = 'DB_ERROR';
    $info = array('driver' => 'lmbMssql');
    if(!empty($errstr))
    {
      $info['errorno'] = 0;
      $info['error'] = $errstr;
      $id .= '_MESSAGE';
    }
    if(!is_null($sql))
    {
      $info['sql'] = $sql;
      $id .= '_SQL';
    }
    throw new lmbDbException($errstr . ' SQL: '. $sql, $info);
  }

  function execute($sql)
  {
    if (defined("LIMB_APP_MODE") && LIMB_APP_MODE == "devel")
    {
      Profiler :: instance()->addHit("Query");
      Profiler :: instance()->startIncrementCheckpoint("sql_time");
    }
    $sql = mb_convert_encoding($sql, 'Windows-1251', 'UTF-8');
    $result = mssql_query($sql, $this->getConnectionId());
    if (defined("LIMB_APP_MODE") && LIMB_APP_MODE == "devel")
    {
      error_log($sql."\n\n\n", 3, LIMB_VAR_DIR.'/log/query.log');
      Profiler :: instance()->stopIncrementCheckpoint("sql_time");
    }
    if($result === false)
    {
      $this->_raiseError($sql);
    }
    return $result;
  }
  
  function executeStatement($stmt)
  {
    return (bool) $this->execute($stmt->getSQL());
  }

  function beginTransaction()
  {
    $this->execute('BEGIN TRANSACTION');
    $this->transactionCount++;
  }

  function commitTransaction()
  {
    if ($this->transactionCount > 0)
    {
      $this->execute('COMMIT TRANSACTION');
      $this->transactionCount--;
    }
  }

  function rollbackTransaction()
  {
    if ($this->transactionCount > 0)
    {
      $this->execute('ROLLBACK TRANSACTION');
      $this->transactionCount--;
    }
  }

  function newStatement($sql)
  {
    if(preg_match('/^\s*\(*\s*(\w+).*$/m', $sql, $match))
    {
      $statement = $match[1];
    }
    else
    {
      $statement = $sql;
    }
    switch(strtoupper($statement))
    {
      case 'SELECT':
      return new lmbMssqlQueryStatement($this, $sql);
      case 'INSERT':
      return new lmbMssqlInsertStatement($this, $sql);
      case 'UPDATE':
      case 'DELETE':
      return new lmbMssqlManipulationStatement($this, $sql);
      default:
      return new lmbMssqlStatement($this, $sql);
    }
  }

  function getTypeInfo()
  {
    return new lmbMssqlTypeInfo();
  }


  function getDatabaseInfo()
  {
    return new lmbMssqlDbInfo($this, $this->config['database'], true);
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
    return str_replace("'", "''", $string);
  }

  function getSequenceValue($table, $colname)
  {
    return (int)($this->newStatement("SELECT @@IDENTITY")->getOneValue());
  }
}


