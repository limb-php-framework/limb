<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: section.tag.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
* @tag pager:SECTION
* @parent_tag_class WactPagerListTag
* @restring_self_nesting true
*/
class WactPagerSectionTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $parent = $this->findParentByClass('WactPagerNavigatorTag');
    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->isDisplayedSection()) {');

    $code->writePhp($this->getDataSource()->getComponentRefCode() . '["href"] = ' . $parent->getComponentRefCode() . '->getSectionUri();' . "\n");
    $code->writePhp($this->getDataSource()->getComponentRefCode() . '["number_begin"] = ' . $parent->getComponentRefCode() . '->getSectionBeginPage();' . "\n");
    $code->writePhp($this->getDataSource()->getComponentRefCode() . '["number_end"] = ' . $parent->getComponentRefCode() . '->getSectionEndPage();' . "\n");

    parent :: generateTagContent($code);

    $code->writePhp('}');
  }
}

?>