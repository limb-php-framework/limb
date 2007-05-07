<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDateTest.class.php 5824 2007-05-07 13:44:24Z pachanga $
 * @package    datetime
 */
lmb_require('limb/datetime/src/lmbMonthCalendar.class.php');

class lmbMonthCalendarTest extends UnitTestCase
{
  function testGetBoundaries()
  {
    $c = new lmbMonthCalendar(2007, 5);
    $this->assertEqual(new lmbDate('2007-05-01'), $c->getStartDate());
    $this->assertEqual(new lmbDate('2007-05-31'), $c->getEndDate());
  }
  
  function testGetNumberOfDays()
  {    
    $c = new lmbMonthCalendar(2007, 5);
    $this->assertEqual($c->getNumberOfDays(), 31);    
  }
  
  function testGetNumberOfDaysForLeapYear()
  {
    $c1 = new lmbMonthCalendar(2005, 2);
    $this->assertEqual($c1->getNumberOfDays(), 28);
    
    $c2 = new lmbMonthCalendar(2004, 2);
    $this->assertEqual($c2->getNumberOfDays(), 29);    
  }
  
  function testGetNumberOfWeeks()
  {
    $c1 = new lmbMonthCalendar(1999, 2);
    $this->assertEqual($c1->getNumberOfWeeks(), 4);
    
    $c2 = new lmbMonthCalendar(2007, 2);
    $this->assertEqual($c2->getNumberOfWeeks(), 5);
    
    $c3 = new lmbMonthCalendar(2010, 5);
    $this->assertEqual($c3->getNumberOfWeeks(), 6);            
  }  
}
?>