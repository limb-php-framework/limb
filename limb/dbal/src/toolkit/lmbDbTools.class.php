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
 * @version $Id: lmbDbTools.class.php 6221 2007-08-07 07:24:35Z pachanga $
 */
class lmbDbTools extends lmbAbstractTools
{
  protected $default_connection;
  protected $default_db_config;
  protected $cache_db_info = true;
  protected $db_info = array();
  protected $db_tables = array();
  protected $db_env = 'devel';

  function setDbEnvironment($env)
  {
    $this->db_env = $env;
    $this->default_db_config = null;
    $this->default_connection = null;
  }

  function getDbEnvironment()
  {
    return $this->db_env;
  }

  function setDefaultDbDSN($conf)
  {
    if(is_object($conf))
      $this->default_db_config = $conf;
    else
      $this->default_db_config = new lmbDbDSN($conf);
  }

  function getDefaultDbDSN()
  {
    if(is_object($this->default_db_config))
      return $this->default_db_config;

    $conf = $this->toolkit->getConf('db');

    //for BC 'dsn' overrides other db environments
    if($dsn = $conf->get('dsn'))
    {
      $this->default_db_config = new lmbDbDSN($dsn);
    }
    else
    {
      $env = $conf->get($this->db_env);
      if(!is_array($env) || !isset($env['dsn']))
        throw new lmbException("Could not find database connection settings for environment '{$this->db_env}'");

      $this->default_db_config = new lmbDbDSN($env['dsn']);
    }

    return $this->default_db_config;
  }

  function getDbDSN($env)
  {
    $conf = $this->toolkit->getConf('db');
    $array = $conf->get($env);

    if(!is_array($array) || !isset($array['dsn']))
      throw new lmbException("Could not find database connection settings for environment '{$env}'");

    return new lmbDbDSN($array['dsn']);
  }

  function getDefaultDbConnection()
  {
    if(is_object($this->default_connection))
      return $this->default_connection;

    if(!is_object($dsn = $this->toolkit->getDefaultDbDSN()))
      throw new lmbException('Default database DSN is not valid');

    $this->default_connection = $this->toolkit->createDbConnection($dsn);
    return $this->default_connection;
  }

  function createDbConnection($dsn)
  {
    $driver = $dsn->getDriver();
    $class = 'lmb' . ucfirst($driver) . 'Connection';
    $file = dirname(__FILE__) . '/../drivers/' . $driver . '/' . $class . '.class.php';
    if(!file_exists($file))
      throw new lmbException("Driver '$driver' file not found for DSN '" . $dsn->toString() . "'!");

    lmb_require($file);
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

  function setDefaultDbConnection($conn)
  {
    $this->default_connection = $conn;
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

