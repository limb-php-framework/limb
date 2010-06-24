<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package i18n
 * @version $Id: common.inc.php 8042 2010-01-19 20:53:10Z korchasa $
 */
require_once('limb/core/common.inc.php');
lmb_require('limb/i18n/toolkit.inc.php');

function lmb_i18n($text, $arg1 = null, $arg2 = null)
{
  static $toolkit;

  if(!$toolkit)
    $toolkit = lmbToolkit :: instance();

  return $toolkit->translate($text, $arg1, $arg2);
}


function lmb_translit_russian($input, $encoding = 'utf-8')
{
  $encoding = strtolower($encoding);
  if($encoding != 'utf-8')
    $input = iconv($encoding, 'utf-8', $input);

  $arrRus = array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м',
                  'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь',
                  'ы', 'ъ', 'э', 'ю', 'я',
                  'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М',
                  'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ь',
                  'Ы', 'Ъ', 'Э', 'Ю', 'Я');
  $arrEng = array('a', 'b', 'v', 'g', 'd', 'e', 'jo', 'zh', 'z', 'i', 'y', 'k', 'l', 'm',
                  'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'kh', 'c', 'ch', 'sh', 'sch', '',
                  'y', '', 'e', 'yu', 'ya',
                  'A', 'B', 'V', 'G', 'D', 'E', 'JO', 'ZH', 'Z', 'I', 'Y', 'K', 'L', 'M',
                  'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'KH', 'C', 'CH', 'SH', 'SCH', '',
                  'Y', '', 'E', 'YU', 'YA');

  $result = str_replace($arrRus, $arrEng, $input);

  if($encoding != 'utf-8')
    return iconv('utf-8', $encoding, $result);
  else
    return $result;
}

lmb_package_register('i18n', dirname(__FILE__));
