<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/exception/lmbException.class.php');
lmb_require('limb/datetime/src/lmbDateTime.class.php');

/**
 * class lmbLocaleDateTime.
 *
 * @package i18n
 * @version $Id: lmbLocaleDateTime.class.php 6475 2007-10-31 09:30:46Z korchasa $
 */
class lmbLocaleDateTime extends lmbDateTime
{
  /**
   *  Formats the date in the given format according to locale settings, much like
   *  strftime().  Most strftime() attributes are supported.
   *
   *  %a    abbreviated weekday name (Sun, Mon, Tue)
   *  %A    full weekday name (Sunday, Monday, Tuesday)
   *  %b    abbreviated month name (Jan, Feb, Mar)
   *  %B    full month name (January, February, March)
   *  %C    century number (the year divided by 100 and truncated to an integer, range 00 to 99)
   *  %d    day of month (range 00 to 31)
   *  %D    same as "%m/%d/%y"
   *  %e    day of month, single digit (range 0 to 31)
   *  %E    number of days since unspecified epoch
   *  %H    hour as decimal number (00 to 23)
   *  %I    hour as decimal number on 12-hour clock (01 to 12)
   *  %j    day of year (range 001 to 366)
   *  %m    month as decimal number (range 01 to 12)
   *  %M    minute as a decimal number (00 to 59)
   *  %n    newline character (\n)
   *  %O    dst-corrected timezone offset expressed as "+/-HH:MM"
   *  %o    raw timezone offset expressed as "+/-HH:MM"
   *  %p    either 'am' or 'pm' depending on the time
   *  %P    either 'AM' or 'PM' depending on the time
   *  %r    time in am/pm notation, same as "%I:%M:%S %p"
   *  %R    time in 24-hour notation, same as "%H:%M"
   *  %S    seconds as a decimal number (00 to 59)
   *  %t    tab character (\t)
   *  %T    current time, same as "%H:%M:%S"
   *  %w    weekday as decimal (0 = Sunday)
   *  %U    week number of current year, first sunday as first week
   *  %y    year as decimal (range 00 to 99)
   *  %Y    year as decimal including century (range 0000 to 9999)
   *  %%    literal '%'
   */
  function localeStrftime($format, $locale=null)
  {
    $output = '';

    for($strpos=0; $strpos < strlen($format); $strpos++)
    {
      $char = substr($format, $strpos, 1);
      if($char != '%')
      {
        $output .= $char;
        continue;
      }

      $nextchar = substr($format, $strpos + 1, 1);
      switch($nextchar)
      {
        case 'a':
            self :: _ensureLocale($locale);
            $output .= $locale->getDayName($this->getPhpDayOfWeek(), true);
            break;
        case 'A':
            self :: _ensureLocale($locale);
            $output .= $locale->getDayName($this->getPhpDayOfWeek());
            break;
        case 'b':
            self :: _ensureLocale($locale);
            $output .= $locale->getMonthName($this->getMonth() - 1, true);
            break;
        case 'B':
            self :: _ensureLocale($locale);
            $output .= $locale->getMonthName($this->getMonth() - 1);
            break;
        case 'p':
            self :: _ensureLocale($locale);
            $output .= $locale->getMeridiemName($this->getHour());
            break;
        case 'P':
            self :: _ensureLocale($locale);
            $output .= $locale->getMeridiemName($this->getHour(), true);
            break;
        case 'C':
            $output .= sprintf("%02d", intval($this->getYear()/100));
            break;
        case 'd':
            $output .= sprintf("%02d", $this->getDay());
            break;
        case 'D':
            $output .= sprintf("%02d/%02d/%02d", $this->getMonth(), $this->getDay(), substr($this->getYear(), 2));
            break;
        case 'e':
            $output .= $this->getDay();
            break;
        case 'E':
            $output .= $this->getDateDays();
            break;
        case 'H':
            $output .= sprintf("%02d", $this->getHour());
            break;
        case 'I':
            $hour = ($this->getHour() + 1) > 12 ? $this->getHour() - 12 : $this->getHour();
            $output .= sprintf("%02d", $hour==0 ? 12 : $hour);
            break;
        case 'j':
            $output .= sprintf("%03d", $this->getDayOfYear());
            break;
        case 'm':
            $output .= sprintf("%02d",$this->getMonth());
            break;
        case 'M':
            $output .= sprintf("%02d",$this->getMinute());
            break;
        case 'n':
            $output .= "\n";
            break;
        case 'O':
            $offms = $this->getTimeZone()->getOffset($this);
            $direction = $offms >= 0 ? '+' : '-';
            $offmins = abs($offms) / 1000 / 60;
            $hours = $offmins / 60;
            $minutes = $offmins % 60;
            $output .= sprintf("%s%02d:%02d", $direction, $hours, $minutes);
            break;
        case 'o':
            $offms = $this->getTimeZone()->getRawOffset($this);
            $direction = $offms >= 0 ? '+' : '-';
            $offmins = abs($offms) / 1000 / 60;
            $hours = $offmins / 60;
            $minutes = $offmins % 60;
            $output .= sprintf("%s%02d:%02d", $direction, $hours, $minutes);
            break;
        case 'r':
            $hour = ($this->getHour() + 1) > 12 ? $this->getHour() - 12 : $this->getHour();
            $output .= sprintf("%02d:%02d:%02d %s", $hour==0 ?  12 : $hour, $this->getMinute(), $this->getSecond(), $this->getHour() >= 12 ? "PM" : "AM");
            break;
        case 'R':
            $output .= sprintf("%02d:%02d", $this->getHour(), $this->getMinute());
            break;
        case 'S':
            $output .= sprintf("%02d", $this->getSecond());
            break;
        case 't':
            $output .= "\t";
            break;
        case 'T':
            $output .= sprintf("%02d:%02d:%02d", $this->getHour(), $this->getMinute(), $this->getSecond());
            break;
        case 'w':
            $output .= $this->getPhpDayOfWeek();
            break;
        case 'U':
            $output .= $this->getWeekOfYear();
            break;
        case 'y':
            $output .= substr($this->getYear() . '', 2);
            break;
        case 'Y':
            $output .= sprintf("%04d", $this->getYear());
            break;
        case 'Z':
            $output .= $this->getTimeZone()->isInDaylightTime() ? $this->getTimeZone()->getDSTShortName() : $this->getTimeZone()->getShortName();
            break;
        case '%':
            $output .= '%';
            break;
        default:
            $output .= $char . $nextchar;
      }
      $strpos++;
    }
    return $output;
  }
  
  static function create($year_or_date=null, $month_or_tz=null, $day=null, $hour=0, $minute=0, $second=0, $tz='')
  {
    if(func_num_args() > 2)
      return new lmbLocaleDateTime($year_or_date, $month_or_tz, $day, $hour, $minute, $second, $tz);
    else
      return new lmbLocaleDateTime($year_or_date, $month_or_tz);
  }
  
  function getShortDateFormatted($locale = null)
  {
    self :: _ensureLocale($locale);
    return $this->localeStrftime($locale->short_date_format, $locale);
  }
  
  function getShortDateTimeFormatted($locale = null)
  {
    self :: _ensureLocale($locale);
    return $this->localeStrftime($locale->short_date_time_format, $locale);
  }
  
  static function createFromShortDateFormat($string, $locale = null)
  {
    self :: _ensureLocale($locale);
    return self :: localStringToDate($locale, $string, $locale->short_date_format);
  }

  static function createFromShortDateTimeFormat($string, $locale = null)
  {
    self :: _ensureLocale($locale);
    return self :: localStringToDate($locale, $string, $locale->short_date_time_format);
  }

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

  static protected function _ensureLocale(&$locale)
  {
    if(!is_object($locale))
      $locale = lmbToolkit :: instance()->getLocaleObject();
  }

  static function localStringToDate($locale, $string, $format = null)
  {
    if(!$format)
      $format = $locale->getShortDateFormat();

    $arr = self :: parseTimeString($locale, $string, $format);
    return new lmbLocaleDateTime($arr['year'], $arr['month'], $arr['day'], $arr['hour'], $arr['minute'], $arr['second']);
  }

  static function isLocalStringValid($locale, $string, $format = null)
  {
    try
    {
      lmbLocaleDateTime :: localStringToDate($locale, $string, $format);
      return true;
    }
    catch(lmbException $e)
    {
      return false;
    }
  }
}


