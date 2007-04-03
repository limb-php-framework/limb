<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: section.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* @tag pager:SECTION
* @parent_tag_class WactPagerListTag
* @restring_self_nesting true
*/
class WactPagerSectionTag extends WactCompilerTag
{
  function generateContents($code)
  {
    $parent = $this->findParentByClass('WactPagerNavigatorTag');
    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->isDisplayedSection()) {');

    $code->writePhp($this->getDataSource()->getComponentRefCode() . '["href"] = ' . $parent->getComponentRefCode() . '->getSectionUri();' . "\n");
    $code->writePhp($this->getDataSource()->getComponentRefCode() . '["number_begin"] = ' . $parent->getComponentRefCode() . '->getSectionBeginPage();' . "\n");
    $code->writePhp($this->getDataSource()->getComponentRefCode() . '["number_end"] = ' . $parent->getComponentRefCode() . '->getSectionEndPage();' . "\n");

    parent :: generateContents($code);

    $code->writePhp('}');
  }
}

?>