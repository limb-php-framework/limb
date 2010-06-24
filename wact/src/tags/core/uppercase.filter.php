<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @filter uppercase
 * @package wact
 * @version $Id: uppercase.filter.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactUpperCaseFilter extends WactCompilerFilter {

  /**
   * Return this value as a PHP value
   * @return String
   */
  function getValue() {
    if ($this->isConstant()) {
      return strtoupper($this->base->getValue());
    } else {
      $this->raiseUnresolvedBindingError();
    }
  }

  /**
   * Generate the code to read the data value at run time
   * Must generate only a valid PHP Expression.
   * @param WactCodeWriter
   * @return void
   */
  function generateExpression($code_writer) {
    $code_writer->writePHP('strtoupper(');
    $this->base->generateExpression($code_writer);
    $code_writer->writePHP(')');
  }

}


