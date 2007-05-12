<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: last.tag.php 5873 2007-05-12 17:17:45Z serega $
 * @package    wact
 */

/**
* @tag pager:LAST
* @restrict_self_nesting
* @parent_tag_class WactPagerNavigatorTag
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