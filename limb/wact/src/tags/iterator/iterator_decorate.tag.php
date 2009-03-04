<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
/**
 * @tag iterator:DECORATE
 * @forbid_end_tag
 * @req_const_attributes using
 * @parent_tag_class WactIteratorTransferTag
 * @package wact
 * @version $Id$
 */
class WactIteratorDecorateTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $include_path = $this->getAttribute('include');

    $decorator = $this->getAttribute('using');
    $code->writePhp($this->parent->getComponentRefCode() .
                      '->addDataSetDecorator("' . $decorator . '", "' . $include_path . '");');

    foreach(array_keys($this->attributeNodes) as $key)
    {
      $name = $this->attributeNodes[$key]->getName();

      if($name == 'using')
        continue;

      $code->writePhp($this->parent->getComponentRefCode() .
                      '->addDataSetDecoratorParameter("' . $decorator . '","' . $name . '",');
      $this->attributeNodes[$key]->generateExpression($code);
      $code->writePhp(');');
    }
  }
}


