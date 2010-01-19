<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbSerializable.class.php');
lmb_require('limb/core/src/lmbProxy.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');

/**
 * class lmbDbCachedInfo.
 *
 * @package dbal
 * @version $Id: lmbDbCachedInfo.class.php 8051 2010-01-19 22:39:25Z korchasa $
 */
class lmbDbCachedInfo extends lmbProxy
{
  protected $conn;
  protected $db_info;
  protected $cache_file;

  function __construct($conn, $cache_dir = null)
  {
    $this->conn = $conn;
    if($cache_dir)
      $this->cache_file = $cache_dir . '/db_info.' . $conn->getHash() . '.cache';
  }

  function flushCache()
  {
    if($this->cache_file && file_exists($this->cache_file))
      unlink($this->cache_file);
  }

  protected function _createOriginalObject()
  {
    if($db_info = $this->_readFromCache())
      return $db_info;

    //forcing to load all metainfo
    $db_info = $this->conn->getDatabaseInfo();
    $tables = $db_info->getTableList();
    foreach($tables as $table)
      $db_info->getTable($table)->loadColumns();

    $this->_writeToCache($db_info);
    return $db_info;
  }

  protected function _readFromCache()
  {
    if($db_info = $this->_readFromMemCache())
      return $db_info;

    if($db_info = $this->_readFromFileCache())
    {
      $this->_writeToMemCache($db_info);
      return $db_info;
    }
  }

  protected function _readFromMemCache()
  {
    if(isset($this->db_info))
      return $this->db_info;
  }

  protected function _readFromFileCache()
  {
    if($this->_isFileCachingEnabled())
    {
      $container = unserialize(file_get_contents($this->cache_file));
      $db_info = $container->getSubject();
      return $db_info;
    }
  }

  protected function _isFileCachingEnabled()
  {
    return (lmb_env_get('LIMB_CACHE_DB_META_IN_FILE', false) && file_exists($this->cache_file));
  }

  protected function _writeToCache($db_info)
  {
    $this->_writeToMemCache($db_info);
    $this->_writeToFileCache($db_info);
  }

  protected function _writeToMemCache($db_info)
  {
    $this->db_info = $db_info;
  }

  protected function _writeToFileCache($db_info)
  {
    lmbFs :: safeWrite($this->cache_file, serialize(new lmbSerializable($db_info)));
  }  
}


