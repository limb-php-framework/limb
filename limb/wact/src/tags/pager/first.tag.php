<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: first.tag.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
* @tag pager:FIRST
* @restrict_self_nesting
* @parent_tag_class WactPagerNavigatorTag
*/
class WactPagerFirstTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $parent = $this->findParentByClass('WactPagerNavigatorTag');
    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->isFirst()) {');

    $code->writePhp($this->getDataSource()->getComponentRefCode() . '["href"] = ' .
                    $parent->getComponentRefCode() . '->getFirstPageUri();' . "\n");

    parent :: generateTagContent($code);

    $code->writePhp('}' . "\n");
  }
}

?>