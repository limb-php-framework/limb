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
 * @version $Id: utf8.inc.php 7486 2009-01-26 19:13:20Z pachanga $
 */
lmb_require('limb/i18n/src/charset/lmbUTF8BaseDriver.class.php');
lmb_require('limb/i18n/src/charset/lmbUTF8MbstringDriver.class.php');
lmb_require('limb/i18n/src/charset/driver.inc.php');

if(!defined('LIMB_UTF8_IGNORE_MBSTRING') && function_exists('mb_strlen'))
{
  lmb_require('limb/i18n/src/charset/lmbUTF8MbstringDriver.class.php');
  lmb_use_charset_driver(new lmbUTF8MbstringDriver());
}
else
{
  lmb_require('limb/i18n/src/charset/lmbUTF8BaseDriver.class.php');
  lmb_use_charset_driver(new lmbUTF8BaseDriver());
}

function lmb_utf8_to_win1251($str)
{
   static $conv = '';
   if(!is_array($conv))
   {
     $conv = array();
     for($x = 128; $x <= 143; $x++)
     {
       $conv['utf'][] = chr(209) . chr($x);
       $conv['win'][] = chr($x + 112);
     }

     for($x = 144; $x <= 191; $x++)
     {
       $conv['utf'][] = chr(208) . chr($x);
       $conv['win'][] = chr($x + 48);
     }

     $conv['utf'][] = chr(208) . chr(129);
     $conv['win'][] = chr(168);
     $conv['utf'][] = chr(209) . chr(145);
     $conv['win'][] = chr(184);
   }

   return str_replace($conv['utf'], $conv['win'], $str);
}

function lmb_win1251_to_utf8($s)
{
  $c209 = chr(209);
  $c208 = chr(208);
  $c129 = chr(129);
  $t = '';
  for($i = 0; $i < strlen($s); $i++)
  {
    $c = ord($s[$i]);
    if($c >= 192 && $c <= 239)
      $t .= $c208 . chr($c-48);
    elseif($c > 239)
      $t .= $c209 . chr($c-112);
    elseif($c == 184)
      $t .= $c209 . $c209;
    elseif($c == 168)
      $t .= $c208 . $c129;
    else
      $t .= $s[$i];
  }
  return $t;
}
