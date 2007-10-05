<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/lmbMacroExpressionInterface.interface.php');

/**
 * class lmbMacroExpression
 * @package macro
 * @version $Id$
 */
class lmbMacroExpression implements lmbMacroExpressionInterface
{
  protected $tmp;

  function __construct($expression_str)
  {
    $this->expression_str = $expression_str;
  }

  function preGenerate($code)
  {
    $this->tmp = $code->getTempVarRef();

    // simple case if expression is just a variable
    if(strpos($this->expression_str, '.') === false)
    {
      $this->tmp = $this->expression_str;
      return;
    }

    $expr = '';
    $items = explode('.', $this->expression_str);

    //first item is variable itself
    $var = $items[0];
    $code->writePHP($this->tmp . "='';");
    for($i=1; $i<sizeof($items); $i++)
    {
      $item = $items[$i];
      $expr .= 'if((is_array(' . $var . ') && isset(' . $var . '["' . $item . '"])) || ' .
               '(is_object(' . $var . ') && ' . $this->tmp . '=' . $var . '->get("' . $item . '")))' .
               '{if(is_array(' . $var . '))' . $this->tmp . ' = ' . $var . '["' . $item . '"];';
      $var = $this->tmp;
    }

    //closing brackets
    for($i=1; $i < sizeof($items); $i++)
      $expr .= '}else{' . $this->tmp . '="";}';

    $code->writePHP($expr);
  }

  function getValue()
  {
    return $this->tmp;
  }
}

