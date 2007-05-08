<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDate.class.php 5837 2007-05-08 14:19:37Z pachanga $
 * @package    datetime
 */
lmb_require('limb/core/src/lmbObject.class.php');

define('LIMB_DATE_FIRST_WEEKDAY', 1);

class lmbDate extends lmbObject
{
  //YYYY-MM-DD HH:MM:SS timezone
  const DATE_ISO_REGEX = '~^(([0-9]{4})-([0-9]{2})-([0-9]{2}))?((?(1)\s+)([0-9]{2}):([0-9]{2}):?([0-9]{2})?)?$~';

  static protected $first_day_week = 1;

  protected $year = 0;
  protected $month = 0;
  protected $day = 0;
  protected $hour = 0;
  protected $minute = 0;
  protected $second = 0;
  protected $tz = '';

  function __construct($hour_or_date=null, $minute_or_tz=0, $second=0, $day=0, $month=0, $year=0, $tz='')
  {
    if(func_num_args() > 2)
    {
      $this->hour   = (int)$hour_or_date;
      $this->minute = (int)$minute_or_tz;
      $this->second = (int)$second;
      $this->day    = (int)$day;
      $this->month  = (int)$month;
      $this->year   = (int)$year;
      $this->tz     = $tz;
    }
    elseif(is_a($hour_or_date, 'lmbDate'))
    {
      $this->_copy($hour_or_date);
    }
    elseif(is_numeric($hour_or_date))
    {
      $this->_setByStamp($hour_or_date);
      $this->tz = $minute_or_tz;
    }
    elseif(is_string($hour_or_date))
    {
      $this->_setByString($hour_or_date);
      $this->tz = $minute_or_tz;
    }
    else
    {
      $this->_setByStamp(time());
      $this->tz = $minute_or_tz;
    }

    if(!$this->isValid())
    {
      $args = func_get_args();
      throw new lmbException("Could not create date using args", $args);
    }
  }

  /**
   * Wrapper around constructor, it can be useful since the following is not allowed in PHP 'new lmbDate(..)->addDay(..)->'
   */
  static function create($hour_or_date=null, $minute_or_tz=0, $second=0, $day=0, $month=0, $year=0, $tz='')
  {
    if(func_num_args() > 2)
      return new lmbDate($hour_or_date, $minute_or_tz, $second, $day, $month, $year, $tz);
    else
      return new lmbDate($hour_or_date, $minute_or_tz);
  }

  static function createWithoutTime($year=null, $month=null, $day=null, $tz='')
  {
    if(!$year && !$month && !$day)
    {
      $date = new lmbDate();
      return $date->setSecond(0)->setMinute(0)->setHour(0);
    }
    else
      return new lmbDate(0, 0, 0, $day, $month, $year, $tz);
  }

  static function createByDays($days)
  {
    $days   -= 1721119;
    $century = floor((4 * $days - 1) / 146097);
    $days    = floor(4 * $days - 1 - 146097 * $century);
    $day     = floor($days / 4);

    $year    = floor((4 * $day +  3) / 1461);
    $day     = floor(4 * $day +  3 - 1461 * $year);
    $day     = floor(($day +  4) / 4);

    $month   = floor((5 * $day - 3) / 153);
    $day     = floor(5 * $day - 3 - 153 * $month);
    $day     = floor(($day +  5) /  5);

    if($month < 10)
    {
      $month +=3;
    }
    else
    {
      $month -=9;
      if($year++ == 99)
      {
        $year = 0;
        $century++;
      }
    }

    $century = sprintf('%02d', $century);
    $year    = sprintf('%02d', $year);
    return new lmbDate(0, 0, 0, $day, $month, $century . $year);
  }

  static function setFirstDayOfWeek($n)
  {
    self :: $first_day_week = $n;
  }

  static function getFirstDayOfWeek()
  {
    return self :: $first_day_week;
  }

  static function stampToIso($stamp)
  {
    $date = new lmbDate((int)$stamp);
    return $date->getIsoDate();
  }

  function _createTimeZoneObject($code=null)
  {
    lmb_require('limb/datetime/src/lmbDateTimeZone.class.php');

    if(!$code)
      return lmbDateTimeZone::getDefault();
    else
      return new lmbDateTimeZone($code);
  }

  function isValid()
  {
    if($this->year < 0) return false;
    if($this->month < 0 || $this->month > 12) return false;
    if($this->day < 0 || $this->day > 31) return false;
    if($this->hour < 0 || $this->hour > 23) return false;
    if($this->minute < 0 || $this->minute > 59) return false;
    if($this->second < 0 || $this->second > 59) return false;

    //dirty hack for checkdate...
    return checkdate($this->month ? $this->month : 1,
                     $this->day ? $this->day : 1,
                     $this->year ? $this->year : 1);
  }

  static function isValidDateString($value)
  {
    try
    {
      new lmbDate((string)$value);
      return true;
    }
    catch(lmbException $e)
    {
      return false;
    }
  }

  protected function _setByString($string)
  {
    if(!preg_match(self :: DATE_ISO_REGEX, trim($string), $regs))
      throw new lmbException("Could not setup date using string '$string'");

    if(isset($regs[1]))
    {
      $this->year   = (int)$regs[2];
      $this->month  = (int)$regs[3];
      $this->day    = (int)$regs[4];
    }

    if(isset($regs[5]))
    {
      $this->hour   = (int)$regs[6];
      $this->minute = (int)$regs[7];
      if(isset($regs[8]))
        $this->second = (int)$regs[8];
    }
  }

  protected function _setByStamp($time)
  {
    if($time < 0 || !$arr = @getdate($time))
      throw new lmbException("Could not setup date using stamp'$time'");

    $this->year   = $arr['year'];
    $this->month  = $arr['mon'];
    $this->day    = $arr['mday'];
    $this->hour   = $arr['hours'];
    $this->minute = $arr['minutes'];
    $this->second = $arr['seconds'];
  }

  protected function _copy($date)
  {
    $this->year = $date->getYear();
    $this->month = $date->getMonth();
    $this->day = $date->getDay();
    $this->hour = $date->getHour();
    $this->minute = $date->getMinute();
    $this->second = $date->getSecond();
    $this->tz = $date->getTimeZone();
  }

  function getStamp()
  {
    //temporary ugly hack for unspecified year
    if(!$this->year)
      return mktime($this->hour, $this->minute, $this->second, $this->month, $this->day);
    else
      return mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year);
  }

  function date($format)
  {
    return date($format, $this->getStamp());
  }

  function strftime($format)
  {
    return strftime($format, $this->getStamp());
  }

  function getIsoDate($with_seconds = true)
  {
    return sprintf($with_seconds ? '%04d-%02d-%02d %02d:%02d:%02d' : '%04d-%02d-%02d %02d:%02d',
                   $this->getYear(), $this->getMonth(), $this->getDay(),
                   $this->getHour(), $this->getMinute(), $this->getSecond());
  }

  function getIsoShortDate()
  {
    return sprintf('%04d-%02d-%02d',
                   $this->getYear(), $this->getMonth(), $this->getDay());
  }

  function getIsoTime($with_seconds = true)
  {
    return sprintf($with_seconds ? '%02d:%02d:%02d' : '%02d:%02d',
                   $this->getHour(), $this->getMinute(), $this->getSecond());
  }

  /**
   * @deprecated
   */
  function toTimestamp()
  {
    return $this->getStamp();
  }

  function toString()
  {
    return $this->getIsoDate();
  }

  function isInDaylightTime()
  {
    return $this->getTimeZoneObject()->inDaylightTime($this);
  }

  function toUTC()
  {
    $tz = $this->getTimeZoneObject();

    if($tz->getOffset($this) > 0)
      $date = $this->addSecond(-1 * intval($tz->getOffset($this) / 1000));
    else
      $date = $this->addSecond(intval(abs($tz->getOffset($this)) / 1000));

    return $date->setTimeZone('UTC');
  }

  /**
   * Compares object with $d date object.
   * return int 0 if the dates are equal, -1 if is before, 1 if is after than $d
   */
  function compare($d)
  {
    $s1 = $this->getStamp();
    $s2 = $d->getStamp();

    if($s1 > $s2)
      return 1;
    elseif($s2 > $s1)
      return -1;
    else
      return 0;
  }

  function isBefore($when, $use_time_zone=false)
  {
    if($this->compare($when, $use_time_zone) == -1)
      return true;
    else
      return false;
  }

  function isAfter($when, $use_time_zone=false)
  {
    if($this->compare($when, $use_time_zone) == 1)
      return true;
    else
      return false;
  }

  function isEqual($when, $use_time_zone=false)
  {
    if($this->compare($when, $use_time_zone) == 0)
      return true;
    else
      return false;
  }

  function isLeapYear()
  {
    return (($this->year % 4 == 0 &&  $this->year % 100 != 0) ||  $this->year % 400 == 0);
  }

  function getDayOfYear()
  {
    $days = array(0,31,59,90,120,151,181,212,243,273,304,334);

    $julian = ($days[$this->month - 1] + $this->day);

    if($this->month > 2 &&  $this->isLeapYear())
      $julian++;

    return $julian;
  }

  function getDayOfWeek()
  {
    $year = $this->year;
    $month = $this->month;
    $day = $this->day;

    if(1901 < $year && $year < 2038)
      return (int)date('w', mktime(0, 0, 0, $month, $day, $year));

    //gregorian correction
    $correction = 0;
    if(($year < 1582) || (($year == 1582) || (($month < 10) || (($month == 10) || ($day < 15)))))
      $correction = 3;

    if($month > 2)
    {
      $month -= 2;
    }
    else
    {
      $month += 10;
      $year--;
    }

    $day  = floor((13 * $month - 1) / 5) + $day + ($year % 100) + floor(($year % 100) / 4);
    $day += floor(($year / 100) / 4) - 2 * floor($year / 100) + 77 + $correction;
    return (int)($day - 7 * floor($day / 7));
  }

  function getBeginOfWeek()
  {
    $this_weekday = $this->getDayOfWeek();
    $interval = (7 - self :: $first_day_week + $this_weekday) % 7;
    return lmbDate :: createByDays($this->getDateDays() - $interval);
  }

  function getEndOfWeek()
  {
    $this_weekday = $this->getDayOfWeek();
    $interval = (6 + self :: $first_day_week - $this_weekday) % 7;
    return lmbDate :: createByDays($this->getDateDays() + $interval);
  }

  function getWeekOfYear()
  {
    $day = $this->day;
    $month = $this->month;
    $year = $this->year;

    if((1901 < $year) && ($year < 2038))
    {
      $res  = (int)date('W', mktime(0, 0, 0, $month, $day, $year));
      if($res > 52) //bug in PHP date???
        return $res % 52;
      return $res;
    }

    $dayofweek = $this->getDayOfWeek();
    $firstday  = self :: createWithoutTime($year, 1, 1)->getDayOfWeek();
    if(($month == 1) && (($firstday < 1) || ($firstday > 4)) && ($day < 4))
    {
      $firstday  = self :: createWithoutTime($year - 1, 1, 1)->getDayOfWeek();
      $month     = 12;
      $day       = 31;
    }
    elseif(($month == 12) && ((self :: createWithoutTime($year + 1, 1, 1)->getDayOfWeek() < 5) &&
            (self :: createWithoutTime($year + 1, 1, 1)->getDayOfWeek() > 0)))
        return 1;

    return intval(((self :: createWithoutTime($year, 1, 1)->getDayOfWeek() < 5) && (self :: createWithoutTime($year, 1, 1)->getDayOfWeek() > 0)) +
           4 * ($month - 1) + (2 * ($month - 1) + ($day - 1) + $firstday - $dayofweek + 6) * 36 / 256);
  }

  function getDateDays()
  {
    $century = (int)substr("{$this->year}", 0, 2);
    $year = (int)substr("{$this->year}", 2, 2);
    $month = $this->month;
    $day = $this->day;

    if($month > 2)
      $month -= 3;
    else
    {
      $month += 9;
      if($year)
        $year--;
      else
      {
        $year = 99;
        $century --;
      }
    }
    return (
        floor((146097 * $century) / 4) +
        floor((1461 * $year) / 4) +
        floor((153 * $month + 2) / 5) +
        $day + 1721119);
  }

  function getYear()
  {
    return $this->year;
  }

  function getMonth()
  {
    return $this->month;
  }

  function getDay()
  {
    return $this->day;
  }

  function getHour()
  {
    return $this->hour;
  }

  function getMinute()
  {
    return $this->minute;
  }

  function getSecond()
  {
    return $this->second;
  }

  function getTimeZoneObject()
  {
    return $this->_createTimeZoneObject($this->tz);
  }

  function getTimeZone()
  {
    return $this->tz;
  }

  function setYear($y)
  {
    return new lmbDate($this->hour, $this->minute, $this->second, $this->day, $this->month, $y, $this->tz);
  }

  function setMonth($m)
  {
    return new lmbDate($this->hour, $this->minute, $this->second, $this->day, $m, $this->year, $this->tz);
  }

  function setDay($d)
  {
    return new lmbDate($this->hour, $this->minute, $this->second, $d, $this->month, $this->year, $this->tz);
  }

  function setHour($h)
  {
    return new lmbDate($h, $this->minute, $this->second, $this->day, $this->month, $this->year, $this->tz);
  }

  function setMinute($m)
  {
    return new lmbDate($this->hour, $m, $this->second, $this->day, $this->month, $this->year, $this->tz);
  }

  function setSecond($s)
  {
    return new lmbDate($this->hour, $this->minute, $s, $this->day, $this->month, $this->year, $this->tz);
  }

  function setTimeZone($tz)
  {
    return new lmbDate($this->hour, $this->minute, $this->second, $this->day, $this->month, $this->year, $tz);
  }

  function addYear($n=1)
  {
    $date = new lmbDate(mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year + $n));
    return $date->setTimeZone($this->tz);
  }

  function addMonth($n=1)
  {
    $date = new lmbDate(mktime($this->hour, $this->minute, $this->second, $this->month + $n, $this->day, $this->year));
    return $date->setTimeZone($this->tz);
  }

  function addWeek($n=1)
  {
    $date = new lmbDate(mktime($this->hour, $this->minute, $this->second, $this->month, $this->day + ($n * 7), $this->year));
    return $date->setTimeZone($this->tz);
  }

  function addDay($n=1)
  {
    $date = new lmbDate(mktime($this->hour, $this->minute, $this->second, $this->month, $this->day + $n, $this->year));
    return $date->setTimeZone($this->tz);
  }

  function addHour($n=1)
  {
    $date = new lmbDate(mktime($this->hour + $n, $this->minute, $this->second, $this->month, $this->day, $this->year));
    return $date->setTimeZone($this->tz);
  }

  function addMinute($n=1)
  {
    $date = new lmbDate(mktime($this->hour, $this->minute + $n, $this->second, $this->month, $this->day, $this->year));
    return $date->setTimeZone($this->tz);
  }

  function addSecond($n=1)
  {
    $date = new lmbDate(mktime($this->hour, $this->minute, $this->second + $n, $this->month, $this->day, $this->year));
    return $date->setTimeZone($this->tz);
  }
}
?>