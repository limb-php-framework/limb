<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: next_disabled.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* @tag pager:next:DISABLED
* @parent_tag_class WactPagerNavigatorTag
*/
class WactPagerNextDisabledTag extends WactCompilerTag
{
  function preGenerate($code)
  {
    $code->writePhp('if (!' . $this->findParentByClass('WactPagerNavigatorTag')->getComponentRefCode() . '->hasNext()) {');

    parent :: preGenerate($code);

    $code->writePhp('}');
  }

  function postGenerate($code)
  {
    $code->writePhp('if (!' . $this->findParentByClass('WactPagerNavigatorTag')->getComponentRefCode() . '->hasNext()) {');

    parent::postGenerate($code);

    $code->writePhp('}');
  }

  function generateContents($code)
  {
    $code->writePhp('if (!' . $this->findParentByClass('WactPagerNavigatorTag')->getComponentRefCode() . '->hasNext()) {');

    parent :: generateContents($code);

    $code->writePhp('}');
  }
}

?>