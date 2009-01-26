<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
/**
 * @tag find:params, find:param
 * @forbid_end_tag
 * @parent_tag_class lmbActiveRecordFetchTag
 * @package web_app
 * @version $Id$
 */
class lmbFindParametersTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $code->writePhp($this->parent->getComponentRefCode() .'->resetFindParams();'. "\n");

    foreach(array_keys($this->attributeNodes) as $key)
    {
      $name = $this->attributeNodes[$key]->getName();

      $code->writePhp($this->parent->getComponentRefCode() .
                      '->addFindParam(');
      $this->attributeNodes[$key]->generateExpression($code);
      $code->writePhp(');' . "\n");
    }
  }
}


