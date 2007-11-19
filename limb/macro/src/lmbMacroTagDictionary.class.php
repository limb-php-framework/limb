<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/lmbMacroTagInfo.class.php');
lmb_require('limb/macro/src/lmbMacroAnnotationParser.class.php');

/**
 * class lmbMacroTagDictionary.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTagDictionary
{
  protected $info = array();
  protected $output_tag_info;
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
    $config_scan_dirs = $config->getTagsScanDirectories();
    $real_scan_dirs = array();
    
    foreach($config_scan_dirs as $dir)
    {
      foreach($this->_getThisAndImmediateDirectories($dir) as $item)
        $real_scan_dirs[] = $item;
    }
    
    foreach($real_scan_dirs as $scan_dir)
    {
      foreach(lmb_glob($scan_dir . '/*.tag.php') as $file)
        $this->registerFromFile($file);
    }
  }

  function _getThisAndImmediateDirectories($dir)
  {
    $dirs = array();
    foreach(lmb_glob("$dir/*") as $item) {
      if($item{0} != '.' && is_dir($item))
        $dirs[] = $item;
    }

    $dirs[] = $dir;

    return $dirs;
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

