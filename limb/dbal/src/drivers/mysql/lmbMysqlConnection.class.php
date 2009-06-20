<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/dbal/src/drivers/lmbDbBaseConnection.class.php');
lmb_require(dirname(__FILE__) . '/lmbMysqlDbInfo.class.php');
lmb_require(dirname(__FILE__) . '/lmbMysqlQueryStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbMysqlInsertStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbMysqlManipulationStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbMysqlStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbMysqlTypeInfo.class.php');
lmb_require(dirname(__FILE__) . '/lmbMysqlRecord.class.php');
lmb_require(dirname(__FILE__) . '/lmbMysqlRecordSet.class.php');

/**
 * class lmbMysqlConnection.
 *
 * @package dbal
 * @version $Id: lmbMysqlConnection.class.php 7957 2009-06-20 17:15:17Z pachanga $
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
    if(isset($this->config['pconnect']) && $this->config['pconnect']) 
    {
      $this->connectionId = mysql_pconnect($this->config['host'],
                                          $this->config['user'],
                                          $this->config['password']);
    }
    else
    {
      $this->connectionId = mysql_connect($this->config['host'],
                                          $this->config['user'],
                                          $this->config['password'],
                                          true);
    }

    if($this->connectionId === false)
      $this->_raiseError();

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
    return (bool) $this->execute($stmt->getSQL());
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


