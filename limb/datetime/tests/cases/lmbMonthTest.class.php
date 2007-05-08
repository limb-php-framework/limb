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
lmb_require('limb/datetime/src/lmbMonth.class.php');

class lmbMonthTest extends UnitTestCase
{
  function testGetBoundaries()
  {
    $c = new lmbMonth(2007, 5);
    $this->assertEqual(new lmbDate('2007-05-01'), $c->getStartDate());
    $this->assertEqual(new lmbDate('2007-05-31'), $c->getEndDate());
  }

  function testGetNumberOfDays()
  {
    $c = new lmbMonth(2007, 5);
    $this->assertEqual($c->getNumberOfDays(), 31);
  }

  function testGetNumberOfDaysForLeapYear()
  {
    $c1 = new lmbMonth(2005, 2);
    $this->assertEqual($c1->getNumberOfDays(), 28);

    $c2 = new lmbMonth(2004, 2);
    $this->assertEqual($c2->getNumberOfDays(), 29);
  }

  function testGetNumberOfWeeks()
  {
    $c1 = new lmbMonth(1999, 2);
    $this->assertEqual($c1->getNumberOfWeeks(), 4);

    $c2 = new lmbMonth(2007, 2);
    $this->assertEqual($c2->getNumberOfWeeks(), 5);

    $c3 = new lmbMonth(2010, 5);
    $this->assertEqual($c3->getNumberOfWeeks(), 6);
  }

  function testGetWeekFailed()
  {
    $c = new lmbMonth(1999, 2);
    $this->assertNull($c->getWeek(4));
    $this->assertNull($c->getWeek(10));
    $this->assertNull($c->getWeek(-1));
  }

  function testGetIdealWeek()
  {
    $c = new lmbMonth(1999, 2);
    $week0 = $c->getWeek(0);
    $week1 = $c->getWeek(1);
    $week2 = $c->getWeek(2);
    $week3 = $c->getWeek(3);

    $expected0 = array();
    $expected1 = array();
    $expected2 = array();
    $expected3 = array();

    for($i=0;$i<7;$i++)
      $expected0[] = new lmbDate(sprintf("1999-02-%02d", $i+1));
    for($i=7;$i<14;$i++)
      $expected1[] = new lmbDate(sprintf("1999-02-%02d", $i+1));
    for($i=14;$i<21;$i++)
      $expected2[] = new lmbDate(sprintf("1999-02-%02d", $i+1));
    for($i=21;$i<28;$i++)
      $expected3[] = new lmbDate(sprintf("1999-02-%02d", $i+1));

    $this->assertEqual($week0, $expected0);
    $this->assertEqual($week1, $expected1);
    $this->assertEqual($week2, $expected2);
    $this->assertEqual($week3, $expected3);
  }

  function testGetWeekWithDaysFromBoundaryMonths()
  {
    $c = new lmbMonth(2007, 2);
    $week0 = $c->getWeek(0);
    $week1 = $c->getWeek(1);
    $week2 = $c->getWeek(2);
    $week3 = $c->getWeek(3);
    $week4 = $c->getWeek(4);

    $expected0 = array();
    $expected1 = array();
    $expected2 = array();
    $expected3 = array();
    $expected4 = array();

    for($i=29;$i<32;$i++)
      $expected0[] = new lmbDate(sprintf("2007-01-%02d", $i));
    for($i=1;$i<5;$i++)
      $expected0[] = new lmbDate(sprintf("2007-02-%02d", $i));

    for($i=5;$i<12;$i++)
      $expected1[] = new lmbDate(sprintf("2007-02-%02d", $i));
    for($i=12;$i<19;$i++)
      $expected2[] = new lmbDate(sprintf("2007-02-%02d", $i));
    for($i=19;$i<26;$i++)
      $expected3[] = new lmbDate(sprintf("2007-02-%02d", $i));

    for($i=26;$i<29;$i++)
      $expected4[] = new lmbDate(sprintf("2007-02-%02d", $i));
    for($i=1;$i<5;$i++)
      $expected4[] = new lmbDate(sprintf("2007-03-%02d", $i));

    $this->assertEqual($week0, $expected0);
    $this->assertEqual($week1, $expected1);
    $this->assertEqual($week2, $expected2);
    $this->assertEqual($week3, $expected3);
    $this->assertEqual($week4, $expected4);
  }

  function testGetAllWeeks()
  {
    $c = new lmbMonth(2007, 2);
    $weeks = $c->getAllWeeks();
    $this->assertEqual($weeks, array($c->getWeek(0),  $c->getWeek(1), $c->getWeek(2),
                                     $c->getWeek(3), $c->getWeek(4)));
  }

  function testGetNextMonth()
  {
    $c = new lmbMonth(2007, 2);
    $next = $c->getNextMonth();
    $this->assertEqual(new lmbMonth(2007, 3), $next);
  }

  function testGetNextMothFromDecember()
  {
    $c = new lmbMonth(2007, 12);
    $next = $c->getNextMonth();
    $this->assertEqual(new lmbMonth(2008, 1), $next);
  }

  function testGetPrevMonth()
  {
    $c = new lmbMonth(2007, 2);
    $prev = $c->getPrevMonth();
    $this->assertEqual(new lmbMonth(2007, 1), $prev);
  }

  function testGetPrevMonthFromJanuary()
  {
    $c = new lmbMonth(2007, 1);
    $prev = $c->getPrevMonth();
    $this->assertEqual(new lmbMonth(2006, 12), $prev);
  }
}
?>