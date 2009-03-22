<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMacroFilterDictionary.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroFilterDictionary
{
  protected $info = array();
  protected $cache_dir;
  static protected $instance;

  static function instance()
  {
    if(self :: $instance)
      return self :: $instance;

    self :: $instance = new lmbMacroFilterDictionary();
    return self :: $instance;
  }

  function load(lmbMacroConfig $config)
  {
    $this->cache_dir = $config->cache_dir;
    if(!$config->forcescan && $this->_loadCache())
      return;

    //compatibility with PHP 5.1.6
    $filters_scan_dirs = $config->filters_scan_dirs;
    foreach($filters_scan_dirs as $dir)
    {
      foreach(lmb_glob($dir . '/*.filter.php') as $file)
        $this->registerFromFile($file);
    }

    $this->_saveCache();
  }

  protected function _loadCache()
  {
    $cache_file = $this->cache_dir . '/filters.cache';
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
    $cache_file = $this->cache_dir . '/filters.cache';
    lmbFs :: safeWrite($cache_file, serialize($this->info));
  }

  function register($filter_info)
  {
    $names = array(strtolower($filter_info->getName()));

    $aliases = $filter_info->getAliases();
    if(count($aliases))
    {
      $aliases = array_map('strtolower', $aliases);
      $names = array_merge($names, $aliases);
    }

    foreach($names as $filter_name)
    {
      if(isset($this->info[$filter_name]))
        return;

      $this->info[$filter_name] = $filter_info;
    }
  }

  function registerFromFile($file)
  {
    $infos = lmbMacroAnnotationParser :: extractFromFile($file, 'lmbMacroFilterInfo');
    foreach($infos as $info)
      $this->register($info, $file);
  }

  function findFilterInfo($name)
  {
    $name = strtolower($name);
    if(isset($this->info[$name]))
      return $this->info[$name];
  }
}

