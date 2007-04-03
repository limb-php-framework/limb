<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: number.filter.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 * @filter number
 * @max_attributes 3
 */
class WactNumberFilter extends WactCompilerFilter {

  /**
   * Return this value as a PHP value
   * @return String
   * @access public
   */
  function getValue() {
    $places = 0;
    $decimal = '.';
    $thousep = ',';
    if (array_key_exists(0, $this->parameters)) {
      $places = (int)$this->parameters[0]->getValue();
    }
    if (array_key_exists(1, $this->parameters)
    && array_key_exists(2, $this->parameters)) {
      $decimal = $this->parameters[1]->getValue();
      $thousep = $this->parameters[2]->getValue();
    }
    if ($this->isConstant()) {
      return number_format($this->base->getValue(), $places, $decimal, $thousep);
    } else {
      $this->raiseUnresolvedBindingError();
    }
  }

  /**
   * Generate the code to read the data value at run time
   * Must generate only a valid PHP Expression.
   * @param WactCodeWriter
   * @return void
   * @access protected
   */
  function generateExpression($code_writer) {
    $code_writer->writePHP('number_format(');
    $this->base->generateExpression($code_writer);
    if (array_key_exists(0, $this->parameters)) {
      $code_writer->writePHP(',');
      $this->parameters[0]->generateExpression($code_writer);
    }
    if (array_key_exists(1, $this->parameters)
    && array_key_exists(2, $this->parameters)) {
      $code_writer->writePHP(',');
      $this->parameters[1]->generateExpression($code_writer);
      $code_writer->writePHP(',');
      $this->parameters[2]->generateExpression($code_writer);
    }
    $code_writer->writePHP(')');
  }

}

?>
