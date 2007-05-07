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

class lmbMonthCalendar
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
    $dof_start = $this->start_date->getDayOfWeek();    
    $dof_end = $this->end_date->getDayOfWeek() + 1;//fix it???
    
    $days = ($this->getNumberOfDays() - (7 - $dof_start) - $dof_end);        
    return ((int)$days / 7) + 1 + (($dof_end > 0) ? 1 : 0);
  }
}
?>