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

  // used for parsing expression path items
  protected $text;
  protected $position;

  function __construct($expression_str)
  {
    $this->expression_str = $expression_str;
  }

  function preGenerate($code)
  {
    $this->tmp = $code->getTempVarRef();
    $var = $code->getTempVarRef();

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
    $this->text = $text;
    $length = strlen($text);

    $path_items = array();
    do
    {
      $token = $this->_getToken('/\G("|\'|\.|[^\'"\.]+)/u');
      if ($token === FALSE)
      {
        $path_items[] = $this->text;
        break;
      }

      if ($token == '"' || $token == "'")
      {
        $string = $this->_getToken('/\G([^' . $token . ']*)' . $token . ',?/u');

        if ($string === FALSE)
          $this->context->raise("Expecting a closing quote in expression path item: " . $text);
      }
      elseif($token == '.')
      {
        $path_items[] = substr($this->text, 0, $this->position - 1);
        $this->text = substr($this->text, $this->position);
        $length = strlen($this->text);
        $this->position = 0;
      }
    }
    while($this->position < $length);

    //ensures the last filter expression added
    $path_items[] = substr($this->text, 0, $this->position );
    $this->text = substr($this->text, $this->position);
    $length = strlen($this->text);
    $this->position = 0;

    return $path_items;
  }

  function getValue()
  {
    return $this->tmp;
  }

  protected function _getToken($pattern)
  {
    if (preg_match($pattern, $this->text, $match, PREG_OFFSET_CAPTURE, $this->position))
    {
      $this->position += strlen($match[0][0]);
      return $match[1][0];
    }
    else
      return FALSE;
  }
}

