<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * A property representing a constant value.
 * @package wact
 * @version $Id: WactConstantProperty.class.php 6243 2007-08-29 11:53:10Z pachanga $
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


