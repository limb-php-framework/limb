<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: todate.filter.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
 * @filter todate
 * @max_attributes 1
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

?>
