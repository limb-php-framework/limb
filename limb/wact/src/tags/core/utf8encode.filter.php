<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: utf8encode.filter.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 * @filter utf8encode
 */
class WactUtf8EncodeFilter extends WactCompilerFilter {

  /**
   * Return this value as a PHP value
   * @return String
   */
  function getValue() {
    if ($this->isConstant()) {
      return utf8_encode($this->base->getValue());
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
    $code_writer->writePHP('utf8_encode(');
    $this->base->generateExpression($code_writer);
    $code_writer->writePHP(')');
  }

}

?>
