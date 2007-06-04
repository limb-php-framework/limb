<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: total_pages.prop.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
* @property TotalPages
* @tag_class WactPagerNavigatorTag
*/
class WactPagerTotalPagesProperty extends WactCompilerProperty
{
  function generateExpression($code)
  {
    $code->writePHP($this->context->getComponentRefCode());
    $code->writePHP('->getTotalPages()');
  }
}

?>