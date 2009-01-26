<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Used to store complex expressions like "{$var1}_my_{$var2}" found inside tag attributes
 * @package macro
 * @version $Id$
 */
class lmbMacroTagAttribute implements lmbMacroExpressionInterface
{
  protected $name;
  protected $expressions = array();
  protected $raw_string = '';

  function __construct($name, $value = null)
  {
    $this->name = $name;

    if($value)
      $this->raw_string .= $value;
  }

  function getName()
  {
    return $this->name;
  }
  
  function setName($name)
  {
    $this->name = $name;
  }

  function addTextFragment($text)
  {
    $this->raw_string .= $text;
  }

  function addExpressionFragment($expression)
  {
    $this->raw_string .= '%s';
    $this->expressions[] = $expression;
  }

  function preGenerate($code_writer)
  {
    foreach($this->expressions as $fragment)
      $fragment->preGenerate($code_writer);
  }

  function isDynamic()
  {
    return (bool)sizeof($this->expressions) || (strpos($this->raw_string, '$') === 0);
  }

  function getValue()
  {
    // simple case
    if(!sizeof($this->expressions))
       return $this->raw_string;
    
    if((sizeof($this->expressions) == 1) && ($this->raw_string == "%s"))
    {
      return $this->expressions[0]->getValue();
    }

    $res = 'sprintf(\'' . $this->raw_string. '\',';

    $separator = '';
    foreach($this->expressions as $expressions)
    {
      $res .= $separator . $expressions->getValue();
      $separator = ',';
    }

    $res .= ')';
    return $res;
  }
}


