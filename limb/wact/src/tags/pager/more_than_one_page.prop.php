<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: more_than_one_page.prop.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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