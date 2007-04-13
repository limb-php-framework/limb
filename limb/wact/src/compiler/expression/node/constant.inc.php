<?php
require_once 'limb/wact/src/compiler/expression/node/expression.inc.php';

/**
* A property representing a constant value.
*/
class WactConstantExpressionNode extends WactTemplateExpressionNode
{
  protected $value;

  function __construct($value)
  {
    $this->value = $value;
  }

  /**
  * Does this property refer to a constant value at compile time?
  * @return Boolean
  */
  function isConstant()
  {
    return TRUE;
  }

  /**
  * Return this value as a PHP value
  * @return String
  */
  function getValue()
  {
    return $this->value;
  }

  /**
  * Generate the code to read the data value at run time
  * Must generate only a valid PHP Expression.
  * @param WactCodeWriter
  * @return void
  */
  function generateExpression($code)
  {
    $code->writePHPLiteral($this->value);
  }
}

?>