<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * A property representing a constant value.
 * @package wact
 * @version $Id$
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
    $code->writePHP(var_export($this->value, TRUE));
  }
}


