<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @tag core:REPEAT
 * @req_attributes value
 * @package wact
 * @version $Id: repeat.tag.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class WactCoreRepeatTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $counter = '$' . $code->getTempVariable();

    $value_attr = $this->attributeNodes['value'];

    if($value_attr->isConstant())
    {
      $value = $value_attr->getValue();
      $code->writePhp('for(' . $counter . '=0;' . $counter . ' < ' . $value . '; ' . $counter . '++){');
    }
    else
    {
      $value = '$' . $code->getTempVariable();
      $code->writePHP($value . ' = ');
      $value_attr->generateExpression($code);
      $code->writePHP(';');

      $code->writePhp('for(' . $counter . '=0;' . $counter . ' < ' . $value . '; ' . $counter . '++){');
    }

    parent :: generateTagContent($code);

    $code->writePhp('}');
  }
}


