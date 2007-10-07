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

  static function load(lmbMacroConfig $config)
  {
    $dictionary = self :: instance();

    $dirs = $config->getFiltersScanDirectories();
    foreach($dirs as $dir)
    {
      foreach(lmb_glob($dir . '/*.tag.php') as $file)
        $dictionary->registerFromFile($file);
    }

    return $dictionary;
  }

  function register($filter_info)
  {
    $name_to_lower = strtolower($filter_info->getName());

    if(isset($this->info[$name_to_lower]))
      return;

    $this->info[$name_to_lower] = $filter_info;
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

