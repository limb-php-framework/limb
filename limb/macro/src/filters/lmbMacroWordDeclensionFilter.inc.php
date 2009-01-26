<?php 
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */ 

function lmb_macro_choose_declension_by_number($number, $singular_form, $plural_main_form, $plural_other_form)
{  
  if(substr($number, -2) == 11)
    return $plural_main_form;
  
  if(substr($number, -1) == 1)
    return $singular_form;

  if(in_array(substr($number, -1), array(2, 3, 4)))
  {
    if($number > 10 AND (in_array(substr($number, -2), array(12, 13, 14))))
      return $plural_main_form;
    else
      return $plural_other_form;
  }
  return $plural_main_form;
}
