<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: next_disabled.tag.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
* @tag pager:next:DISABLED
* @parent_tag_class WactPagerNavigatorTag
*/
class WactPagerNextDisabledTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $code->writePhp('if (!' . $this->findParentByClass('WactPagerNavigatorTag')->getComponentRefCode() . '->hasNext()) {');

    parent :: generateTagContent($code);

    $code->writePhp('}');
  }
}

?>