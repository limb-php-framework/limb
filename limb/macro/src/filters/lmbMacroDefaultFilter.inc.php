<?php 
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */ 

function lmb_macro_apply_default($value, $default)
{
  if (empty($value) && $value !== "0" && $value !== 0)
    return $default;
  else
    return $value;
}
 
