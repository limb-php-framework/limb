<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCachingFileLocator.class.php 4996 2007-02-08 15:36:18Z pachanga $
 * @package    file_schema
 */
lmb_require('limb/file_schema/src/lmbFileLocatorDecorator.class.php');
lmb_require('limb/util/src/system/lmbFs.class.php');

class lmbCachingFileLocator extends lmbFileLocatorDecorator
{
  protected $_cached_paths = array();
  protected $_changed = false;
  protected $_cache_name;

  function __construct($locator, $_cache_name = 'generic')
  {
    $this->_cache_name = $_cache_name;

    parent :: __construct($locator);

    $this->_loadCache();
  }

  function __destruct()
  {
    $this->saveCache();
  }

  function getCacheFile()
  {
    $cache_file = LIMB_VAR_DIR . '/locators/' . $this->_cache_name  . '_locator.cache';
    lmbFs :: mkdir(LIMB_VAR_DIR . '/locators/');

    return $cache_file;
  }

  function flushCache()
  {
    $this->_cached_paths = array();
    $cache_file = $this->getCacheFile();

    if(file_exists($cache_file))
      unlink($cache_file);
  }

  protected function _loadCache()
  {
    $cache_file = $this->getCacheFile();
    if(!file_exists($cache_file))
      return;

    $this->_cached_paths = unserialize(file_get_contents($cache_file));
  }

  function saveCache()
  {
    if(!$this->_changed)
      return;

    $content = serialize($this->_cached_paths);
    lmbFs :: safeWrite($this->getCacheFile(), $content);
  }

  function locate($alias, $params = array())
  {
    if($params)
      $hash = $alias . '_' . md5(serialize($params));
    else
      $hash = $alias;

    if(isset($this->_cached_paths[$hash]))
      return $this->_cached_paths[$hash];

    $this->_changed = true;
    $this->_cached_paths[$hash] = $this->locator->locate($alias, $params);

    return $this->_cached_paths[$hash];
  }
}

?>