<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: more_than_one_page.prop.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* @property HasMoreThanOnePage
* @tag_class WactPagerNavigatorTag
*/
class WactPagerHasMoreThanOnePageProperty extends WactCompilerProperty
{
  function generateExpression($code)
  {
    $code->writePHP($this->context->getComponentRefCode());
    $code->writePHP('->hasMoreThanOnePage()');
  }
}

?>