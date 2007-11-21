<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/datetime/src/lmbDateTime.class.php');
lmb_require('limb/datetime/src/lmbDateTimePeriod.class.php');

/**
 * class lmbTimePeriod.
 *
 * @package datetime
 * @version $Id: lmbTimePeriod.class.php 6533 2007-11-21 20:03:24Z pachanga $
 */
class lmbTimePeriod extends lmbDateTimePeriod
{
  function __construct($start, $end)
  {
    $start_date = new lmbDateTime($start);
    $end_date = new lmbDateTime($end);

    parent :: __construct($start_date->stripDate(), $end_date->stripDate());
  }

  function getDatePeriod($date)
  {
    $date = new lmbDateTime($date);
    $year = $date->getYear();
    $month = $date->getMonth();
    $day = $date->getDay();

    $start_date = new lmbDateTime($year, $month, $day,
                              $this->start->getHour(), $this->start->getMinute(), $this->start->getSecond());

    $end_date = new lmbDateTime($year, $month, $day,
                            $this->end->getHour(), $this->end->getMinute(), $this->end->getSecond());

    return new lmbDateTimePeriod($start_date, $end_date);
  }
}


