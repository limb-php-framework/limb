<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMacroTagDictionary.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTagDictionary
{
  protected $info = array();
  protected $cache_dir;
  static protected $instance;

  static function instance()
  {
    if(self :: $instance)
      return self :: $instance;

    self :: $instance = new lmbMacroTagDictionary();
    return self :: $instance;
  }

  function load(lmbMacroConfig $config)
  {
    $this->cache_dir = $config->cache_dir;
    if(!$config->forcescan && $this->_loadCache())
      return;

    $real_scan_dirs = array();

    //compatibility with PHP 5.1.6
    $tag_scan_dirs = $config->tags_scan_dirs;
    foreach($tag_scan_dirs as $dir)
    {
      foreach($this->_getThisAndImmediateDirectories($dir) as $item)
        $real_scan_dirs[] = $item;
    }
    foreach($real_scan_dirs as $scan_dir)
    {
      foreach(lmb_glob($scan_dir . '/*.tag.php') as $file)
        $this->registerFromFile($file);
    }

    $this->_saveCache();
  }

  function _getThisAndImmediateDirectories($dir)
  {
    $dirs = array();
    foreach(lmb_glob("$dir/*") as $item) {
      if($item != '.' && $item != '..' && is_dir($item))
        $dirs[] = $item;
    }

    $dirs[] = $dir;

    return $dirs;
  }

  protected function _loadCache()
  {
    $cache_file = $this->cache_dir . '/tags.cache';
    if(!file_exists($cache_file))
      return false;

    $info = @unserialize(file_get_contents($cache_file));
    if($info === false || !is_array($info))
      return false;

    $this->info = $info;

    return true;
  }

  protected function _saveCache()
  {
    lmb_require('limb/fs/src/lmbFs.class.php');
    $cache_file = $this->cache_dir . '/tags.cache';
    lmbFs :: safeWrite($cache_file, serialize($this->info));
  }

  function register($tag_info)
  {
    $names = array(strtolower($tag_info->getTag()));

    $aliases = $tag_info->getAliases();
    if(count($aliases))
    {
      $aliases = array_map('strtolower', $aliases);
      $names = array_merge($names, $aliases);
    }

    foreach($names as $tag_name)
    {
      if(isset($this->info[$tag_name]))
        return;

      $this->info[$tag_name] = $tag_info;
    }
  }

  function registerFromFile($file)
  {
    $infos = lmbMacroAnnotationParser :: extractFromFile($file, 'lmbMacroTagInfo');
    foreach($infos as $info)
      $this->register($info, $file);
  }

  function findTagInfo($tag)
  {
    $tag = strtolower($tag);
    if(isset($this->info[$tag]))
      return $this->info[$tag];
  }
}


