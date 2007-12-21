<?php 
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */ 

/**
 * class lmbMacroFunctionBasedFilter.
 *
 * @filter strtoupper
 * @package macro
 * @version $Id$
 */ 
abstract class lmbMacroFunctionBasedFilter extends lmbMacroFilter
{
  protected $function;
  protected $include_file;
  
  function preGenerate($code)
  {
    if($this->include_file)
      $code->registerInclude($this->include_file); 
      
    parent :: preGenerate($code);
  }
  
  function getValue()
  {
    $res = $this->function .'(' . $this->base->getValue();
    foreach($this->params as $param)
      $res .= ',' . $param;
    
    $res .= ')';
    return $res;
  }
} 
