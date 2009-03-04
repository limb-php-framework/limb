<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @filter date
 * @max_attributes 1
 * @package wact
 * @version $Id: date.filter.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactDateFilter extends WactCompilerFilter
{
  function getValue()
  {
    if ($this->isConstant())
    {
     $value = $this->base->getValue();
     $exp = $this->parameters[0]->getValue();
     return date($exp, $value);
    } else {
     $this->raiseUnresolvedBindingError();
    }
  }

  function generateExpression($code_writer)
  {
    $code_writer->writePHP('date(');
    $this->parameters[0]->generateExpression($code_writer);
    $code_writer->writePHP(',');
    $this->base->generateExpression($code_writer);
    $code_writer->writePHP(')');
  }
}

