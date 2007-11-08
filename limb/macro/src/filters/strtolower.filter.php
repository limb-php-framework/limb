<?php 
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */ 
lmb_require('limb/macro/src/filters/lmbMacroPhpFunctionBasedFilter.class.php');

/**
 * class lmbMacroStrToLowerFilter.
 *
 * @filter strtolower
 * @aliases lowercase
 * @package macro
 * @version $Id$
 */ 
class lmbMacroStrToLowerFilter extends lmbMacroPhpFunctionBasedFilter
{
  protected $function = 'strtolower';
} 
