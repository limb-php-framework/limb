<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Prevents a section of the template from being parsed, removing the contents
 * @tag core:COMMENT
 * @forbid_parsing
 */
class WactCoreCommentTag extends WactCompilerTag
{
  function removeChildren()
  {
    foreach(array_keys($this->children) as $key)
      unset($this->children[$key]);
  }

  function generate($code_writer)
  {
    $this->removeChildren();
  }
}
?>