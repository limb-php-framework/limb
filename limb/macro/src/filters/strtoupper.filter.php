<?php 
/**
 * class lmbMacroStrToUpperFilter.
 *
 * @filter strtoupper
 * @package macro
 * @version $Id$
 */ 
class lmbMacroStrToUpperFilter extends lmbMacroFilter
{
  function getValue()
  {
    return 'strtoupper(' . $this->base->getValue() . ')';
  }
} 
