<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/datetime/src/lmbDateTime.class.php');

/**
 * class lmbMonth.
 *
 * @package datetime
 * @version $Id$
 */
class lmbMonth
{
  protected $start_date;
  protected $end_date;

  function __construct($year_or_date = null, $month = null)
  {
    if($year_or_date && $month)
      $tmp_date = new lmbDateTime($year_or_date, $month, 1);
    elseif($year_or_date && !$month)
      $tmp_date = new lmbDateTime($year_or_date);
    else
      $tmp_date = new lmbDateTime();

    $this->start_date = $tmp_date->getBeginOfMonth();
    $this->end_date = $tmp_date->getEndOfMonth();
  }

  function getMonth()
  {
    return $this->start_date->getMonth();
  }

  function getMonthName()
  {
    return $this->start_date->date('M');
  }

  function getMonthShortName()
  {
    return $this->start_date->date('F');
  }

  function getYear()
  {
    return $this->start_date->getYear();
  }

  function getStartDate()
  {
    return $this->start_date;
  }

  function getEndDate()
  {
    return $this->end_date;
  }

  function getNumberOfDays()
  {
    return $this->end_date->getDay();
  }

  function getNumberOfWeeks()
  {
    $dow = $this->start_date->getPhpDayOfWeek();

    if(lmbDateTime :: getWeekStartsAt() == 1 && $dow == 0)
    {
      $first_week_days = 7 - $dow + lmbDateTime :: getWeekStartsAt();
      $weeks = 1;
    }
    elseif(lmbDateTime :: getWeekStartsAt() == 0 && $dow == 6)
    {
      $first_week_days = 7 - $dow + lmbDateTime :: getWeekStartsAt();
      $weeks = 1;
    }
    else
    {
      $first_week_days = lmbDateTime :: getWeekStartsAt() - $dow;
      $weeks = 0;
    }

    $first_week_days %= 7;
    return ceil(($this->getNumberOfDays() - $first_week_days) / 7) + $weeks;
  }

  function getWeek($n)
  {
    if($n < 0 || $n > $this->getNumberOfWeeks()-1)
      return null;

    $week_array = array();
    $curr_day = ($n * 7) + $this->start_date->getBeginOfWeek()->getDateDays();

    for($i=0;$i<=6;$i++)
    {
      $week_array[$i] = lmbDateTime :: createByDays($curr_day);
      $curr_day++;
    }
    return $week_array;
  }

  function getAllWeeks()
  {
    $weeks = array();
    for($i = 0; $i < $this->getNumberOfWeeks(); $i++)
      $weeks[$i] = $this->getWeek($i);
    return $weeks;
  }

  function getNextMonth()
  {
    $date = $this->end_date->addDay(1);
    return new lmbMonth($date->getYear(), $date->getMonth());
  }

  function getPrevMonth()
  {
    $date = $this->start_date->addDay(-1);
    return new lmbMonth($date->getYear(), $date->getMonth());
  }
}

