<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: number.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* @tag pager:NUMBER
* @restrict_self_nesting
* @parent_tag_class WactPagerListTag
*/
class WactPagerNumberTag extends WactCompilerTag
{
  function generateContents($code)
  {
    $parent = $this->findParentByClass('WactPagerNavigatorTag');
    $code->writePhp('if (!' . $parent->getComponentRefCode() . '->isDisplayedPage()) {');

    $code->writePhp($this->getDataSource()->getComponentRefCode() . '["href"] = ' . $parent->getComponentRefCode() . '->getPageUri();' . "\n");
    $code->writePhp($this->getDataSource()->getComponentRefCode() . '["number"] = ' . $parent->getComponentRefCode() . '->getPage();' . "\n");

    parent :: generateContents($code);

    $code->writePhp('}');
  }
}

?>