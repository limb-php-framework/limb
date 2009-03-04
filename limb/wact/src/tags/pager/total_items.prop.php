<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @property TotalItems
 * @tag_class WactPagerNavigatorTag
 * @package wact
 * @version $Id: total_items.prop.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactPagerTotalItemsProperty extends WactCompilerProperty
{
  function generateExpression($code)
  {
    $code->writePHP($this->context->getComponentRefCode());
    $code->writePHP('->getTotalItems()');
  }
}


