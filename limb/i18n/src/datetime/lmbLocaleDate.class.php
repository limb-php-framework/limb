<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbLocaleDate.class.php 5353 2007-03-27 16:20:49Z pachanga $
 * @package    i18n
 */
lmb_require('limb/core/src/exception/lmbException.class.php');
lmb_require('limb/datetime/src/lmbDate.class.php');
lmb_require('limb/i18n/src/datetime/lmbDateFormat.class.php');

class lmbLocaleDate extends lmbDate
{
  /**
  *  Tries to guess time values in time string $time_string formatted with $fmt
  *  Returns an array('hour','minute','second','month','day','year')
  *  At this moment only most common tags are supported.
  */
  static function parseTimeString($locale, $time_string, $fmt)
  {
    $hour = 0;
    $minute = 0;
    $second = 0;
    $month = 0;
    $day = 0;
    $year = 0;

    if(!($time_array = self :: explodeTimeStringByFormat($time_string, $fmt)))
      return -1;

    foreach($time_array as $time_char => $value)
    {
      switch($time_char)
      {
        case '%p':
        case '%P':
          if(strtolower($value) == $locale->getPmName())
            $hour += 12;
        break;

        case '%I':
        case '%H':
          $hour = (int)$value;
        break;

        case '%M':
          $minute = (int)$value;
        break;

        case '%S':
          $second = (int)$value;
        break;

        case '%m':
          $month = (int)$value;
        break;

        case '%b':
        case '%h':
          if(($index = array_search($value, $locale->getMonthNames(true))) !== false)
          {
            if($index !== false)
              $month = $index + 1;
          }
        break;

        case '%B':
          if(($index = array_search($value, $locale->getMonthNames())) !== false)
          {
            if($index !== false)
              $month = $index + 1;
          }
        break;

        case '%d':
          $day = (int)$value;
        break;

        case '%Y':
          $year = (int)$value;
        break;
        case '%y':
          if($value < 40)
            $year = 2000 + $value;
          else
            $year = 1900 + $value;
        break;

        case '%T':
          if ($regs = explode(':', $value))
          {
            $hour   = (int)$regs[1];
            $minute = (int)$regs[2];
            $second = (int)$regs[3];
          }
        break;

        case '%D':
          if ($regs = explode('/', $value))
          {
            $hour   = (int)$regs[1];
            $minute = (int)$regs[2];
            $second = (int)$regs[3];
          }
        break;

        case '%R':
          if ($regs = explode(':', $value))
          {
            $hour   = (int)$regs[1];
            $minute = (int)$regs[2];
          }
        break;
      }
    }

    return array('hour' => $hour, 'minute' => $minute, 'second' => $second, 'month' => $month, 'day' => $day, 'year' => $year);
  }

  static function explodeTimeStringByFormat($time_string, $fmt)
  {
    $fmt_len = strlen($fmt);
    $time_string_len = strlen($time_string);

    $time_array = array();

    $fmt_pos = 0;
    $time_string_pos = 0;

    while(($fmt_pos = strpos($fmt, '%', $fmt_pos)) !== false)
    {
      $current_time_char = $fmt{++$fmt_pos};

      if(($fmt_pos+1) >= $fmt_len)
        $delimiter_pos = $time_string_len;
      elseif($time_string_pos <= $time_string_len)
      {
        $current_delimiter = $fmt{++$fmt_pos};
        $delimiter_pos = strpos($time_string, $current_delimiter, $time_string_pos);
        if($delimiter_pos === false)
          $delimiter_pos = $time_string_len;
      }

      $delimiter_len = $delimiter_pos - $time_string_pos;

      $value = substr($time_string, $time_string_pos, $delimiter_len);

      if(preg_match("/[-\/]/", $value))
        throw new lmbException("Wrong date format: $time_string does not matches $fmt format");

      $time_array['%' . $current_time_char] = $value;

      $time_string_pos += ($delimiter_len + 1);
    }

    return $time_array;
  }

  function localStringToDate($locale, $string, $format = null)
  {
    if(!$format)
      $format = $locale->getShortDateFormat();

    $arr = self :: parseTimeString($locale, $string, $format);
    return new lmbDate($arr['hour'], $arr['minute'], $arr['second'], $arr['day'], $arr['month'], $arr['year']);
  }

  static function localStringToISO($locale, $string)
  {
    $date = self :: localStringToDate($locale, $string);
    return $date->toString();
  }

  static function localStringToStamp($locale, $string)
  {
    $date = self :: localStringToDate($locale, $string);
    return $date->toTimestamp();
  }

  static function ISOToLocalString($locale, $iso_date)
  {
    $date = new lmbDate($iso_date);
    $format = new lmbDateFormat();

    return $format->toString($date, $locale->getShortDateFormat());
  }

  static function stampToLocalString($locale, $stamp)
  {
    $date = new lmbDate((int)$stamp);
    $format = new lmbDateFormat();

    return $format->toString($date, $locale->getShortDateFormat());
  }

  static function isLocalStringValid($locale, $string)
  {
    try
    {
      lmbLocaleDate :: localStringToDate($locale, $string);
      return true;
    }
    catch(lmbException $e)
    {
      return false;
    }
  }

  static function localString($locale, $time = null, $format = null)
  {
    if(!$format)
      $format = $locale->getShortDateFormat();

    $date = new lmbDate($time);
    $format = new lmbDateFormat();

    return $format->toString($date, $locale->getShortDateFormat());
  }
}

?>
