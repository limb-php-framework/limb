<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: comment.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
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

  function generateContents($code_writer)
  {
    $this->removeChildren();
  }
}
?>