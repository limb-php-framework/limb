<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
 * @version $Id: lmbDbTools.class.php 8007 2009-12-15 20:31:12Z idler $
 */
class lmbDbTools extends lmbAbstractTools
{
  protected $dsnes_available = array('dsn' => null);
  protected $dsnes_active = array();
  protected $dsnes_names = array('dsn' => null);
  protected $db_info = array();
  protected $db_tables = array();
  protected $is_db_info_cache_enabled;
  protected $db_env = 'devel';

  function setDbEnvironment($env)
  {
    $this->dsnes_available = $env;
  }

  function getDbEnvironment()
  {
    return $this->dsnes_available;
  }

  function setDefaultDbDSN($dsn)
  {
    $this->setDbDSNByName('dsn', $dsn);
  }

  function getDefaultDbDSN()
  {
    return $this->getDbDSNByName('dsn');
  }

  function isDefaultDbDSNAvailable()
  {
    try
    {
      $dsn = $this->getDefaultDbDSN();
      if($dsn)
        return true;
      else
        return false;
    }
    catch(lmbException $e)
    {
      return false;
    }
  }
  
  protected function _getDbDsnHash($dsn)
  {
    $dsn = self :: castToDsnObject($dsn);
    return md5($dsn->toString());
  }
  
  static function castToDsnObject($dsn)
  {
    if(is_object($dsn) && ($dsn instanceof lmbDbDSN))
      return $dsn;
    if(is_object($dsn) && ($dsn instanceof lmbSet))
      return new lmbDbDSN($dsn->export());
    return new lmbDbDSN($dsn);
  }
  
  protected function _tryLoadDsnFromEnvironment($conf, $name)
  {    
    $env = $conf->get($this->db_env);
    if(!is_array($env) || !isset($env[$name]))
      throw new lmbException("Could not find database connection settings for environment '{$this->db_env}'");
    return $env[$name];
  }
  
  protected function _loadDbDsnFromConfig($name)
  {
    $conf = $this->toolkit->getConf('db');

    //for BC 'dsn' overrides other db environments
    $dsn = ($conf->has($name))
      ? $conf->get($name)
      : $this->_tryLoadDsnFromEnvironment($conf, $name);

    $dsn = self :: castToDsnObject($dsn);
    $this->setDbDSNByName($name, $dsn);
    
    return $dsn->toString();
  }

  function setDbDSNByName($name, $dsn)
  {
    $dsn = self :: castToDsnObject($dsn);
    
    $this->dsnes_names[$name] = $this->_getDbDsnHash($dsn);
    $this->dsnes_available[$this->dsnes_names[$name]] = $dsn;
  }

  function getDbDSNByName($name)
  {  
    if(!isset($this->dsnes_names[$name]))
    {
      $this->_loadDbDsnFromConfig($name);
    }

    if(isset($this->dsnes_available[$this->dsnes_names[$name]]) && is_object($this->dsnes_available[$this->dsnes_names[$name]]))
      return $this->dsnes_available[$this->dsnes_names[$name]];

    return $this->dsnes_available[$this->dsnes_names[$name]];
  }

  function getDbDSN($env)
  {
    $conf = $this->toolkit->getConf('db');
    $array = $conf->get($env);

    if(!is_array($array) || !isset($array['dsn']))
      throw new lmbException("Could not find database connection settings for environment '{$env}'");

    return new lmbDbDSN($array['dsn']);
  }
  
  function getDbConnectionByDsn($dsn)
  {
    $dsn_hash = $this->_getDbDsnHash($dsn);
    
    if(isset($this->dsnes_active[$dsn_hash]) && is_object($this->dsnes_active[$dsn_hash]))
      return $this->dsnes_active[$dsn_hash];

    $this->setDbConnectionByDsn($dsn, $this->createDbConnection($dsn));
    return $this->dsnes_active[$dsn_hash];
  }
  
  function setDbConnectionByDsn($dsn, $conn)
  {
    $this->dsnes_active[$this->_getDbDsnHash($dsn)] = $conn;
  }
  
  function setDbConnectionByName($name, $conn)
  {
    if(!is_object($dsn = $this->toolkit->getDbDSNByName($name)))
      throw new lmbException($name . ' database DSN is not valid');
    
    return $this->setDbConnectionByDsn($dsn, $conn);
  }

  function getDbConnectionByName($name)
  {
    if(!is_object($dsn = $this->toolkit->getDbDSNByName($name)))
      throw new lmbException($name . ' database DSN is not valid');
    
    return $this->getDbConnectionByDsn($dsn);
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
    $dsn = self :: castToDsnObject($dsn);

    $driver = $dsn->getDriver();
    $class = 'lmb' . ucfirst($driver) . 'Connection';

    if(!class_exists($class))
    {
      $file = dirname(__FILE__) . '/../drivers/' . $driver . '/' . $class . '.class.php';
      if(!file_exists($file))
        throw new lmbException("Driver '$driver' file not found for DSN '" . $dsn->toString() . "'!");

      lmb_require($file);
    }
    return new $class($dsn, $dsn->toString());
  }
  
  protected function _isDbInfoCacheEnabled()
  {
    if(is_null($this->is_db_info_cache_enabled))
    {
      $this->is_db_info_cache_enabled = false;      
      
      if(lmb_env_has('LIMB_CACHE_DB_META_IN_FILE'))
        $this->is_db_info_cache_enabled = lmb_env_get('LIMB_CACHE_DB_META_IN_FILE');
      else if($this->toolkit->getConf('db')->has('cache_db_info'))
        $this->is_db_info_cache_enabled = $this->toolkit->getConf('db')->get('cache_db_info');
    }
    
    return $this->is_db_info_cache_enabled;
  }

  function getDbInfo($conn)
  {
    $id = $conn->getHash();

    if(isset($this->db_info[$id]))
      return $this->db_info[$id];    

    if($this->_isDbInfoCacheEnabled())
      $db_info = new lmbDbCachedInfo($conn, lmb_env_get('LIMB_VAR_DIR'));
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
