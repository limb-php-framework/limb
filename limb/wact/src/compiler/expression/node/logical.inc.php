<?php
/**
* Web Application Component Toolkit
*
* @link http://www.phpwact.org/
*
* @author Wact Development Team
* @link http://www.phpwact.org/team
*
* @copyright Copyright 2006, Jeff Moore
* @license http://opensource.org/licenses/mit-license.php MIT
*
* @package Template
* @version 0.9
*/

/**
* File level includes
*/
require_once 'limb/wact/src/compiler/expression/node/expression.inc.php';

/**
* A node representing the logical and operator
*/
class WactLogicalAndExpressionNode extends WactBinaryExpressionNode {

  /**
  * Return this value as a PHP value
  * @return String
  */
  function getValue() {
    if ($this->isConstant()) {
      return $this->firstOperand->getValue() && $this->secondOperand->getValue();
    } else {
      die("Not a constant");
    }
    }

  /**
  * Generate the code to read the data value at run time
  * Must generate only a valid PHP Expression.
  * @param WactCodeWriter
  * @return void
  */
  function generateExpression($code) {
    $code->writePHP('(');
    $this->firstOperand->generateExpression($code);
    $code->writePHP('&&');
    $this->secondOperand->generateExpression($code);
    $code->writePHP(')');
    }

}


/**
* A node representing the logical or operator
*/
class WactLogicalOrExpressionNode extends WactBinaryExpressionNode {

  /**
  * Return this value as a PHP value
  * @return String
  */
  function getValue() {
    if ($this->isConstant()) {
      return $this->firstOperand->getValue() || $this->secondOperand->getValue();
    } else {
      die("Not a constant");
    }
    }

  /**
  * Generate the code to read the data value at run time
  * Must generate only a valid PHP Expression.
  * @param WactCodeWriter
  * @return void
  */
  function generateExpression($code) {
    $code->writePHP('(');
    $this->firstOperand->generateExpression($code);
    $code->writePHP('||');
    $this->secondOperand->generateExpression($code);
    $code->writePHP(')');
    }

}

/**
* A node representing the logical or operator
*/
class WactLogicalNotExpressionNode extends WactUnaryExpressionNode {

  /**
  * Return this value as a PHP value
  * @return String
  */
  function getValue() {
    if ($this->isConstant()) {
      return !$this->operand->getValue();
    } else {
      die("Not a constant");
    }
    }

  /**
  * Generate the code to read the data value at run time
  * Must generate only a valid PHP Expression.
  * @param WactCodeWriter
  * @return void
  */
  function generateExpression($code) {
    $code->writePHP('!(');
    $this->operand->generateExpression($code);
    $code->writePHP(')');
    }

}


/**
* A node representing the Less than or Equal To comparison operator
*/
class WactEqualToExpressionNode extends WactBinaryExpressionNode {

  /**
  * Return this value as a PHP value
  * @return String
  */
  function getValue() {
    if ($this->isConstant()) {
      return $this->firstOperand->getValue() == $this->secondOperand->getValue();
    } else {
      die("Not a constant");
    }
    }

  /**
  * Generate the code to read the data value at run time
  * Must generate only a valid PHP Expression.
  * @param WactCodeWriter
  * @return void
  */
  function generateExpression($code) {
    $code->writePHP('(');
    $this->firstOperand->generateExpression($code);
    $code->writePHP('==');
    $this->secondOperand->generateExpression($code);
    $code->writePHP(')');
    }

}

/**
* A node representing the Less than or Equal To comparison operator
*/
class WactNotEqualToExpressionNode extends WactBinaryExpressionNode {

  /**
  * Return this value as a PHP value
  * @return String
  */
  function getValue() {
    if ($this->isConstant()) {
      return $this->firstOperand->getValue() != $this->secondOperand->getValue();
    } else {
      die("Not a constant");
    }
    }

  /**
  * Generate the code to read the data value at run time
  * Must generate only a valid PHP Expression.
  * @param WactCodeWriter
  * @return void
  */
  function generateExpression($code) {
    $code->writePHP('(');
    $this->firstOperand->generateExpression($code);
    $code->writePHP('!=');
    $this->secondOperand->generateExpression($code);
    $code->writePHP(')');
    }

}


/**
* A node representing the greater than comparison operator
*/
class WactGreaterThanExpressionNode extends WactBinaryExpressionNode {

  /**
  * Return this value as a PHP value
  * @return String
  */
  function getValue() {
    if ($this->isConstant()) {
      return $this->firstOperand->getValue() > $this->secondOperand->getValue();
    } else {
      die("Not a constant");
    }
    }

  /**
  * Generate the code to read the data value at run time
  * Must generate only a valid PHP Expression.
  * @param WactCodeWriter
  * @return void
  */
  function generateExpression($code) {
    $code->writePHP('(');
    $this->firstOperand->generateExpression($code);
    $code->writePHP('>');
    $this->secondOperand->generateExpression($code);
    $code->writePHP(')');
    }

}

/**
* A node representing the greater than or equal to comparison operator
*/
class WactGreaterThanOrEqualToExpressionNode extends WactBinaryExpressionNode {

  /**
  * Return this value as a PHP value
  * @return String
  */
  function getValue() {
    if ($this->isConstant()) {
      return $this->firstOperand->getValue() >= $this->secondOperand->getValue();
    } else {
      die("Not a constant");
    }
    }

  /**
  * Generate the code to read the data value at run time
  * Must generate only a valid PHP Expression.
  * @param WactCodeWriter
  * @return void
  */
  function generateExpression($code) {
    $code->writePHP('(');
    $this->firstOperand->generateExpression($code);
    $code->writePHP('>=');
    $this->secondOperand->generateExpression($code);
    $code->writePHP(')');
    }

}


/**
* A node representing the Less than comparison operator
*/
class WactLessThanExpressionNode extends WactBinaryExpressionNode {

  /**
  * Return this value as a PHP value
  * @return String
  */
  function getValue() {
    if ($this->isConstant()) {
      return $this->firstOperand->getValue() < $this->secondOperand->getValue();
    } else {
      die("Not a constant");
    }
    }

  /**
  * Generate the code to read the data value at run time
  * Must generate only a valid PHP Expression.
  * @param WactCodeWriter
  * @return void
  */
  function generateExpression($code) {
    $code->writePHP('(');
    $this->firstOperand->generateExpression($code);
    $code->writePHP('<');
    $this->secondOperand->generateExpression($code);
    $code->writePHP(')');
    }

}

/**
* A node representing the Less than or Equal To comparison operator
*/
class WactLessThanOrEqualToExpressionNode extends WactBinaryExpressionNode {

  /**
  * Return this value as a PHP value
  * @return String
  */
  function getValue() {
    if ($this->isConstant()) {
      return $this->firstOperand->getValue() <= $this->secondOperand->getValue();
    } else {
      die("Not a constant");
    }
    }

  /**
  * Generate the code to read the data value at run time
  * Must generate only a valid PHP Expression.
  * @param WactCodeWriter
  * @return void
  */
  function generateExpression($code) {
    $code->writePHP('(');
    $this->firstOperand->generateExpression($code);
    $code->writePHP('<=');
    $this->secondOperand->generateExpression($code);
    $code->writePHP(')');
    }

}


?>