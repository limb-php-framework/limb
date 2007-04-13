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
* Base class for all expression nodes
*/
abstract class WactTemplateExpressionNode {

    /**
    * Does this expression refer to a constant value (at compile time)?
    * @return Boolean
    */
    function isConstant() {
    }

    /**
    * Return the value of this expression
    * @return String
    */
    function getValue() {
    }

    /**
    * Generate setup code for an expression reference
    * @param WactCodeWriter
    * @return void
    */
    function generatePreStatement($code) {
    }

    /**
    * Generate the code to read the data value at run time
    * Must generate only a valid PHP Expression.
    * @param WactCodeWriter
    * @return void
    */
    function generateExpression($code) {
    }

    /**
    * Generate tear down code for an expression reference
    * @param WactCodeWriter
    * @return void
    */
    function generatePostStatement($code) {
    }

    /**
    * Calls the prepare method on the root of the formatter chain
    * @return void
    */
    function prepare() {
    }

}

/**
* Base class for all Unary expression nodes
*/
abstract class WactUnaryExpressionNode extends WactTemplateExpressionNode {

	protected $operand;

	function __construct($operand) {
		$this->operand = $operand;
	}

	function isConstant() {
		return $this->operand->isConstant();
	}
	
    function generatePreStatement($code) {
		$this->operand->generatePreStatement($code);
    }

    function generateExpression($code) {
		$this->operand->generateExpression($code);
    }

    function generatePostStatement($code) {
		$this->operand->generatePostStatement($code);
    }

    function prepare() {
		$this->operand->prepare();
    }
	
}

/**
* Base class for all Unary expression nodes
*/
abstract class WactBinaryExpressionNode extends WactTemplateExpressionNode {

	protected $firstOperand;
	protected $secondOperand;

	function __construct($first, $second) {
		$this->firstOperand = $first;
		$this->secondOperand = $second;
	}

	function isConstant() {
		return $this->firstOperand->isConstant() && $this->secondOperand->isConstant();
	}
	
    function generatePreStatement($code) {
		$this->firstOperand->generatePreStatement($code);
		$this->secondOperand->generatePreStatement($code);
    }

    function generateExpression($code) {
		$this->firstOperand->generateExpression($code);
		$this->secondOperand->generateExpression($code);
    }

    function generatePostStatement($code) {
		$this->firstOperand->generatePostStatement($code);
		$this->secondOperand->generatePostStatement($code);
    }

    function prepare() {
		$this->firstOperand->prepare();
		$this->secondOperand->prepare();
    }
	
}

?>