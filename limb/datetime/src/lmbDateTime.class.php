<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbObject.class.php');

/**
 * class lmbDateTime.
 *
 * @package datetime
 * @version $Id: lmbDateTime.class.php 6532 2007-11-20 15:55:48Z serega $
 */
class lmbDateTime extends lmbObject
{
  const MINUTE = 60;
  const HOUR = 3600;
  const DAY = 86400;
  const WEEK = 604800;
  
  //YYYY-MM-DD HH:MM:SS timezone
  const DATE_ISO_REGEX = '~^(([0-9]{4})-([0-9]{2})-([0-9]{2}))?((?(1)\s+)([0-9]{2}):([0-9]{2}):?([0-9]{2})?)?$~';

  /**
   * Defines what day starts the week.
   * Monday (1) is the international standard, Sunday (0) is used in US.
   * @see setWeekStartsAt()
   */
  static protected $week_starts_at = 1;

  protected $year = 0;
  protected $month = 0;
  protected $day = 0;
  protected $hour = 0;
  protected $minute = 0;
  protected $second = 0;
  protected $tz = '';

  function __construct($year_or_date=null, $month_or_tz=null, $day=null, $hour=0, $minute=0, $second=0, $tz='')
  {
    if(func_num_args() > 2)
    {
      $this->year   = (int)$year_or_date;
      $this->month  = (int)$month_or_tz;
      $this->day    = (int)$day;
      $this->hour   = (int)$hour;
      $this->minute = (int)$minute;
      $this->second = (int)$second;
      $this->tz     = $tz;
    }
    elseif(is_a($year_or_date, 'lmbDateTime'))
    {
      $this->_copy($year_or_date);
    }
    elseif(is_numeric($year_or_date))
    {
      $this->_setByStamp($year_or_date);
      $this->tz = $month_or_tz;
    }
    elseif(is_string($year_or_date))
    {
      $this->_setByString($year_or_date);
      $this->tz = $month_or_tz;
    }
    else
    {
      $this->_setByStamp(time());
      $this->tz = $month_or_tz;
    }

    if(!$this->isValid())
    {
      $args = func_get_args();
      throw new lmbException("Could not create date using args", $args);
    }
  }

  /**
   * Wrapper around constructor
   * 
   * It can be useful since the following is not allowed in PHP 'new lmbDateTime(..)->addDay(..)->'
   *
   * @param integer $year_or_date
   * @param integer $month_or_tz
   * @param integer $day
   * @param integer $hour
   * @param integer $minute
   * @param integer $second
   * @param string $tz
   * @return lmbDateTime
   */
  static function create($year_or_date=null, $month_or_tz=null, $day=null, $hour=0, $minute=0, $second=0, $tz='')
  {
    if(func_num_args() > 2)
      return new self($year_or_date, $month_or_tz, $day, $hour, $minute, $second, $tz);
    else
      return new self($year_or_date, $month_or_tz);
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
    return new self($century . $year, $month, $day);
  }

  static function setWeekStartsAt($n)
  {
    self :: $week_starts_at = $n;
  }

  static function getWeekStartsAt()
  {
    return self :: $week_starts_at;
  }

  static function stampToIso($stamp)
  {
    $date = new self((int)$stamp);
    return $date->getIsoDate();
  }
  
  static function stampToShortIso($stamp)
  {
    $date = new $class((int)$stamp);
    return $date->getIsoShortDate();
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

  static function validate($year_or_date=null, $month_or_tz=null, $day=null, $hour=0, $minute=0, $second=0, $tz='')
  {
    try
    {
      if(func_num_args() > 2)
        new self($year_or_date, $month_or_tz, $day, $hour, $minute, $second, $tz);
      else
        new self($year_or_date, $month_or_tz);
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
    if(!$arr = @getdate($time))
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

  function getIsoShortTime()
  {
    return $this->getIsoTime(false);
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
    if(!$d instanceof lmbDateTime)
      throw new lmbException("Wrong date argument", array('arg' => $d));

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

  function isEqualDate($when)
  {
    return $this->stripTime()->isEqual($when->stripTime());
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
    return $this->_correctDayOfWeek($this->getPhpDayOfWeek(), self :: $week_starts_at);
  }

  function getIntlDayOfWeek()
  {
    return $this->_correctDayOfWeek($this->getPhpDayOfWeek(), 1);
  }

  function getPhpDayOfWeek()
  {
    $year = $this->year;
    $month = $this->month;
    $day = $this->day;

    if(1901 < $year && $year < 2038)
      return (int)date('w', mktime(0, 0, 0, $month, $day, $year));

    if($month > 2)
    {
      $month -= 2;
    }
    else
    {
      $month += 10;
      $year--;
    }

    $day = (floor((13 * $month - 1) / 5) +
            $day + ($year % 100) +
            floor(($year % 100) / 4) +
            floor(($year / 100) / 4) - 2 *
            floor($year / 100) + 77);

    return $day - 7 * floor($day / 7);
  }

  protected function _correctDayOfWeek($dow, $week_starts_at)
  {
    if($week_starts_at == 0)
      return $dow;

    if($dow == 0)
      return 6;
    return $dow - 1;
  }

  function getBeginOfDay()
  {
    $class = get_class($this);
    return new $class($this->year, $this->month, $this->day, $this->tz);
  }

  function getEndOfDay()
  {
    $class = get_class($this);
    return new $class($this->year, $this->month, $this->day, 23, 59, 59, $this->tz);
  }

  function getBeginOfWeek()
  {
    $this_weekday = $this->getPhpDayOfWeek();
    $interval = (7 - self :: $week_starts_at + $this_weekday) % 7;
    return self :: createByDays($this->getDateDays() - $interval);
  }

  function getEndOfWeek()
  {
    $this_weekday = $this->getPhpDayOfWeek();
    $interval = (6 + self :: $week_starts_at - $this_weekday) % 7;
    return self :: createByDays($this->getDateDays() + $interval);
  }

  function getBeginOfMonth()
  {
    $class = get_class($this);
    return new $class($this->year, $this->month, 1, $this->tz);
  }

  function getEndOfMonth()
  {
    return $this->setDay(1)->addMonth(1)->addDay(-1)->getEndOfDay();
  }

  function getBeginOfYear()
  {
    $class = get_class($this);
    return new $class($this->year, 1, 1, $this->tz);
  }

  function getEndOfYear()
  {
    $class = get_class($this);
    return new $class($this->year, 12, 31, 23, 59, 59, $this->tz);
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

    $dayofweek = $this->getPhpDayOfWeek();
    $firstday  = self :: create($year, 1, 1)->getPhpDayOfWeek();
    if(($month == 1) && (($firstday < 1) || ($firstday > 4)) && ($day < 4))
    {
      $firstday  = self :: create($year - 1, 1, 1)->getPhpDayOfWeek();
      $month     = 12;
      $day       = 31;
    }
    elseif(($month == 12) && ((self :: create($year + 1, 1, 1)->getPhpDayOfWeek() < 5) &&
            (self :: create($year + 1, 1, 1)->getPhpDayOfWeek() > 0)))
        return 1;

    return intval(((self :: create($year, 1, 1)->getPhpDayOfWeek() < 5) && (self :: create($year, 1, 1)->getPhpDayOfWeek() > 0)) +
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
    $class = get_class($this);
    return new $class($y, $this->month, $this->day, $this->hour, $this->minute, $this->second, $this->tz);
  }

  function setMonth($m)
  {
    $class = get_class($this);
    return new $class($this->year, $m, $this->day, $this->hour, $this->minute, $this->second, $this->tz);
  }

  function setDay($d)
  {
    $class = get_class($this);
    return new $class($this->year, $this->month, $d, $this->hour, $this->minute, $this->second, $this->tz);
  }

  function setHour($h)
  {
    $class = get_class($this);
    return new $class($this->year, $this->month, $this->day, $h, $this->minute, $this->second, $this->tz);
  }

  function setMinute($m)
  {
    $class = get_class($this);
    return new $class($this->year, $this->month, $this->day, $this->hour, $m, $this->second, $this->tz);
  }

  function setSecond($s)
  {
    $class = get_class($this);
    return new $class($this->year, $this->month, $this->day, $this->hour, $this->minute, $s, $this->tz);
  }

  function setTimeZone($tz)
  {
    $class = get_class($this);
    return new $class($this->year, $this->month, $this->day, $this->hour, $this->minute, $this->second, $tz);
  }

  function addYear($n=1)
  {
    $class = get_class($this);
    $date = new $class(mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year + $n));
    return $date->setTimeZone($this->tz);
  }

  function addMonth($n=1)
  {
    $class = get_class($this);
    $date = new $class(mktime($this->hour, $this->minute, $this->second, $this->month + $n, $this->day, $this->year));
    return $date->setTimeZone($this->tz);
  }

  function addWeek($n=1)
  {
    $class = get_class($this);
    $date = new $class(mktime($this->hour, $this->minute, $this->second, $this->month, $this->day + ($n * 7), $this->year));
    return $date->setTimeZone($this->tz);
  }

  function addDay($n=1)
  {
    $class = get_class($this);
    $date = new $class(mktime($this->hour, $this->minute, $this->second, $this->month, $this->day + $n, $this->year));
    return $date->setTimeZone($this->tz);
  }

  function addHour($n=1)
  {
    $class = get_class($this);
    $date = new $class(mktime($this->hour + $n, $this->minute, $this->second, $this->month, $this->day, $this->year));
    return $date->setTimeZone($this->tz);
  }

  function addMinute($n=1)
  {
    $class = get_class($this);
    $date = new $class(mktime($this->hour, $this->minute + $n, $this->second, $this->month, $this->day, $this->year));
    return $date->setTimeZone($this->tz);
  }

  function addSecond($n=1)
  {
    $class = get_class($this);
    $date = new $class(mktime($this->hour, $this->minute, $this->second + $n, $this->month, $this->day, $this->year));
    return $date->setTimeZone($this->tz);
  }

  function stripTime()
  {
    $class = get_class($this);
    return new $class($this->year, $this->month, $this->day, 0, 0, 0, $this->tz);
  }

  function stripDate()
  {
    $class = get_class($this);
    return new $class(null, null, null, $this->hour, $this->minute, $this->second, $this->tz);
  }
}

