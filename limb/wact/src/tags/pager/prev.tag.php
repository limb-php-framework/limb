<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: prev.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* @tag pager:PREV
* @restrict_self_nesting
* @parent_tag_class WactPagerNavigatorTag
*/
class WactPagerPrevTag extends WactCompilerTag
{
  protected $hide_for_current_page;

  function preGenerate($code)
  {
    $this->hide_for_current_page = $this->getBoolAttribute('hide_for_current_page');

    $parent = $this->findParentByClass('WactPagerNavigatorTag');
    $code->writePhp('if (' . $parent->getComponentRefCode() . '->hasPrev()) {');

    parent :: preGenerate($code);

    $code->writePhp($this->getDataSource()->getComponentRefCode() . '["href"] = ' .
                    $parent->getComponentRefCode() . '->getPageUri( ' .
                    $parent->getComponentRefCode() . '->getDisplayedPage() - 1 );' . "\n");

    if (!$this->hide_for_current_page)
    {
      $code->writePhp('}' . "\n");
    }
  }

  function postGenerate($code)
  {
    if (!$this->hide_for_current_page)
    {
      $parent = $this->findParentByClass('WactPagerNavigatorTag');
      $code->writePhp('if (' . $parent->getComponentRefCode() . '->hasPrev()) {' . "\n");
    }

    parent::postGenerate($code);

    $code->writePhp('}' . "\n");
  }

  function generateContents($code)
  {
    $parent = $this->findParentByClass('WactPagerNavigatorTag');

    $code->writePhp('if (' . $parent->getComponentRefCode() . '->hasPrev()) {' . "\n");

    parent :: generateContents($code);

    $code->writePhp('}' . "\n");
  }
}

?>