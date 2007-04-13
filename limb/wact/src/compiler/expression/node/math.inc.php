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
* A node representing the addition operator
*/
class WactAdditionExpressionNode extends WactBinaryExpressionNode {

  /**
  * Return this value as a PHP value
  * @return String
  */
  function getValue() {
    if ($this->isConstant()) {
      return $this->firstOperand->getValue() + $this->secondOperand->getValue();
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
    $code->writePHP('+');
    $this->secondOperand->generateExpression($code);
    $code->writePHP(')');
    }

}

/**
* A node representing the subtraction operator
*/
class WactSubtractionExpressionNode extends WactBinaryExpressionNode {

  /**
  * Return this value as a PHP value
  * @return String
  */
  function getValue() {
    if ($this->isConstant()) {
      return $this->firstOperand->getValue() - $this->secondOperand->getValue();
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
    $code->writePHP('-');
    $this->secondOperand->generateExpression($code);
    $code->writePHP(')');
    }

}

/**
* A node representing the multiplication operator
*/
class WactMultiplicationExpressionNode extends WactBinaryExpressionNode {

  /**
  * Return this value as a PHP value
  * @return String
  */
  function getValue() {
    if ($this->isConstant()) {
      return $this->firstOperand->getValue() * $this->secondOperand->getValue();
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
    $code->writePHP('*');
    $this->secondOperand->generateExpression($code);
    $code->writePHP(')');
    }

}

/**
* A node representing the division operator
*/
class WactDivisionExpressionNode extends WactBinaryExpressionNode {

  /**
  * Return this value as a PHP value
  * @return String
  */
  function getValue() {
    // How do we handle divide by zero?
    if ($this->isConstant()) {
      return $this->firstOperand->getValue() / $this->secondOperand->getValue();
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
    $code->writePHP('/');
    $this->secondOperand->generateExpression($code);
    $code->writePHP(')');
    }

}

/**
* A node representing the modulo operator
*/
class WactModuloExpressionNode extends WactBinaryExpressionNode {

  /**
  * Return this value as a PHP value
  * @return String
  */
  function getValue() {
    // How do we handle divide by zero?
    if ($this->isConstant()) {
      return $this->firstOperand->getValue() % $this->secondOperand->getValue();
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
    $code->writePHP('%');
    $this->secondOperand->generateExpression($code);
    $code->writePHP(')');
    }

}

/**
* A node representing the unary minus operator
*/
class WactUnaryMInusExpressionNode extends WactUnaryExpressionNode {

  /**
  * Return this value as a PHP value
  * @return String
  */
  function getValue() {
    // How do we handle divide by zero?
    if ($this->isConstant()) {
      return -($this->operand->getValue());
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
    $code->writePHP('(-');
    $this->operand->generateExpression($code);
    $code->writePHP(')');
    }

}

/**
* A node representing the string concatination operator
*/
class WactConcatinationExpressionNode extends WactBinaryExpressionNode {

  /**
  * Return this value as a PHP value
  * @return String
  */
  function getValue() {
    if ($this->isConstant()) {
      return $this->firstOperand->getValue() . $this->secondOperand->getValue();
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
    $code->writePHP('.');
    $this->secondOperand->generateExpression($code);
    $code->writePHP(')');
    }

}

?>