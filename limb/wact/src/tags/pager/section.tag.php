<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag pager:SECTION
 * @parent_tag_class WactPagerListTag
 * @restring_self_nesting true
 * @package wact
 * @version $Id: section.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactPagerSectionTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $parent = $this->findParentByClass('WactPagerNavigatorTag');
    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->isDisplayedSection()) {');

    $code->writePhp($this->getDataSourceRefCode() . '["href"] = ' . $parent->getComponentRefCode() . '->getSectionUri();' . "\n");
    $code->writePhp($this->getDataSourceRefCode() . '["number_begin"] = ' . $parent->getComponentRefCode() . '->getSectionBeginPage();' . "\n");
    $code->writePhp($this->getDataSourceRefCode() . '["number_end"] = ' . $parent->getComponentRefCode() . '->getSectionEndPage();' . "\n");

    parent :: generateTagContent($code);

    $code->writePhp('}');
  }
}


