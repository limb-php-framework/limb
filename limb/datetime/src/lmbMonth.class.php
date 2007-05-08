<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDate.class.php 5824 2007-05-07 13:44:24Z pachanga $
 * @package    datetime
 */
lmb_require('limb/datetime/src/lmbDate.class.php');

class lmbMonth
{
  protected $start_date;
  protected $end_date;

  function __construct($year, $month)
  {
    $tmp_date = new lmbDate(0, 0, 0, 0, $month, $year);
    $this->start_date = $tmp_date->setDay(1);
    $this->end_date = $this->start_date->addMonth(1)->addDay(-1);
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
    $dof = $this->start_date->getDayOfWeek();
    if(lmbDate :: getFirstDayOfWeek() == 1 && $dof == 0)
    {
      $first_week_days = 7 - $dof + lmbDate :: getFirstDayOfWeek();
      $weeks = 1;
    }
    elseif(lmbDate :: getFirstDayOfWeek() == 0 && $dof == 6)
    {
      $first_week_days = 7 - $dof + lmbDate :: getFirstDayOfWeek();
      $weeks = 1;
    }
    else
    {
      $first_week_days = lmbDate :: getFirstDayOfWeek() - $dof;
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
      $week_array[$i] = lmbDate :: createByDays($curr_day);
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
?>