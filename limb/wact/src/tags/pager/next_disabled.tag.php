<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @tag pager:next:DISABLED
 * @parent_tag_class WactPagerNavigatorTag
 * @package wact
 * @version $Id: next_disabled.tag.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class WactPagerNextDisabledTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $code->writePhp('if (!' . $this->findParentByClass('WactPagerNavigatorTag')->getComponentRefCode() . '->hasNext()) {');

    parent :: generateTagContent($code);

    $code->writePhp('}');
  }
}

?>