<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: prev_disabled.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* @tag pager:prev:DISABLED
* @parent_tag_class WactPagerNavigatorTag
*/
class WactPagerPrevTagDisabled extends WactCompilerTag
{
  function preGenerate($code)
  {
    $parent = $this->findParentByClass('WactPagerNavigatorTag');

    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->hasPrev()) {');

    parent :: preGenerate($code);

    $code->writePhp('}');
  }

  function postGenerate($code)
  {
    $parent = $this->findParentByClass('WactPagerNavigatorTag');
    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->hasPrev()) {');

    parent::postGenerate($code);

    $code->writePhp('}');
  }

  function generateContents($code)
  {
    $parent = $this->findParentByClass('WactPagerNavigatorTag');

    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->hasPrev()) {');

    parent :: generateContents($code);

    $code->writePhp('}');
  }
}

?>