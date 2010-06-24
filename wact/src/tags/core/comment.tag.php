<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Prevents a section of the template from being parsed, removing the contents
 * @tag core:COMMENT
 * @forbid_parsing
 * @package wact
 * @version $Id: comment.tag.php 7686 2009-03-04 19:57:12Z korchasa $
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

