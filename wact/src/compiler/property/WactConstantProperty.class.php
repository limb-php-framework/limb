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
 * @version $Id: WactConstantProperty.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactConstantProperty extends WactCompilerProperty
{
  protected $value;

  function __construct($value)
  {
    $this->value = $value;
  }

  function isConstant() {
    return TRUE;
  }

  function getValue()
  {
    return $this->value;
  }

  function generateExpression($code_writer)
  {
    $code_writer->writePHPLiteral($this->value);
  }

  function prepare()
  {
  }
}


