<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
* @tag pager:PREV
* @restrict_self_nesting
* @parent_tag_class WactPagerNavigatorTag
*/
class WactPagerPrevTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $parent = $this->findParentByClass('WactPagerNavigatorTag');
    $code->writePhp('if (' . $parent->getComponentRefCode() . '->hasPrev()) {');

    $code->writePhp($this->getDataSource()->getComponentRefCode() . '["href"] = ' .
                    $parent->getComponentRefCode() . '->getPageUri( ' .
                    $parent->getComponentRefCode() . '->getDisplayedPage() - 1 );' . "\n");

    parent :: generateTagContent($code);

    $code->writePhp('}' . "\n");
  }
}

?>