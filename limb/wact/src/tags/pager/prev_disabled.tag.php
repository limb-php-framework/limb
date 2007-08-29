<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @tag pager:prev:DISABLED
 * @parent_tag_class WactPagerNavigatorTag
 * @package wact
 * @version $Id: prev_disabled.tag.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class WactPagerPrevTagDisabled extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $parent = $this->findParentByClass('WactPagerNavigatorTag');

    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->hasPrev()) {');

    parent :: generateTagContent($code);

    $code->writePhp('}');
  }
}


