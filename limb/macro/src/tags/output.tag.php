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

    $result = '';

    $tmp = $code->getTempVarRef();
    $code->writePHP($tmp . "='';");

    $items = explode('.', $this->tag);
    //first item is variable itself
    $prev = $items[0];
    for($i=1; $i<sizeof($items); $i++)
    {
      $item = $items[$i];
      $result .= 'if((is_array(' . $prev . ') && isset(' . $prev . '["' . $item . '"])) || ' . 
                 '(is_object(' . $prev . ') && ' . $tmp . '=' . $prev . '->get("' . $item . '")))' .  
                 '{ if(is_array(' . $prev . '))' . $tmp . ' = ' . $prev . '["' . $item . '"];';
      $prev = $tmp;
    }

    //closing brackets
    for($i=1; $i<sizeof($items); $i++)
      $result .= '}';

    $result .= "echo $tmp;";
    return $result;
  }
}

