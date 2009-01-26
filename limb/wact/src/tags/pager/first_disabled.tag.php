<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @tag pager:first:DISABLED
 * @parent_tag_class WactPagerNavigatorTag
 * @package wact
 * @version $Id: first_disabled.tag.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class WactPagerFirstDisabledTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $code->writePhp('if (' . $this->findParentByClass('WactPagerNavigatorTag')->getComponentRefCode() . '->isFirst()) {');

    parent :: generateTagContent($code);

    $code->writePhp('}');
  }
}


