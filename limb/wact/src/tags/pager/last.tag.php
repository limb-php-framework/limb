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
 * @version $Id: last.tag.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class WactPagerLastTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $parent = $this->findParentByClass('WactPagerNavigatorTag');
    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->isLast()) {');

    $code->writePhp($this->getDataSource()->getComponentRefCode() . '["href"] = ' .
                    $parent->getComponentRefCode() . '->getLastPageUri();' . "\n");

    parent :: generateTagContent($code);

    $code->writePhp('}');
  }
}

?>