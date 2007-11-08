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
    $this->tmp = $code->generateVar();
    $var = $code->generateVar();

    // simple case if expression is just a variable
    if(strpos($this->expression_str, '.') === false)
    {
      $this->tmp = $this->expression_str;
      return;
    }

    $expr = '';
    
    $items = $this->_extractExpressionPathItems($this->expression_str);

    //first item is variable itself
    //$var = $items[0];
    $expr .= $var . ' = ' . $items[0] . ';';
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

  protected function _extractExpressionPathItems($text)
  {
    $tokens = token_get_all('<?php ' . $text . '?>');
    // removing first and last tokens since we just added them with the line above
    array_shift($tokens);
    array_pop($tokens);

    $path_items = array();
    $in_function = false;
    
    $item = '';
    foreach($tokens as $token)
    {
      if(is_scalar($token))
      {
        if($token == '(')
          $in_function = true;
        if($token == ')')
          $in_function = false;
        if($token == '.' && !$in_function)
        {
          $path_items[] = $item;
          $item = '';
          continue;
        }
        
        $item .= $token;
      }
      else
        $item .= $token[1];
    }
    
    $path_items[] = $item;
    
    return $path_items;
  }

  function getValue()
  {
    return $this->tmp;
  }
}

