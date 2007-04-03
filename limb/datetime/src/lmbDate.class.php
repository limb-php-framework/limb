<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDate.class.php 4993 2007-02-08 15:35:44Z pachanga $
 * @package    datetime
 */
lmb_require('limb/classkit/src/lmbObject.class.php');

class lmbDate extends lmbObject
{
  //YYYY-MM-DD HH:MM:SS timezone
  const DATE_STRING_REGEX = '~^(([0-9]{4})-([0-9]{2})-([0-9]{2}))?((?(1)\s+)([0-9]{2}):([0-9]{2}):?([0-9]{2})?)?$~';
  const DATE_ISO_FORMAT = "%04d-%02d-%02d %02d:%02d:%02d";

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
      throw new lmbException('could not create date', array($args));
    }
  }

  static function create($hour_or_date=null, $minute_or_tz=0, $second=0, $day=0, $month=0, $year=0, $tz='')
  {
    if(func_num_args() > 2)
      return new lmbDate($hour_or_date, $minute_or_tz, $second, $day, $month, $year, $tz);
    else
      return new lmbDate($hour_or_date, $minute_or_tz);
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

  protected function _setByString($string)
  {
    if(!preg_match(self :: DATE_STRING_REGEX, trim($string), $regs))
      throw new lmbException('could not setup date using string', array('string' => $string));

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
      throw new lmbException('could not setup date using stamp', array('stamp' => $time));

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

  function getISODate()
  {
    return sprintf(self :: DATE_ISO_FORMAT,
                   $this->getYear(), $this->getMonth(), $this->getDay(),
                   $this->getHour(), $this->getMinute(), $this->getSecond());
  }

  //obsolete?
  function toTimestamp()
  {
    return $this->getStamp();
  }

  function toString()
  {
    return $this->getISODate();
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

    if($s1 > $s2) return 1;
    elseif($s2 > $s1)  return -1;
    else return 0;
  }

  function isBefore($when, $use_time_zone=false)
  {
    if ($this->compare($when, $use_time_zone) == -1)
      return true;
    else
      return false;
  }

  function isAfter($when, $use_time_zone=false)
  {
    if ($this->compare($when, $use_time_zone) == 1)
      return true;
    else
      return false;
  }

  function isEqual($when, $use_time_zone=false)
  {
    if ($this->compare($when, $use_time_zone) == 0)
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

    if ($this->month > 2 &&  $this->isLeapYear())
      $julian++;

    return $julian;
  }

  function getDayOfWeek()
  {
    $year = $this->year;
    $month = $this->month;
    $day = $this->day;

    if($month > 2)
    {
      $month -= 2;
    }
    else
    {
      $month += 10;
      $year--;
    }

    $day = ( floor((13 * $month - 1) / 5) +
        $day + ($year % 100) +
        floor(($year % 100) / 4) +
        floor(($year / 100) / 4) - 2 *
        floor($year / 100) + 77);

    $weekday_number = (($day - 7 * floor($day / 7)));

    return $weekday_number - 1;
  }

  function getWeekOfYear()
  {
    $day = $this->day;
    $month = $this->month;
    $year = $this->year;

    $mnth = array (0,31,59,90,120,151,181,212,243,273,304,334);
    $y_isleap = $this->isLeapYear();
    $d = new lmbDate($this);
    $d = $d->setYear($year - 1);
    $y_1_isleap = $d->isLeapYear();

    $day_of_year_number = $day + $mnth[$month - 1];
    if ($y_isleap && $month > 2)
      $day_of_year_number++;

    // find Jan 1 weekday (monday = 1, sunday = 7)
    $yy = ($year - 1) % 100;
    $c = ($year - 1) - $yy;
    $g = $yy + intval($yy/4);
    $jan1_weekday = 1 + intval((((($c / 100) % 4) * 5) + $g) % 7);
    // weekday for year-month-day
    $h = $day_of_year_number + ($jan1_weekday - 1);
    $weekday = 1 + intval(($h - 1) % 7);
    // find if Y M D falls in YearNumber Y-1, WeekNumber 52 or
    if ($day_of_year_number <= (8 - $jan1_weekday) && $jan1_weekday > 4)
    {
      $yearnumber = $year - 1;
      if ($jan1_weekday == 5 || ($jan1_weekday == 6 && $y_1_isleap))
        $weeknumber = 53;
      else
        $weeknumber = 52;
    }
    else
      $yearnumber = $year;
    // find if Y M D falls in YearNumber Y+1, WeekNumber 1
    if ($yearnumber == $year)
    {
      if ($y_isleap)
        $i = 366;
      else
        $i = 365;

      if (($i - $day_of_year_number) < (4 - $weekday))
      {
        $yearnumber++;
        $weeknumber = 1;
      }
    }
    // find if Y M D falls in YearNumber Y, WeekNumber 1 through 53
    if ($yearnumber == $year)
    {
      $j = $day_of_year_number + (7 - $weekday) + ($jan1_weekday - 1);
      $weeknumber = intval($j / 7);
      if ($jan1_weekday > 4)
        $weeknumber--;
    }
   return $weeknumber;
  }

  function dateToDays()
  {
    $century = (int) substr("{$this->year}", 0, 2);
    $year = (int) substr("{$this->year}", 2, 2);
    $month = $this->month;
    $day = $this->day;

    if ($month > 2)
      $month -= 3;
    else
    {
      $month += 9;
      if ($year)
        $year--;
      else
      {
        $year = 99;
        $century --;
      }
    }
    return (
        floor(( 146097 * $century) / 4 ) +
        floor(( 1461 * $year) / 4 ) +
        floor(( 153 * $month + 2) / 5 ) +
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