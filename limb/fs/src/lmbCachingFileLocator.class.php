<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/fs/src/lmbFileLocatorDecorator.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');

/**
 * class lmbCachingFileLocator.
 *
 * @package fs
 * @version $Id$
 */
class lmbCachingFileLocator extends lmbFileLocatorDecorator
{
  protected $_cached_paths = array();
  protected $_changed = false;
  protected $_cache_name;

  function __construct($locator, $cache_dir, $cache_name = 'default')
  {
    $this->_cache_name = $cache_name;
    $this->_cache_dir = $cache_dir;

    parent :: __construct($locator);

    $this->_loadCache();
  }

  function __destruct()
  {
    $this->saveCache();
  }

  function getCacheFile()
  {
    lmbFs :: mkdir($this->_cache_dir);
    $cache_file = $this->_cache_dir . '/' . $this->_cache_name  . '_locator.cache';
    return $cache_file;
  }

  function flushCache()
  {
    $this->_cached_paths = array();
    $cache_file = $this->getCacheFile();

    if(file_exists($cache_file))
      unlink($cache_file);
  }

  function _loadCache()
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

    $this->_cached_paths[$hash] = $this->locator->locate($alias, $params);
    $this->_changed = true;

    return $this->_cached_paths[$hash];
  }
}


