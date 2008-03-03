<?php 
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */ 

/**
 * class lmbMacroDefaultFilter.
 *
 * @filter default
 * @package macro
 * @version $Id$
 */ 
class lmbMacroDefaultFilter extends lmbMacroFunctionBasedFilter
{
  protected $function = 'lmb_macro_apply_default';
  protected $include_file = 'limb/macro/src/filters/lmbMacroDefaultFilter.inc.php';
} 
