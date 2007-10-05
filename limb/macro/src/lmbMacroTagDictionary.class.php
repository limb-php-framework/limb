<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/lmbMacroTagInfo.class.php');

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

  static function load(lmbMacroConfig $config)
  {
    $dictionary = self :: instance();

    $dirs = $config->getTagsScanDirectories();
    foreach($dirs as $dir)
    {
      foreach(lmb_glob($dir . '/*.tag.php') as $file)
        $dictionary->registerFromFile($file);
    }

    return $dictionary;
  }

  function register($taginfo, $file)
  {
    $tag_to_lower = strtolower($taginfo->getTag());

    if(isset($this->info[$tag_to_lower]))
      return;

    $taginfo->setFile($file);
    $this->info[$tag_to_lower] = $taginfo;
  }

  function registerFromFile($file)
  {
    $infos = lmbMacroTagInfo :: extractFromFile($file);
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

