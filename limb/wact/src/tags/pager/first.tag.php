<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: first.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* @tag pager:FIRST
* @restrict_self_nesting
* @parent_tag_class WactPagerNavigatorTag
*/
class WactPagerFirstTag extends WactCompilerTag
{
  protected $hide_for_current_page;

  function preGenerate($code)
  {
    $this->hide_for_current_page = $this->getBoolAttribute('hide_for_current_page');

    $parent = $this->findParentByClass('WactPagerNavigatorTag');
    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->isFirst()) {');

    parent::preGenerate($code);

    $code->writePhp($this->getDataSource()->getComponentRefCode() . '["href"] = ' . $parent->getComponentRefCode() . '->getFirstPageUri();' . "\n");
    if (!$this->hide_for_current_page)
    {
      $code->writePhp('}');
    }
  }

  function postGenerate($code)
  {
    if (!$this->hide_for_current_page)
    {
      $parent = $this->findParentByClass('WactPagerNavigatorTag');
      $code->writePhp('if (!' . $parent->getComponentRefCode() . '->isFirst()) {');
    }

    parent::postGenerate($code);

    $code->writePhp('}');
  }

  function generateContents($code)
  {
    $parent = $this->findParentByClass('WactPagerNavigatorTag');

    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->isFirst()) {');

    parent :: generateContents($code);

    $code->writePhp('}' . "\n");
  }
}

?>