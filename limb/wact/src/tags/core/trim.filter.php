<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @filter trim
 * @max_attributes 1
 * @package wact
 * @version $Id: trim.filter.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactTrimFilter extends WactCompilerFilter
{
  function getValue()
  {
   if(isset($this->parameters[0]) && $this->parameters[0]->getValue())
      $characters = $this->parameters[0]->getValue();
    else
      $characters = '';

    if (!$this->isConstant())
      $this->raiseUnresolvedBindingError();

    if($characters)
      return trim($this->base->getValue(), $characters);
    else
      return trim($this->base->getValue());
  }

  function generateExpression($code_writer)
  {
    $code_writer->writePHP('trim(');
    $this->base->generateExpression($code_writer);

    if(isset($this->parameters[0]) && $this->parameters[0]->getValue())
    {
      $code_writer->writePHP(',');
      $this->parameters[0]->generateExpression($code_writer);
    }

    $code_writer->writePHP(')');
  }
}


