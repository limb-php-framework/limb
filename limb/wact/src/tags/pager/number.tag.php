<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag pager:NUMBER
 * @restrict_self_nesting
 * @parent_tag_class WactPagerListTag
 * @package wact
 * @version $Id: number.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactPagerNumberTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $parent = $this->findParentByClass('WactPagerNavigatorTag');
    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->isDisplayedPage()) {');

    $code->writePhp($this->getDataSourceRefCode() . '["href"] = ' . $parent->getComponentRefCode() . '->getPageUri();' . "\n");
    $code->writePhp($this->getDataSourceRefCode() . '["number"] = ' . $parent->getComponentRefCode() . '->getPage();' . "\n");

    parent :: generateTagContent($code);

    $code->writePhp('}');
  }
}


