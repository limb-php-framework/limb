<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag pager:LAST
 * @restrict_self_nesting
 * @parent_tag_class WactPagerNavigatorTag
 * @package wact
 * @version $Id: last.tag.php 6386 2007-10-05 14:22:21Z serega $
 */
class WactPagerLastTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $parent = $this->findParentByClass('WactPagerNavigatorTag');
    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->isLast()) {');

    $code->writePhp($this->getDataSourceRefCode() . '["href"] = ' .
                    $parent->getComponentRefCode() . '->getLastPageUri();' . "\n");

    parent :: generateTagContent($code);

    $code->writePhp('}');
  }
}


