<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/dbal/src/lmbDBAL.class.php');
lmb_require('limb/dbal/src/lmbDbDSN.class.php');
lmb_require('limb/dbal/src/drivers/lmbDbCachedInfo.class.php');
lmb_require('limb/dbal/src/lmbTableGateway.class.php');

/**
 * class lmbDbTools.
 *
 * @package dbal
 * @version $Id: lmbDbTools.class.php 6873 2008-03-31 04:21:11Z svk $
 */
class lmbDbTools extends lmbAbstractTools
{
  protected $db_configs = array('dsn' => null);
  protected $connections = array('dsn' => null);
  protected $cache_db_info = true;
  protected $db_info = array();
  protected $db_tables = array();
  protected $db_env = 'devel';

  function setDbEnvironment($env)
  {
    $this->db_env = $env;
  }

  function getDbEnvironment()
  {
    return $this->db_env;
  }

  function setDefaultDbDSN($dsn)
  {
    $this->setDbDSNByName('dsn', $dsn);
  }

  function getDefaultDbDSN()
  {
    return $this->getDbDSNByName('dsn');
  }

  function isDefaultDbDsnAvailable()
  {
    try
    {
      $dsn = $this->getDefaultDbDSN();
      if($dsn)
        return true;
    }
    catch(lmbException $e)
    {
      return false;
    }
  }

  function setDbDSNByName($name, $dsn)
  {
    if(is_object($dsn))
      $this->db_configs[$name] = $dsn;
    else
      $this->db_configs[$name] = new lmbDbDSN($dsn);
  }

  function getDbDSNByName($name)
  {
    if(isset($this->db_configs[$name]) && is_object($this->db_configs[$name]))
      return $this->db_configs[$name];

    $conf = $this->toolkit->getConf('db');

    //for BC 'dsn' overrides other db environments
    if($dsn = $conf->get($name))
    {
      $this->setDbDSNByName($name, new lmbDbDSN($dsn));
    }
    else
    {
      $env = $conf->get($this->db_env);
      if(!is_array($env) || !isset($env[$name]))
        throw new lmbException("Could not find database connection settings for environment '{$this->db_env}'");

      $this->setDbDSNByName($name, new lmbDbDSN($env[$name]));
    }

    return $this->db_configs[$name];
  }

  function getDbDSN($env)
  {
    $conf = $this->toolkit->getConf('db');
    $array = $conf->get($env);

    if(!is_array($array) || !isset($array['dsn']))
      throw new lmbException("Could not find database connection settings for environment '{$env}'");

    return new lmbDbDSN($array['dsn']);
  }

  function setDbConnectionByName($name, $conn)
  {
    $this->connections[$name] = $conn;
  }

  function getDbConnectionByName($name)
  {
    if(isset($this->connections[$name]) && is_object($this->connections[$name]))
      return $this->connections[$name];

    if(!is_object($dsn = $this->toolkit->getDbDSNByName($name)))
      throw new lmbException($name . ' database DSN is not valid');

    $this->setDbConnectionByName($name, $this->createDbConnection($dsn));
    return $this->connections[$name];
  }

  function setDefaultDbConnection($conn)
  {
    $this->setDbConnectionByName('dsn', $conn);
  }

  function getDefaultDbConnection()
  {
    return $this->getDbConnectionByName('dsn');
  }

  function createDbConnection($dsn)
  {
    if(!is_object($dsn))
      $dsn = new lmbDbDSN($dsn);

    $driver = $dsn->getDriver();
    $class = 'lmb' . ucfirst($driver) . 'Connection';

    if(!class_exists($class))
    {
      $file = dirname(__FILE__) . '/../drivers/' . $driver . '/' . $class . '.class.php';
      if(!file_exists($file))
        throw new lmbException("Driver '$driver' file not found for DSN '" . $dsn->toString() . "'!");

      lmb_require($file);
    }
    return new $class($dsn);
  }

  function cacheDbInfo($flag = true)
  {
    $this->cache_db_info = $flag;
  }

  function getDbInfo($conn)
  {
    $id = $conn->getHash();

    if(isset($this->db_info[$id]))
      return $this->db_info[$id];

    if($this->cache_db_info && defined('LIMB_VAR_DIR'))
      $db_info = new lmbDbCachedInfo($conn, LIMB_VAR_DIR);
    else
      $db_info = $conn->getDatabaseInfo();

    $this->db_info[$id] = $db_info;
    return $this->db_info[$id];
  }

  function createTableGateway($table_name, $conn = null)
  {
    if(!$conn)
      $cache_key = $table_name;
    else
      $cache_key = $table_name . $conn->getHash();

    if(isset($this->db_tables[$cache_key]))
      return $this->db_tables[$cache_key];

    $db_table = new lmbTableGateway($table_name, $conn);
    $this->db_tables[$cache_key] = $db_table;
    return $db_table;
  }
}
