<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: begin_item_number.prop.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* @property BeginItemNumber
* @tag_class WactPagerNavigatorTag
*/
class WactPagerBeginItemNumberProperty extends WactCompilerProperty
{
  function generateExpression($code)
  {
    $code->writePHP($this->context->getComponentRefCode());
    $code->writePHP('->getDisplayedPageBeginItem()');
  }
}

?>