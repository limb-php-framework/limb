<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */ 
 
/**
 * class lmbMacroDateFilter.
 *
 * @filter date
 * @package macro
 * @version $Id$
 */ 
class lmbMacroDateFilter extends lmbMacroFilter
{
  function getValue()
  {
    return 'date(' . $this->params[0].', ' . $this->base->getValue() . ')';
  }  
} 
