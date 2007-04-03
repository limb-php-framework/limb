<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDateFormat.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */

class lmbDateFormat
{
  /**
   *  date pretty printing, similar to strftime()
   *
   *  Formats the date in the given format, much like
   *  strftime().  Most strftime() attributes are supported.
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
  function toString($date, $format, $locale=null)
  {
    $output = '';

    for($strpos = 0; $strpos < strlen($format); $strpos++)
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
            $this->_assertLocale($locale);
            $output .= $locale->getDayName($date->getDayOfWeek(), true);
            break;
        case 'A':
            $this->_assertLocale($locale);
            $output .= $locale->getDayName($date->getDayOfWeek());
            break;
        case 'b':
            $this->_assertLocale($locale);
            $output .= $locale->getMonthName($date->getMonth() - 1, true);
            break;
        case 'B':
            $this->_assertLocale($locale);
            $output .= $locale->getMonthName($date->getMonth() - 1);
            break;
        case 'p':
            $this->_assertLocale($locale);
            $output .= $locale->getMeridiemName($date->getHour());
            break;
        case 'P':
            $this->_assertLocale($locale);
            $output .= $locale->getMeridiemName($date->getHour(), true);
            break;
        case 'C':
            $output .= sprintf("%02d", intval($date->getYear()/100));
            break;
        case 'd':
            $output .= sprintf("%02d", $date->getDay());
            break;
        case 'D':
            $output .= sprintf("%02d/%02d/%02d", $date->getMonth(), $date->getDay(), substr($date->getYear(), 2));
            break;
        case 'e':
            $output .= $date->getDay();
            break;
        case 'E':
            $output .= $date->dateToDays();
            break;
        case 'H':
            $output .= sprintf("%02d", $date->getHour());
            break;
        case 'I':
            $hour = ($date->getHour() + 1) > 12 ? $date->getHour() - 12 : $date->getHour();
            $output .= sprintf("%02d", $hour==0 ? 12 : $hour);
            break;
        case 'j':
            $output .= sprintf("%03d", $date->getDayOfYear());
            break;
        case 'm':
            $output .= sprintf("%02d",$date->getMonth());
            break;
        case 'M':
            $output .= sprintf("%02d",$date->getMinute());
            break;
        case 'n':
            $output .= "\n";
            break;
        case 'O':
            $offms = $date->getTimeZone()->getOffset($this);
            $direction = $offms >= 0 ? '+' : '-';
            $offmins = abs($offms) / 1000 / 60;
            $hours = $offmins / 60;
            $minutes = $offmins % 60;
            $output .= sprintf("%s%02d:%02d", $direction, $hours, $minutes);
            break;
        case 'o':
            $offms = $date->getTimeZone()->getRawOffset($this);
            $direction = $offms >= 0 ? '+' : '-';
            $offmins = abs($offms) / 1000 / 60;
            $hours = $offmins / 60;
            $minutes = $offmins % 60;
            $output .= sprintf("%s%02d:%02d", $direction, $hours, $minutes);
            break;
        case 'r':
            $hour = ($date->getHour() + 1) > 12 ? $date->getHour() - 12 : $date->getHour();
            $output .= sprintf("%02d:%02d:%02d %s", $hour==0 ?  12 : $hour, $date->getMinute(), $date->getSecond(), $date->getHour() >= 12 ? "PM" : "AM");
            break;
        case 'R':
            $output .= sprintf("%02d:%02d", $date->getHour(), $date->getMinute());
            break;
        case 'S':
            $output .= sprintf("%02d", $date->getSecond());
            break;
        case 't':
            $output .= "\t";
            break;
        case 'T':
            $output .= sprintf("%02d:%02d:%02d", $date->getHour(), $date->getMinute(), $date->getSecond());
            break;
        case 'w':
            $output .= $date->getDayOfWeek();
            break;
        case 'U':
            $output .= $date->getWeekOfYear();
            break;
        case 'y':
            $output .= substr($date->getYear() . '', 2);
            break;
        case 'Y':
            $output .= sprintf("%04d", $date->getYear());
            break;
        case 'Z':
            $output .= $date->getTimeZone()->isInDaylightTime() ? $date->getTimeZone()->getDSTShortName() : $date->getTimeZone()->getShortName();
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

  function _assertLocale($locale)
  {
    if(!is_object($locale))
      throw new lmbException('locale object is required for this type of formatting');
  }

  function toISO($date)
  {
    //YYYY-MM-DD HH:MM:SS
    return $this->toString($date, "%Y-%m-%d %T", null);
  }

  function toDateISO($date)
  {
    //YYYY-MM-DD
    return $this->toString($date, "%Y-%m-%d", null);
  }

  function toTimeISO($date)
  {
    //YYYY-MM-DD
    return $this->toString($date, "%T", null);
  }
}
?>