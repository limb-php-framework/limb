<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactConstantProperty.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
* A property representing a constant value.
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

?>