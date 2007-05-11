<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDateTest.class.php 5865 2007-05-11 13:05:33Z pachanga $
 * @package    datetime
 */
lmb_require('limb/datetime/src/lmbDate.class.php');
lmb_require('limb/datetime/src/lmbDateTimeZone.class.php');

class lmbDateTest extends UnitTestCase
{
  function testInvalidDate()
  {
    try
    {
      $date = new lmbDate(400, 500, 5000, 9000);
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testInvalidStamp()
  {
    try
    {
      $date = new lmbDate(-1);
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testInvalidTimeString()
  {
    try
    {
      $date = new lmbDate('baba-duba');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testValidate()
  {
    $this->assertTrue(lmbDate :: validate('2005-12-01 12:45:12'));
    $this->assertTrue(lmbDate :: validate('2005-12-01 12:45'));
    $this->assertTrue(lmbDate :: validate('2005-12-01'));
    $this->assertTrue(lmbDate :: validate('12:45:12'));
    $this->assertTrue(lmbDate :: validate('12:45'));
    $this->assertTrue(lmbDate :: validate(' 12:45:12 '));
  }

  function testValidateFalse()
  {
    $this->assertFalse(lmbDate :: validate('baba-duba'));
    $this->assertFalse(lmbDate :: validate('2005-12-01 12.'));
    $this->assertFalse(lmbDate :: validate(2006, 13, 11));
  }

  function testCreate()
  {
    $date = new lmbDate(2005, 12, 1, 12, 45, 12);
    $this->assertEqual(lmbDate :: create(2005, 12, 1, 12, 45, 12), $date);

    $this->assertEqual($date->getDay(), 1);
    $this->assertEqual($date->getMonth(), 12);
    $this->assertEqual($date->getYear(), 2005);
    $this->assertEqual($date->getHour(), 12);
    $this->assertEqual($date->getMinute(), 45);
    $this->assertEqual($date->getSecond(), 12);
  }

  function testGetIsoDate()
  {
    $date = new lmbDate(2005, 12, 1, 12, 45, 12);
    $this->assertEqual($date->getIsoDate(), '2005-12-01 12:45:12');
  }

  function testGetIsoDateWithoutSeconds()
  {
    $date = new lmbDate(2005, 12, 1, 12, 45, 12);
    $this->assertEqual($date->getIsoDate(false), '2005-12-01 12:45');
  }

  function testGetIsoShortDate()
  {
    $date = new lmbDate(2005, 12, 1, 12, 45, 12);
    $this->assertEqual($date->getIsoShortDate(), '2005-12-01');
  }

  function testGetIsoTime()
  {
    $date = new lmbDate(2005, 12, 1, 12, 45, 12);
    $this->assertEqual($date->getIsoTime(), '12:45:12');
  }

  function testGetIsoTimeWithoutSeconds()
  {
    $date = new lmbDate(2005, 12, 1, 12, 45, 12);
    $this->assertEqual($date->getIsoTime(false), '12:45');
  }

  function testToStringReturnsIsoDate()
  {
    $date = new lmbDate(2005, 12, 1, 12, 45, 12);
    $this->assertEqual($date->toString(), '2005-12-01 12:45:12');
  }

  function testStrftime()
  {
    $date = new lmbDate(2005, 12, 1, 12, 45, 12);
    $this->assertEqual($date->strftime('%m/%d/%y'), '12/01/05');
  }

  function testDate()
  {
    $date = new lmbDate(2005, 12, 1, 12, 45, 12);
    $this->assertEqual($date->date('m.d.y'), '12.01.05');
  }

  function testCreateByCopy()
  {
    $date = new lmbDate($sample = new lmbDate(2005, 12, 1, 12, 45, 12));
    $this->assertEqual(lmbDate :: create($sample), $date);

    $this->assertEqual($date, $sample);
  }

  function testCreateByIso()
  {
    $date = new lmbDate('2005-12-01  12:45:12');
    $this->assertEqual(lmbDate :: create('2005-12-01  12:45:12'), $date);

    $this->assertEqual($date->getDay(), 1);
    $this->assertEqual($date->getMonth(), 12);
    $this->assertEqual($date->getYear(), 2005);
    $this->assertEqual($date->getHour(), 12);
    $this->assertEqual($date->getMinute(), 45);
    $this->assertEqual($date->getSecond(), 12);

    $this->assertEqual($date->toString(), '2005-12-01 12:45:12');
  }

  function testCreateByIsoDateOnly()
  {
    $date = new lmbDate('2005-12-01');
    $this->assertEqual(lmbDate :: create('2005-12-01'), $date);

    $this->assertEqual($date->getDay(), 1);
    $this->assertEqual($date->getMonth(), 12);
    $this->assertEqual($date->getYear(), 2005);
    $this->assertEqual($date->getHour(), 0);
    $this->assertEqual($date->getMinute(), 0);
    $this->assertEqual($date->getSecond(), 0);

    $this->assertEqual($date->toString(), '2005-12-01 00:00:00');
  }

  function testCreateByIsoTimeOnly()
  {
    $date = new lmbDate('12:45:12');
    $this->assertEqual(lmbDate :: create('12:45:12'), $date);

    $this->assertEqual($date->getDay(), 0);
    $this->assertEqual($date->getMonth(), 0);
    $this->assertEqual($date->getYear(), 0);
    $this->assertEqual($date->getHour(), 12);
    $this->assertEqual($date->getMinute(), 45);
    $this->assertEqual($date->getSecond(), 12);

    $this->assertEqual($date->toString(), '0000-00-00 12:45:12');
  }

  function testCreateByIsoTimeWithSecondsOmitted()
  {
    $date = new lmbDate('12:45');
    $this->assertEqual(lmbDate :: create('12:45'), $date);

    $this->assertEqual($date->getDay(), 0);
    $this->assertEqual($date->getMonth(), 0);
    $this->assertEqual($date->getYear(), 0);
    $this->assertEqual($date->getHour(), 12);
    $this->assertEqual($date->getMinute(), 45);
    $this->assertEqual($date->getSecond(), 0);

    $this->assertEqual($date->toString(), '0000-00-00 12:45:00');
  }

  function testStampToIso()
  {
    $stamp = mktime(21, 45, 13, 12, 1, 2005);
    $iso = lmbDate :: stampToIso($stamp);
    $this->assertEqual($iso, '2005-12-01 21:45:13');
  }

  function testCreateByStamp()
  {
    $date = new lmbDate($stamp = mktime(21, 45, 13, 12, 1, 2005));
    $this->assertEqual(lmbDate :: create($stamp), $date);

    $this->assertEqual($date->getDay(), 1);
    $this->assertEqual($date->getMonth(), 12);
    $this->assertEqual($date->getYear(), 2005);
    $this->assertEqual($date->getHour(), 21);
    $this->assertEqual($date->getMinute(), 45);
    $this->assertEqual($date->getSecond(), 13);

    $this->assertEqual($date->toString(), '2005-12-01 21:45:13');
  }

  function testCreateByDays()
  {
    $date = new lmbDate('2005-12-01');
    $days = $date->getDateDays();
    $this->assertEqual(lmbDate :: createByDays($days), $date);
  }

  function testGetStamp()
  {
    $date = new lmbDate($stamp = mktime(21, 45, 13, 12, 1, 2005));
    $this->assertEqual($date->getStamp(), $stamp);
  }

  function testGetDayOfWeekForSunday()
  {
    $date = new lmbDate('2005-01-16');
    $this->assertEqual($date->getDayOfWeek(), 0);
  }

  function testGetDayOfWeekForMonday()
  {
    $date = new lmbDate('2005-01-17');
    $this->assertEqual($date->getDayOfWeek(), 1);
  }

  function testGetDayOfWeekForSuturday()
  {
    $date = new lmbDate('2005-01-15');
    $this->assertEqual($date->getDayOfWeek(), 6);
  }

  //in the two tests below we're testing a boundary situtation
  //for day of the week which happens in February
  function testGetDayOfWeekMonthBeforeFebruary()
  {
    $date = new lmbDate('2005-01-20');
    $this->assertEqual($date->getDayOfWeek(), 4);
  }

  function testGetDayOfWeekMonthAfterFebruary()
  {
    $date = new lmbDate('2005-08-20');
    $this->assertEqual($date->getDayOfWeek(), 6);
  }

  function testGetBeginOfDay()
  {
    $date = new lmbDate('2005-08-20 12:24:12');
    $this->assertEqual($date->getBeginOfDay(), new lmbDate('2005-08-20 00:00:00'));
  }

  function testGetEndOfDay()
  {
    $date = new lmbDate('2005-08-20 12:24:12');
    $this->assertEqual($date->getEndOfDay(), new lmbDate('2005-08-20 23:59:59'));
  }

  function testGetBeginOfWeek()
  {
    $date = new lmbDate('2005-01-20');
    $this->assertEqual($date->getBeginOfWeek(), new lmbDate('2005-01-17'));
  }

  function testGetBeginOfWeekForMonday()
  {
    $date = new lmbDate('2005-01-17');
    $this->assertEqual($date->getBeginOfWeek(), new lmbDate('2005-01-17'));
  }

  function testGetBeginOfWeekForSunday()
  {
    $date = new lmbDate('2005-01-16');
    $this->assertEqual($date->getBeginOfWeek(), new lmbDate('2005-01-10'));
  }

  function testGetEndOfWeek()
  {
    $date = new lmbDate('2005-01-20');
    $this->assertEqual($date->getEndOfWeek(), new lmbDate('2005-01-23'));
  }

  function testGetEndOfWeekForMonday()
  {
    $date = new lmbDate('2005-01-17');
    $this->assertEqual($date->getEndOfWeek(), new lmbDate('2005-01-23'));
  }

  function testGetEndOfWeekForSunday()
  {
    $date = new lmbDate('2005-01-16');
    $this->assertEqual($date->getEndOfWeek(), new lmbDate('2005-01-16'));
  }

  function testGetBeginOfMonth()
  {
    $date = new lmbDate('2005-08-20 12:24:12');
    $this->assertEqual($date->getBeginOfMonth(), new lmbDate('2005-08-01 00:00:00'));
  }

  function testGetEndOfMonth()
  {
    $date = new lmbDate('2007-05-09 12:24:12');
    $this->assertEqual($date->getEndOfMonth(), new lmbDate('2007-05-31 23:59:59'));
  }

  function testGetBeginOfYear()
  {
    $date = new lmbDate('2005-08-20 12:24:12');
    $this->assertEqual($date->getBeginOfYear(), new lmbDate('2005-01-01 00:00:00'));
  }

  function testGetEndOfYear()
  {
    $date = new lmbDate('2007-05-09 12:24:12');
    $this->assertEqual($date->getEndOfYear(), new lmbDate('2007-12-31 23:59:59'));
  }

  function testSetYear()
  {
    $date = new lmbDate('2005-01-01');
    $new_date = $date->setYear(2006);
    $this->assertEqual($date->toString(), '2005-01-01 00:00:00');
    $this->assertEqual($new_date->toString(), '2006-01-01 00:00:00');
  }

  function testSetMonth()
  {
    $date = new lmbDate('2005-01-01');
    $new_date = $date->setMonth(2);
    $this->assertEqual($date->toString(), '2005-01-01 00:00:00');
    $this->assertEqual($new_date->toString(), '2005-02-01 00:00:00');
  }

  function testSetDay()
  {
    $date = new lmbDate('2005-01-01');
    $new_date = $date->setDay(2);
    $this->assertEqual($date->toString(), '2005-01-01 00:00:00');
    $this->assertEqual($new_date->toString(), '2005-01-02 00:00:00');
  }

  function testSetHour()
  {
    $date = new lmbDate('2005-01-01');
    $new_date = $date->setHour(2);
    $this->assertEqual($date->toString(), '2005-01-01 00:00:00');
    $this->assertEqual($new_date->toString(), '2005-01-01 02:00:00');
  }

  function testSetMinute()
  {
    $date = new lmbDate('2005-01-01');
    $new_date = $date->setMinute(2);
    $this->assertEqual($date->toString(), '2005-01-01 00:00:00');
    $this->assertEqual($new_date->toString(), '2005-01-01 00:02:00');
  }

  function testSetSecond()
  {
    $date = new lmbDate('2005-01-01');
    $new_date = $date->setSecond(20);
    $this->assertEqual($date->toString(), '2005-01-01 00:00:00');
    $this->assertEqual($new_date->toString(), '2005-01-01 00:00:20');
  }

  function TODO_testSetWeek()
  {
    $date = new lmbDate('2005-01-01');
    $new_date = $date->setWeek(2);
    $this->assertEqual($date->toString(), '2005-01-01 00:00:00');
    $this->assertEqual($new_date->toString(), '2005-01-08 00:00:00');//???
  }

  function TODO_testSetDayOfWeek()
  {
  }

  function testSetTimeZone()
  {
    $date = new lmbDate('2005-01-01', 'Europe/Moscow');
    $new_date = $date->setTimeZone('UTC');
    $this->assertEqual($date->getTimeZone(), 'Europe/Moscow');
    $this->assertEqual($new_date->getTimeZone(), 'UTC');
  }

  function testAddYear()
  {
    $date = lmbDate :: create('2005-01-01')->addYear();
    $new_date = $date->addYear(-3);

    $this->assertEqual($date->toString(), '2006-01-01 00:00:00');
    $this->assertEqual($new_date->toString(), '2003-01-01 00:00:00');
  }

  function testAddMonth()
  {
    $date = lmbDate :: create('2005-01-01')->addMonth();
    $new_date = $date->addMonth(-2);
    $this->assertEqual($date->toString(), '2005-02-01 00:00:00');
    $this->assertEqual($new_date->toString(), '2004-12-01 00:00:00');
  }

  function testAddWeek()
  {
    $date = lmbDate :: create('2005-01-01')->addWeek();
    $new_date = $date->addWeek(-3);
    $this->assertEqual($date->toString(), '2005-01-08 00:00:00');
    $this->assertEqual($new_date->toString(), '2004-12-18 00:00:00');
  }

  function testAddDay()
  {
    $date = lmbDate :: create('2005-01-01')->addDay();
    $new_date = $date->addDay(-33);
    $this->assertEqual($date->toString(), '2005-01-02 00:00:00');
    $this->assertEqual($new_date->toString(), '2004-11-30 00:00:00');
  }

  function testAddHour()
  {
    $date = lmbDate :: create('2005-01-01')->addHour();
    $new_date = $date->addHour(-3);
    $this->assertEqual($date->toString(), '2005-01-01 01:00:00');
    $this->assertEqual($new_date->toString(), '2004-12-31 22:00:00');
  }

  function testAddMinute()
  {
    $date = lmbDate :: create('2005-01-01')->addMinute();
    $new_date = $date->addMinute(-3);
    $this->assertEqual($date->toString(), '2005-01-01 00:01:00');
    $this->assertEqual($new_date->toString(), '2004-12-31 23:58:00');
  }

  function testAddSecond()
  {
    $date = lmbDate :: create('2005-01-01')->addSecond();
    $new_date = $date->addSecond(-61);
    $this->assertEqual($date->toString(), '2005-01-01 00:00:01');
    $this->assertEqual($new_date->toString(), '2004-12-31 23:59:00');
  }

  function testAddMixed()
  {
    $date = lmbDate :: create('2005-01-01')->addMonth()->addWeek(-1)->addDay(2)->addHour(2)->addSecond(-30)->addMinute(2);

    $this->assertEqual($date->toString(), '2005-01-27 02:01:30');
  }

  function testCreateWithTZ()
  {
    $date = new lmbDate(2005, 5, 3, 12, 10, 5, 'Europe/Moscow');
    $tz = $date->getTimeZoneObject();
    $this->assertEqual($tz, new lmbDateTimeZone('Europe/Moscow'));
  }

  function testCreateWithInvalidTZ()
  {
    $date = new lmbDate(2005, 5, 3, 12, 10, 5, 'bla-bla');
    $tz = $date->getTimeZoneObject();
    $this->assertEqual($tz, new lmbDateTimeZone('UTC'));
  }

  function testCreateTZByDateString()
  {
    $date = new lmbDate('2005-01-01', 'Europe/Moscow');
    $tz = $date->getTimeZoneObject();
    $this->assertEqual($tz, new lmbDateTimeZone('Europe/Moscow'));
  }

  function testCreateTZByDateTimeString()
  {
    $date = new lmbDate('2005-01-01 12:20:40', 'Europe/Moscow');
    $tz = $date->getTimeZoneObject();
    $this->assertEqual($tz, new lmbDateTimeZone('Europe/Moscow'));
  }

  function testIgnoreTZWhileCloning()
  {
    $date = new lmbDate(new lmbDate('2005-01-01 12:20:40', 'Europe/Moscow'), 'ya-hooo');
    $tz = $date->getTimeZoneObject();
    $this->assertEqual($tz, new lmbDateTimeZone('Europe/Moscow'));
  }

  function testToUTC()
  {
    $date = new lmbDate('2005-06-01 12:20:40', 'Europe/Moscow');
    $new_date = $date->toUTC();
    $this->assertEqual($new_date->toString(), '2005-06-01 08:20:40');
  }

  function testToUTCWithDayLightSaving()
  {
    $date = new lmbDate('2005-01-01 12:20:40', 'Europe/Moscow');
    $new_date = $date->toUTC();
    $this->assertEqual($new_date->toString(), '2005-01-01 09:20:40');
  }

  function testIsInDaylightTime()
  {
    $date = new lmbDate('2005-01-01 12:20:40', 'Europe/Moscow');
    $this->assertFalse($date->isInDaylightTime());

    $date = new lmbDate('2005-06-01 12:20:40', 'Europe/Moscow');
    $this->assertTrue($date->isInDaylightTime());
  }

  function testIsLeapYear()
  {
    $date = new lmbDate('2005-01-01 12:20:40');
    $this->assertFalse($date->isLeapYear());

    $date = new lmbDate('2004-01-01 12:20:40');
    $this->assertTrue($date->isLeapYear());
  }

  function testGetDayOfYear()
  {
    $date = new lmbDate('2005-01-01 12:20:40');
    $this->assertEqual($date->getDayOfYear(), 1);

    $date = new lmbDate('2005-12-31 12:20:40');
    $this->assertEqual($date->getDayOfYear(), 365);
  }

  function testGetWeekOfYear()
  {
    $date = new lmbDate('2005-01-01 12:20:40');
    $this->assertEqual($date->getWeekOfYear(), 1);

    $date = new lmbDate('2005-01-06 12:20:40');
    $this->assertEqual($date->getWeekOfYear(), 1);

    $date = new lmbDate('2005-12-31 12:20:40');
    $this->assertEqual($date->getWeekOfYear(), 52);
  }

  function testCompare()
  {
    $d1 = new lmbDate('2005-01-01');
    $d2 = new lmbDate('2005-01-01');

    $this->assertEqual($d1->compare($d2), 0);
    $this->assertEqual($d1->addYear()->compare($d2), 1);
    $this->assertEqual($d1->compare($d2->addYear(2)), -1);
  }

  function testStripTime()
  {
    $date = new lmbDate('2005-01-01 12:20:40');
    $this->assertEqual($date->stripTime(), new lmbDate('2005-01-01'));
  }

  function testStripDate()
  {
    $date = new lmbDate('2005-01-01 12:20:40');
    $this->assertEqual($date->stripDate(), new lmbDate('12:20:40'));
  }

  function testIsDateEqual()
  {
    $date1 = new lmbDate('2005-01-01 12:20:40');
    $date2 = new lmbDate('2005-01-01 13:20:40');
    $this->assertTrue($date1->isEqualDate($date2));
    $this->assertTrue($date2->isEqualDate($date1));
  }

  function testIsDateNotEqual()
  {
    $date1 = new lmbDate('2005-02-01 12:20:40');
    $date2 = new lmbDate('2005-01-01 13:20:40');
    $this->assertFalse($date1->isEqualDate($date2));
    $this->assertFalse($date2->isEqualDate($date1));
  }
}
?>