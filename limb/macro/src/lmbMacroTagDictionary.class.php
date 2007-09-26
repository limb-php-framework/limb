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
    if($this->_isOutputTag($tag))
      return $this->_getOutputTagInfo();
    else
    {
      $tag = strtolower($tag);
      if(isset($this->info[$tag]))
        return $this->info[$tag];
    }
  }

  protected function _isOutputTag($tag)
  {
    return $tag{0} == '$';
  }

  protected function _getOutputTagInfo()
  {
    if($this->output_tag_info)
      return $this->output_tag_info;

    //taking first item
    $this->output_tag_info = reset(lmbMacroTagInfo :: extractFromFile($file = dirname(__FILE__) . '/tags/output.tag.php'));
    $this->output_tag_info->setFile($file);
    return $this->output_tag_info;
  }
}

