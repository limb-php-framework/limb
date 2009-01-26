<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @property HasMoreThanOnePage
 * @tag_class WactPagerNavigatorTag
 * @package wact
 * @version $Id: more_than_one_page.prop.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class WactPagerHasMoreThanOnePageProperty extends WactCompilerProperty
{
  function generateExpression($code)
  {
    $code->writePHP($this->context->getComponentRefCode());
    $code->writePHP('->hasMoreThanOnePage()');
  }
}


