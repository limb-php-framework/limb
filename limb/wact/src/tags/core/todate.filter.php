<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @filter todate
 * @max_attributes 1
 * @package wact
 * @version $Id: todate.filter.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class WactToDateFilter extends WactCompilerFilter
{
  protected $input;

  function getValue()
  {
    if ($this->isConstant())
    {
      if ($value = $this->base->getValue())
        return strtotime($value);
    } else {
      $this->raiseUnresolvedBindingError();
    }
  }

  /**
   * @param WactCodeWriter
   */
  function generatePreStatement($code_writer)
  {
    parent::generatePreStatement($code_writer);
    $this->input = $code_writer->getTempVarRef();
    $code_writer->writePHP($this->input.'=');
    $this->base->generateExpression($code_writer);
    $code_writer->writePHP(';');
  }

  /**
   * @param WactCodeWriter
   */
  function generateExpression($code_writer)
  {
    $code_writer->writePHP('(('.$this->input.')?strtotime('.$this->input.'):"")');
  }
}


