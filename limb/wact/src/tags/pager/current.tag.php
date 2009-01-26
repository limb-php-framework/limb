<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag pager:CURRENT
 * @restrict_self_nesting
 * @parent_tag_class WactPagerListTag
 * @package wact
 * @version $Id: current.tag.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class WactPagerDisplayedTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $parent = $this->findParentByClass('WactPagerNavigatorTag');

    $code->writePhp('if (' . $parent->getComponentRefCode() . '->isDisplayedPage()) {');

    $code->writePhp($this->getDatasourceRefCode() . '["href"] = ' . $parent->getComponentRefCode() . '->getDisplayedPageUri();' . "\n");
    $code->writePhp($this->getDatasourceRefCode() . '["number"] = ' . $parent->getComponentRefCode() . '->getPage();' . "\n");

    parent :: generateTagContent($code);

    $code->writePhp('}');
  }
}


