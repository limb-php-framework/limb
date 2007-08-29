<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @property TotalItems
 * @tag_class WactPagerNavigatorTag
 * @package wact
 * @version $Id: total_items.prop.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class WactPagerTotalItemsProperty extends WactCompilerProperty
{
  function generateExpression($code)
  {
    $code->writePHP($this->context->getComponentRefCode());
    $code->writePHP('->getTotalItems()');
  }
}


