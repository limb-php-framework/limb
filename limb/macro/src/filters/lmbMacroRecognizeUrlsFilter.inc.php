<?php 
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */ 

function lmb_macro_recognize_urls($value)
{
  if(!preg_match_all("~((?:(?:ht|f)tps?://|www\.)[^<\s\n]+)(?<![]\.,:;!\})<-])~", $value, $matches))
    return $value;

  $replace = array();
  foreach($matches[0] as $i => $params)
    $replace[$i] = '<a href="' . $matches[1][$i] . '">' . $matches[1][$i] . '</a>';

  $value = str_replace($matches[0], $replace, $value);
  return $value;
}
 
