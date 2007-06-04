<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPgsqlConnection.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/dbal/src/drivers/lmbDbConnection.interface.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlQueryStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlDropStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlInsertStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlManipulationStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlDbInfo.class.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlTypeInfo.class.php');

class lmbPgsqlConnection implements lmbDbConnection
{
  protected $connectionId;
  protected $config;

  function __construct($config)
  {
    $this->config = $config;
  }

  function getType()
  {
    return 'pgsql';
  }

  function getConnectionId()
  {
    if(!isset($this->connectionId))
    {
      $this->connect();
    }
    return $this->connectionId;
  }

  function getHash()
  {
    return crc32(serialize($this->config));
  }

  function connect()
  {

    global $php_errormsg;

    $persistent = $this->config['persistent'];

    $connstr = '';

    if($host = $this->config['host'])
    {
      $connstr = 'host=' . $host;
    }
    if($port = $this->config['port'])
    {
      $connstr .= ' port=' . $port;
    }
    if($database = $this->config['database'])
    {
      $connstr .= ' dbname=\'' . addslashes($database) . '\'';
    }
    if($user = $this->config['user'])
    {
      $connstr .= ' user=\'' . addslashes($user) . '\'';
    }
    if($password = $this->config['password'])
    {
      $connstr .= ' password=\'' . addslashes($password) . '\'';
    }

    if($persistent)
    {
      $conn = @pg_pconnect($connstr);
    }
    else
    {
      $conn = @pg_connect($connstr);
    }

    if(!is_resource($conn))
    {
      $this->_raiseError($php_errormsg);
    }

    if($charset = $this->config['charset'])
    {
      pg_set_client_encoding($conn, $charset);
    }

    $this->connectionId = $conn;
  }

  function __wakeup()
  {
    $this->connectionId = null;
  }

  function disconnect()
  {
    if($this->connectionId)
    {
      @pg_close($this->connectionId);
      $this->connectionId = null;
    }
  }

  function _raiseError($msg)
  {
    throw new lmbException($msg .($this->connectionId ?  ' last pgsql driver error: ' . pg_last_error($this->connectionId) : ''));
  }

  function execute($sql)
  {
    $result = @pg_query($this->getConnectionId(), $sql);
    if($result === false)
    {
      $this->_raiseError($sql);
    }
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
      case 'SHOW':
      case 'DESCRIBE':
      case 'EXPLAIN':
      return new lmbPgsqlQueryStatement($this, $sql);
      case 'DROP':
      return new lmbPgsqlDropStatement($this, $sql);
      case 'INSERT':
      return new lmbPgsqlInsertStatement($this, $sql);
      case 'UPDATE':
      case 'DELETE':
      return new lmbPgsqlManipulationStatement($this, $sql);
      default:
      return new lmbPgsqlStatement($this, $sql);
    }
  }

  function getTypeInfo()
  {
    return new lmbPgsqlTypeInfo();
  }

  function getDatabaseInfo()
  {
    return new lmbPgsqlDbInfo($this, $this->config['database'], true);
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

  function getSequenceValue($table, $colname)
  {
    $seq = "{$table}_{$colname}_seq";
    return (int)$this->newStatement("SELECT currval('$seq')")->getOneValue();
  }
}

?>