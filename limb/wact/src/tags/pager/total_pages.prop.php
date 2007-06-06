<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @property TotalPages
 * @tag_class WactPagerNavigatorTag
 * @package wact
 * @version $Id: total_pages.prop.php 5945 2007-06-06 08:31:43Z pachanga $
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