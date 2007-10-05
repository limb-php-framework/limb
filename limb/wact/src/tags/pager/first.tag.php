<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag pager:FIRST
 * @restrict_self_nesting
 * @parent_tag_class WactPagerNavigatorTag
 * @package wact
 * @version $Id: first.tag.php 6386 2007-10-05 14:22:21Z serega $
 */
class WactPagerFirstTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $parent = $this->findParentByClass('WactPagerNavigatorTag');
    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->isFirst()) {');

    $code->writePhp($this->getDataSourceRefCode() . '["href"] = ' .
                    $parent->getComponentRefCode() . '->getFirstPageUri();' . "\n");

    parent :: generateTagContent($code);

    $code->writePhp('}' . "\n");
  }
}


