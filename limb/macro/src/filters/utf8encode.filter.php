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
 * @filter utf8_encode
 * @aliases utf8encode
 * @package macro
 * @version $Id$
 */
class lmbMacroUtf8EncodeFilter extends lmbMacroPhpFunctionBasedFilter
{
  protected $function = 'utf8_encode';
}  


