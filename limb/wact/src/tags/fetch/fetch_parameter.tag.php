<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
/**
 * @tag fetch:param, fetch:params
 * @forbid_end_tag
 * @parent_tag_class WactFetchTag
 * @package wact
 * @version $Id$
 */
class WactFetchParametersTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    foreach(array_keys($this->attributeNodes) as $key)
    {
      $name = $this->attributeNodes[$key]->getName();

      $code->writePhp($this->parent->getComponentRefCode() .
                      '->setAdditionalParam("' . $name . '",');
      $this->attributeNodes[$key]->generateExpression($code);
      $code->writePhp(');');
    }
  }
}


