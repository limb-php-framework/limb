<?php 
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */ 

/**
 * class lmbMacroStrToUpperFilter.
 *
 * @filter strtoupper
 * @package macro
 * @version $Id$
 */ 
abstract class lmbMacroPhpFunctionBasedFilter extends lmbMacroFilter
{
  protected $function;
  
  function getValue()
  {
    $res = $this->function .'(' . $this->base->getValue();
    foreach($this->params as $param)
      $res .= ',' . $param;
    
    $res .= ')';
    return $res;
  }
} 
