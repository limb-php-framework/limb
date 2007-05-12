<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: prev_disabled.tag.php 5873 2007-05-12 17:17:45Z serega $
 * @package    wact
 */

/**
* @tag pager:prev:DISABLED
* @parent_tag_class WactPagerNavigatorTag
*/
class WactPagerPrevTagDisabled extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $parent = $this->findParentByClass('WactPagerNavigatorTag');

    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->hasPrev()) {');

    parent :: generateTagContent($code);

    $code->writePhp('}');
  }
}

?>