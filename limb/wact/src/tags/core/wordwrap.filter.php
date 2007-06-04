<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: wordwrap.filter.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
 * @filter wordwrap
 * @min_attributes 1
 * @max_attributes 1
 */
class WactWordWrapFilter extends WactCompilerFilter {

  /**
   * Return this value as a PHP value
   * @return String
   */
  function getValue() {
    if ($this->isConstant()) {
      return wordwrap($this->base->getValue(), $this->parameters[0]->getValue(), "\n", TRUE);
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
    $code_writer->writePHP('wordwrap(');
    $this->base->generateExpression($code_writer);
    $code_writer->writePHP(',');
    $this->parameters[0]->generateExpression($code_writer);
    $code_writer->writePHP(', "\n", TRUE)');
  }

}

?>
