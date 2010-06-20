<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/dbal/src/drivers/lmbDbBaseConnection.class.php');
lmb_require('limb/dbal/src/drivers/mysql/lmbMysqlDbInfo.class.php');
lmb_require('limb/dbal/src/drivers/mysql/lmbMysqlQueryStatement.class.php');
lmb_require('limb/dbal/src/drivers/mysql/lmbMysqlInsertStatement.class.php');
lmb_require('limb/dbal/src/drivers/mysql/lmbMysqlManipulationStatement.class.php');
lmb_require('limb/dbal/src/drivers/mysql/lmbMysqlStatement.class.php');
lmb_require('limb/dbal/src/drivers/mysql/lmbMysqlTypeInfo.class.php');
lmb_require('limb/dbal/src/drivers/mysql/lmbMysqlRecord.class.php');
lmb_require('limb/dbal/src/drivers/mysql/lmbMysqlRecordSet.class.php');

/**
 * class lmbMysqlConnection.
 *
 * @package dbal
 * @version $Id: lmbMysqlConnection.class.php 8180 2010-04-26 08:45:46Z korchasa $
 */
class lmbMysqlConnection extends lmbDbBaseConnection
{
  protected $connectionId;

  function getType()
  {
    return 'mysql';
  }

  function getConnectionId()
  {
    if(!isset($this->connectionId))
      $this->connect();
    return $this->connectionId;
  }

  function connect()
  {
    $port = !empty($this->config['port']) ? (int) $this->config['port'] : null;
    $socket = !empty($this->config['socket']) ? $this->config['socket'] : null;
    $host = $this->config['host'];

    if ($socket) {
      $host .= ':' . $socket;
    } elseif($port) {
      $host .= ':' . $port;
    }

    if(isset($this->config['pconnect']) && $this->config['pconnect'])
    {
      $this->connectionId = mysql_pconnect($host,
                                          $this->config['user'],
                                          $this->config['password']);
    }
    else
    {
      $this->connectionId = mysql_connect($host,
                                          $this->config['user'],
                                          $this->config['password'],
                                          $force_new_link = true);
    }

    if($this->connectionId === false)
      $this->_raiseError();

    if(!empty($this->config['database']))
      if(mysql_select_db($this->config['database'], $this->connectionId) === false)
        $this->_raiseError();

    if(isset($this->config['charset']) && $charset = $this->config['charset'])
      mysql_query("SET NAMES '$charset'",  $this->connectionId);
  }

  function __wakeup()
  {
    $this->connectionId = null;
  }

  function disconnect()
  {
    if($this->connectionId)
    {
      mysql_close($this->connectionId);
      $this->connectionId = null;
    }
  }

  function _raiseError($sql = null)
  {
    if(!$this->getConnectionId())
      throw new lmbDbException('Could not connect to host "' . $this->config['host'] . '" and database "' . $this->config['database'] . '"');

    $errno = mysql_errno($this->getConnectionId());
    $id = 'DB_ERROR';
    $info = array('driver' => 'lmbMysql');
    if($errno != 0)
    {
      $info['errorno'] = $errno;
      $info['error'] = mysql_error($this->getConnectionId());
      $id .= '_MESSAGE';
    }
    if(!is_null($sql))
    {
      $info['sql'] = $sql;
      $id .= '_SQL';
    }
    throw new lmbDbException(mysql_error($this->getConnectionId()) . ' SQL: '. $sql, $info);
  }

  function execute($sql)
  {
    $result = mysql_query($sql, $this->getConnectionId());
    if($result === false)
      $this->_raiseError($sql);
    return $result;
  }

  function executeStatement($stmt)
  {
    $result = $this->execute($stmt->getSQL());
    if($stmt instanceof lmbDbManipulationStatement)
      return $stmt->getAffectedRowCount();
    else
      return $result;
  }

  function beginTransaction()
  {
    $this->execute('BEGIN');
  }

  function commitTransaction()
  {
    $this->execute('COMMIT');
  }

  function rollbackTransaction()
  {
    $this->execute('ROLLBACK');
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
        return new lmbMysqlQueryStatement($this, $sql);
      case 'INSERT':
        return new lmbMysqlInsertStatement($this, $sql);
      case 'UPDATE':
      case 'DELETE':
        return new lmbMysqlManipulationStatement($this, $sql);
      default:
        return new lmbMysqlStatement($this, $sql);
    }
  }

  function getTypeInfo()
  {
    return new lmbMysqlTypeInfo();
  }

  function getDatabaseInfo()
  {
    return new lmbMysqlDbInfo($this, $this->config['database'], true);
  }

  function quoteIdentifier($id)
  {
    if(!$id)
      return '';

    $pieces = explode('.', $id);
    $quoted = '`' . $pieces[0] . '`';
    if(isset($pieces[1]))
       $quoted .= '.`' . $pieces[1] . '`';
    return $quoted;
  }

  function escape($string)
  {
    return mysql_escape_string($string);
  }

  function getSequenceValue($table, $colname)
  {
    //TODO: is it a good idea to use mysql_insert_id ?
    return mysql_insert_id($this->connectionId);
  }
}


