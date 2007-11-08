<?php 
/**
 * class lmbMacroTrimFilter.
 *
 * @filter trim
 * @package macro
 * @version $Id$
 */ 
class lmbMacroTrimFilter extends lmbMacroFilter
{
  function getValue()
  {
    if(!isset($this->params[0]))
      return 'trim(' . $this->base->getValue() . ')';
    else
      return 'trim(' . $this->base->getValue() . ', ' . $this->params[0] . ')';
  }
} 
