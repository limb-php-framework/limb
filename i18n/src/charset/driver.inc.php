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
 * @version $Id: driver.inc.php 7486 2009-01-26 19:13:20Z pachanga $
 */
lmb_require('limb/i18n/src/charset/lmbSingleByteCharsetDriver.class.php');

function lmb_use_charset_driver($driver)
{
  $prev_driver = lmb_get_charset_driver();
  $GLOBALS['LIMB_CHARSET_DRIVER'] = $driver;
  return $prev_driver;
}

function lmb_get_charset_driver()
{
  if(isset($GLOBALS['LIMB_CHARSET_DRIVER']))
    return $GLOBALS['LIMB_CHARSET_DRIVER'];
}

if(!lmb_get_charset_driver())
{
  lmb_require('limb/i18n/src/charset/lmbSingleByteCharsetDriver.class.php');
  lmb_use_charset_driver(new lmbSingleByteCharsetDriver());
}

/**
 * Multibyte aware replacement for strlen()
 */
function lmb_strlen($string)
{
  return $GLOBALS['LIMB_CHARSET_DRIVER']->_strlen($string);
}
/**
 * Multibyte aware replacement for substr()
 */
function lmb_substr($str, $start, $length=null)
{
  return $GLOBALS['LIMB_CHARSET_DRIVER']->_substr($str, $start, $length);
}
/**
 * Multibyte aware replacement for strrepalce()
 */
function lmb_str_replace($s, $r, $str)
{
  return $GLOBALS['LIMB_CHARSET_DRIVER']->_str_replace($s, $r, $str);
}
/**
 * Multibyte aware replacement for ltrim()
 */
function lmb_ltrim($str, $charlist = '')
{
  return $GLOBALS['LIMB_CHARSET_DRIVER']->_ltrim($str, $charlist);
}
/**
 * Multibyte aware replacement for ltrim()
 */
function lmb_rtrim($str, $charlist = '')
{
  return $GLOBALS['LIMB_CHARSET_DRIVER']->_rtrim($str, $charlist);
}
/**
 * Multibyte aware replacement for trim()
 */
function lmb_trim($str, $charlist = '')
{
  if($charlist == '')
    return $GLOBALS['LIMB_CHARSET_DRIVER']->_trim($str);
  else
    return $GLOBALS['LIMB_CHARSET_DRIVER']->_trim($str, $charlist);
}
/**
 * This is a unicode aware replacement for strtolower()
 */
function lmb_strtolower($string)
{
  return $GLOBALS['LIMB_CHARSET_DRIVER']->_strtolower($string);
}
/**
 * This is a unicode aware replacement for strtoupper()
 */
function lmb_strtoupper($string)
{
  return $GLOBALS['LIMB_CHARSET_DRIVER']->_strtoupper($string);
}
/**
 * Multibyte aware replacement for strpos
 */
function lmb_strpos($haystack, $needle, $offset=null)
{
  return $GLOBALS['LIMB_CHARSET_DRIVER']->_strpos($haystack, $needle, $offset);
}
/**
 * Multibyte aware replacement for strrpos
 */
function lmb_strrpos($haystack, $needle, $offset=null)
{
  return $GLOBALS['LIMB_CHARSET_DRIVER']->_strrpos($haystack, $needle, $offset);
}
/**
 * Multibyte aware replacement for ucfirst
 */
function lmb_ucfirst($str)
{
  return $GLOBALS['LIMB_CHARSET_DRIVER']->_ucfirst($str);
}
/*
 * Multibyte aware replacement for strcasecmp
 */
function lmb_strcasecmp($strX, $strY)
{
  return $GLOBALS['LIMB_CHARSET_DRIVER']->_strcasecmp($strX, $strY);
}
/**
 * Multibyte aware replacement for substr_count
 */
function lmb_substr_count($haystack, $needle)
{
  return $GLOBALS['LIMB_CHARSET_DRIVER']->_substr_count($haystack, $needle);
}
/**
 * Multibyte aware replacement for str_split
 */
function lmb_str_split($str, $split_len=1)
{
  return $GLOBALS['LIMB_CHARSET_DRIVER']->_str_split($strX, $strY);
}
/**
 * This is multibyte aware alternative to preg_match
 */
function lmb_preg_match($pattern, $subject, &$matches, $flags=null, $offset=null)
{
  return $GLOBALS['LIMB_CHARSET_DRIVER']->_preg_match($pattern, $subject, $matches, $flags, $offset);
}
/**
 * This is multibyte aware alternative to preg_match_all
 */
function lmb_preg_match_all($pattern, $subject, &$matches, $flags=null, $offset=null)
{
  return $GLOBALS['LIMB_CHARSET_DRIVER']->_preg_match_all($pattern, $subject, $matches, $flags, $offset);
}
/**
 * This is multibyte aware alternative to preg_replace
 */
function lmb_preg_replace($pattern, $replacement, $subject, $limit=null)
{
  return $GLOBALS['LIMB_CHARSET_DRIVER']->_preg_replace($pattern, $replacement, $subject, $limit);
}
/**
 * This is multibyte aware alternative to preg_replace_callback
 */
function lmb_preg_replace_callback($pattern, $callback, $subject, $limit=null)
{
  return $GLOBALS['LIMB_CHARSET_DRIVER']->_preg_replace_callback($pattern, $callback, $subject, $limit);
}
/**
 * This is multibyte aware alternative to preg_split
 */
function lmb_preg_split($pattern, $subject, $limit=null, $flags=null)
{
  return $GLOBALS['LIMB_CHARSET_DRIVER']->_preg_split($pattern, $subject, $limit, $flags);
}
