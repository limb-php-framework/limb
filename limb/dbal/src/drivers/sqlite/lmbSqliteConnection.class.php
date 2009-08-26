<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/dbal/src/drivers/lmbDbBaseConnection.class.php');
lmb_require(dirname(__FILE__) . '/lmbSqliteDbInfo.class.php');
lmb_require(dirname(__FILE__) . '/lmbSqliteQueryStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbSqliteInsertStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbSqliteDropStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbSqliteManipulationStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbSqliteStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbSqliteTypeInfo.class.php');
lmb_require(dirname(__FILE__) . '/lmbSqliteRecord.class.php');

/**
 * class lmbSqliteConnection.
 *
 * @package dbal
 * @version $Id$
 */
class lmbSqliteConnection extends lmbDbBaseConnection
{
  protected $connectionId;
  protected $in_transaction = false;

  function getType()
  {
    return 'sqlite';
  }

  function getConnectionId()
  {
    if(!is_resource($this->connectionId))
      $this->connect();

    return $this->connectionId;
  }

  function connect()
  {
    $this->connectionId = sqlite_open($this->config['database'], 0666, $error);

    if($this->connectionId === false)
      $this->_raiseError();
  }

  function __wakeup()
  {
    $this->connectionId = null;
  }

  function disconnect()
  {
    if(is_resource($this->connectionId))
    {
      sqlite_close($this->connectionId);
      $this->connectionId = null;
    }
  }

  function _raiseError($sql = null)
  {
    if(!$this->connectionId)
      throw new lmbDbException('Could not connect to database "' . $this->config['database'] . '"');

    $errno = sqlite_last_error($this->connectionId);

    $info = array('driver' => 'sqlite');
    $info['errorno'] = $errno;
    $info['db'] = $this->config['database'];

    if(!is_null($sql))
      $info['sql'] = $sql;

    throw new lmbDbException(sqlite_error_string($errno) . ' SQL: '. $sql, $info);
  }

  function execute($sql)
  {
    $result = sqlite_query($this->getConnectionId(), $sql);
    if($result === false)
      $this->_raiseError($sql);

    return $result;
  }

  function executeStatement($stmt)
  {
    return (bool)$this->execute($stmt->getSQL());      
  }
  
  function beginTransaction()
  {
    $this->execute('BEGIN');
    $this->in_transaction = true;
  }

  function commitTransaction()
  {
    if($this->in_transaction)
    {
      $this->execute('COMMIT');
      $this->in_transaction = false;
    }
  }

  function rollbackTransaction()
  {
    if($this->in_transaction)
    {
      $this->execute('ROLLBACK');
      $this->in_transaction = false;
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
        return new lmbSqliteQueryStatement($this, $sql);
      case 'INSERT':
        return new lmbSqliteInsertStatement($this, $sql);
      case 'DROP':
        return new lmbSqliteDropStatement($this, $sql);
      case 'UPDATE':
      case 'DELETE':
        return new lmbSqliteManipulationStatement($this, $sql);
      default:
        return new lmbSqliteStatement($this, $sql);
    }
  }

  function getTypeInfo()
  {
    return new lmbSqliteTypeInfo();
  }

  function getDatabaseInfo()
  {
    return new lmbSqliteDbInfo($this, $this->config['database'], true);
  }

  function quoteIdentifier($id)
  {
    if(!$id) return '';

    $pieces = explode('.', $id);
    $quoted = '"' . $pieces[0] . '"';
    if(isset($pieces[1]))
       $quoted .= '."' . $pieces[1] . '"';
    return $quoted;
  }

  function escape($string)
  {
    return sqlite_escape_string($string);
  }

  function getSequenceValue($table, $colname)
  {
    return sqlite_last_insert_rowid($this->connectionId);//???
  }
}


