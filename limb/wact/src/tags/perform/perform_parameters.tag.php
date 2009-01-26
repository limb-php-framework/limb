<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
/**
 * @tag perform:params
 * @forbid_end_tag
 * @parent_tag_class WactPerformTag
 * @package wact
 * @version $Id$
 */
class WactPerformParametersTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    foreach(array_keys($this->attributeNodes) as $key)
    {
      $name = $this->attributeNodes[$key]->getName();

      $code->writePhp($this->parent->getComponentRefCode() .
                      '->addParam(');
      $this->attributeNodes[$key]->generateExpression($code);
      $code->writePhp(');' . "\n");
    }
  }
}


