<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: common.inc.php 5831 2007-05-08 11:45:14Z dan82 $
 * @package    i18n
 */
require_once('limb/core/common.inc.php');
require_once(dirname(__FILE__) . '/toolkit.inc.php');

function lmb_i18n($text, $arg1 = null, $arg2 = null)
{
  static $toolkit;

  if(!$toolkit)
    $toolkit = lmbToolkit :: instance();

  return $toolkit->translate($text, $arg1, $arg2);
}


function lmb_translit_russian($input, $encoding = 'utf8')   
{  
  if($encoding != 'utf8')  
    $input = iconv($encoding, 'utf8', $input);  
	
  $arrRus = array('à', 'á', 'â', 'ã', 'ä', 'å', '¸', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì',  
                  'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', '÷', 'ø', 'ù', 'ü',  
                  'û', 'ú', 'ý', 'þ', 'ÿ',  
                  'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', '¨', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì',  
                  'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', '×', 'Ø', 'Ù', 'Ü',  
                  'Û', 'Ú', 'Ý', 'Þ', 'ß');  
  $arrEng = array('a', 'b', 'v', 'g', 'd', 'e', 'jo', 'zh', 'z', 'i', 'y', 'k', 'l', 'm',  
                  'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'kh', 'c', 'ch', 'sh', 'sch', '',  
                  'y', '', 'e', 'yu', 'ya',  
                  'A', 'B', 'V', 'G', 'D', 'E', 'JO', 'ZH', 'Z', 'I', 'Y', 'K', 'L', 'M',  
                  'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'KH', 'C', 'CH', 'SH', 'SCH', '',  
                  'Y', '', 'E', 'YU', 'YA');  
                   
  $result = str_replace($arrRus, $arrEng, $input);
	
  if($encoding != 'utf8') 
    return iconv('utf8', $encoding, $result);  
  else 
    return $result;  
}


?>
