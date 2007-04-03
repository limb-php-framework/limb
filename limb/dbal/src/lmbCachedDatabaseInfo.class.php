<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCachedDatabaseInfo.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/classkit/src/lmbSerializable.class.php');
lmb_require('limb/classkit/src/lmbProxy.class.php');
lmb_require('limb/util/src/system/lmbFs.class.php');

@define('LIMB_VAR_DIR', '/tmp');

class lmbCachedDatabaseInfo extends lmbProxy
{
  static protected $cached_db_info = array();

  protected $conn;
  protected $cache_file;

  function __construct($conn)
  {
    $this->conn = $conn;
    $this->cache_file = LIMB_VAR_DIR . '/db_info.' . $conn->getHash() . '.cache';
  }

  function flushCache()
  {
    if(isset(self :: $cached_db_info[$this->conn->getHash()]))
      unset(self :: $cached_db_info[$this->conn->getHash()]);

    if(file_exists($this->cache_file))
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
    if(isset(self :: $cached_db_info[$this->conn->getHash()]))
      return self :: $cached_db_info[$this->conn->getHash()];
  }

  protected function _readFromFileCache()
  {
    if($this->_isFileCachingEnabled() && file_exists($this->cache_file))
    {
      $container = unserialize(file_get_contents($this->cache_file));
      $db_info = $container->getSubject();
      return $db_info;
    }
  }

  protected function _writeToCache($db_info)
  {
    $this->_writeToMemCache($db_info);
    $this->_writeToFileCache($db_info);
  }

  protected function _writeToMemCache($db_info)
  {
    self :: $cached_db_info[$this->conn->getHash()] = $db_info;
  }

  protected function _writeToFileCache($db_info)
  {
    if($this->_isFileCachingEnabled())
      lmbFs :: safeWrite($this->cache_file, serialize(new lmbSerializable($db_info)));
  }

  protected function _isFileCachingEnabled()
  {
    return (!defined('LIMB_CACHE_DB_META_IN_FILE') || constant('LIMB_CACHE_DB_META_IN_FILE'));
  }
}

?>
