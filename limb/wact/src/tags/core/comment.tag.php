<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: comment.tag.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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