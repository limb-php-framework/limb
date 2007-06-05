<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/lmbMacroTagInfo.class.php');

class lmbMacroTagDictionary
{
  protected $info = array();

  function register($taginfo, $file)
  {
    $tag_to_lower = strtolower($taginfo->getTag());

    if(isset($this->info[$tag_to_lower]))
      return;

    $taginfo->setFile($file);
    $this->info[$tag_to_lower] = $taginfo;
  }

  function findTagInfo($tag)
  {
    $tag = strtolower($tag);
    if(isset($this->info[$tag]))
      return $this->info[$tag];
  }
}
?>