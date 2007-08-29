<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @filter url
 * @package wact
 * @version $Id: url.filter.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class WactUrlFilter extends WactCompilerFilter {

  /**
   * Return this value as a PHP value
   * @return String
   */
  function getValue() {
    if ($this->isConstant()) {
      return urlencode($base->getValue());
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
    $code_writer->writePHP('urlencode(');
    $this->base->generateExpression($code_writer);
    $code_writer->writePHP(')');
  }

}


