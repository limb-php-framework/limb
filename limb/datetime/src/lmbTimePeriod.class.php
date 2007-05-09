<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTimePeriod.class.php 5847 2007-05-09 12:29:32Z pachanga $
 * @package    datetime
 */
lmb_require('limb/datetime/src/lmbDate.class.php');
lmb_require('limb/datetime/src/lmbDatePeriod.class.php');

class lmbTimePeriod extends lmbDatePeriod
{
  function __construct($start, $end)
  {
    $start_date = new lmbDate($start);
    $end_date = new lmbDate($end);

    parent :: __construct($start_date->setYear(0)->setMonth(0)->setDay(0),
                          $end_date->setYear(0)->setMonth(0)->setDay(0));
  }

  function getDatePeriod($date)
  {
    $date = new lmbDate($date);
    $year = $date->getYear();
    $month = $date->getMonth();
    $day = $date->getDay();

    $start_date = new lmbDate($year, $month, $day,
                              $this->start->getHour(), $this->start->getMinute(), $this->start->getSecond());

    $end_date = new lmbDate($year, $month, $day,
                            $this->end->getHour(), $this->end->getMinute(), $this->end->getSecond());

    return new lmbDatePeriod($start_date, $end_date);
  }
}

?>
