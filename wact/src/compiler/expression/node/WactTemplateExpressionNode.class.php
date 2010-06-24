<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Base class for all expression nodes
 * @package wact
 * @version $Id$
 */
class WactTemplateExpressionNode
{
  /**
  * Does this expression refer to a constant value (at compile time)?
  * @return Boolean
  */
  function isConstant()
  {
    return false;
  }

  /**
  * Return the value of this expression
  * @return String
  */
  function getValue()
  {
  }

  /**
  * Generate setup code for an expression reference
  * @param WactCodeWriter
  * @return void
  */
  function generatePreStatement($code)
  {
  }

  /**
  * Generate the code to read the data value at run time
  * Must generate only a valid PHP Expression.
  * @param WactCodeWriter
  * @return void
  */
  function generateExpression($code)
  {
  }

  /**
  * Generate tear down code for an expression reference
  * @param WactCodeWriter
  * @return void
  */
  function generatePostStatement($code)
  {
  }

  /**
  * Calls the prepare method on the root of the formatter chain
  * @return void
  */
  function prepare()
  {
  }
}

