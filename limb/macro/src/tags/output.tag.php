<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/lmbMacroTag.class.php');

/**
 * class lmbMacroOutputTag.
 *
 * @tag $output
 * @endtag no
 * @package macro
 * @version $Id$
 */
class lmbMacroOutputTag extends lmbMacroTag
{
  function generateContents($code)
  {
    $code->writePHP($this->_compileExpression($code));
  }

  protected function _compileExpression($code)
  {
    if(strpos($this->tag, '.') === false)
      return 'echo ' . $this->tag . ';';

    $expr = '';

    $items = explode('.', $this->tag);

    //first item is variable itself
    $var = $items[0];
    $tmp = $code->getTempVarRef();
    $code->writePHP($tmp . "='';");
    for($i=1; $i<sizeof($items); $i++)
    {
      $item = $items[$i];
      $expr .= 'if((is_array(' . $var . ') && isset(' . $var . '["' . $item . '"])) || ' . 
               '(is_object(' . $var . ') && ' . $tmp . '=' . $var . '->get("' . $item . '")))' .  
               '{if(is_array(' . $var . '))' . $tmp . ' = ' . $var . '["' . $item . '"];';
      $var = $tmp;
    }

    //closing brackets
    for($i=1; $i<sizeof($items); $i++)
      $expr .= '}else{' . $tmp . '="";}';

    $expr .= "echo $tmp;";
    return $expr;
  }
}

