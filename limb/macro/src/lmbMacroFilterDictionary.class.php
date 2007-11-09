<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/lmbMacroFilterInfo.class.php');
lmb_require('limb/macro/src/lmbMacroAnnotationParser.class.php');

/**
 * class lmbMacroFilterDictionary.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroFilterDictionary
{
  protected $info = array();
  protected $output_filter_info;
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
    $dirs = $config->getFiltersScanDirectories();
    foreach($dirs as $dir)
    {
      foreach(lmb_glob($dir . '/*.filter.php') as $file)
        $this->registerFromFile($file);
    }
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

