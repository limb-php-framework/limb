<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbLocaleTest.class.php 5373 2007-03-28 11:10:40Z pachanga $
 * @package    i18n
 */
lmb_require('limb/config/src/lmbIni.class.php');
lmb_require('limb/i18n/src/locale/lmbLocaleSpec.class.php');
lmb_require('limb/i18n/src/locale/lmbLocale.class.php');

class lmbLocaleTest extends UnitTestCase
{
  function testGetLocaleSpec()
  {
    $locale = new lmbLocale(dirname(__FILE__) . '/../en.ini');
    $this->assertEqual($locale->getLocaleSpec(), new lmbLocaleSpec('en'));
  }

  function testGetMonthName()
  {
    $locale = new lmbLocale(dirname(__FILE__) . '/../en.ini');

    $this->assertEqual($locale->getMonthName(0), 'January');
    $this->assertEqual($locale->getMonthName(11), 'December');
    $this->assertNull($locale->getMonthName(12));
  }

  function testGetDayName()
  {
    $locale = new lmbLocale(dirname(__FILE__) . '/../en.ini');

    $this->assertEqual($locale->getDayName(0, $short = false), 'Monday');
    $this->assertEqual($locale->getDayName(0, $short = true), 'Mon');
    $this->assertEqual($locale->getDayName(6, $short = false), 'Sunday');
    $this->assertEqual($locale->getDayName(6, $short = true), 'Sun');
  }

  function testGetOtherOptions()
  {
    $locale = new lmbLocale(dirname(__FILE__) . '/../en.ini');

    $this->assertEqual($locale->getCharset(), 'utf-8');
    $this->assertEqual($locale->getLanguageDirection(), 'ltr');
  }

  function testGetCountryOptions()
  {
    $locale = new lmbLocale(dirname(__FILE__) . '/../en.ini');

    $this->assertEqual($locale->getCountryName(), 'USA');
    $this->assertEqual($locale->getCountryComment(), '');
  }

  function testGetLanguageOptions()
  {
    $locale = new lmbLocale(dirname(__FILE__) . '/../en.ini');

    $this->assertEqual($locale->getLanguageName(), 'English (American)');
    $this->assertEqual($locale->getIntlLanguageName(), 'English (American)');
  }

  function testGetCurrencyOptions()
  {
    $locale = new lmbLocale(dirname(__FILE__) . '/../en.ini');

    $this->assertEqual($locale->getCurrencySymbol(), '$');
    $this->assertEqual($locale->getCurrencyName(), 'US Dollar');
    $this->assertEqual($locale->getCurrencyShortName(), 'USD');
  }

  function testGetDateTimeFormatOptions()
  {
    $locale = new lmbLocale(dirname(__FILE__) . '/../en.ini');

    $this->assertEqual($locale->getTimeFormat(), '%H:%M:%S %p');
    $this->assertEqual($locale->getShortTimeFormat(), '%H:%M %p');
    $this->assertEqual($locale->getDateFormat(), '%A %d %B %Y');
    $this->assertEqual($locale->getShortDateFormat(), '%m/%d/%Y');
    $this->assertEqual($locale->getShortDateTimeFormat(), '%m/%d/%Y %H:%M:%S');
    $this->assertEqual($locale->getDateTimeFormat(), '%A %d %B %Y %H:%M:%S');
  }

  function testGetWeekDaysOptions()
  {
    $locale = new lmbLocale(dirname(__FILE__) . '/../en.ini');

    $this->assertFalse($locale->isMondayFirst());
    $this->assertEqual($locale->getWeekDays(), array(0, 1, 2, 3, 4, 5, 6));
    $this->assertEqual($locale->getMonths(), array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11));
    $this->assertEqual($locale->getWeekDayNames(), array('Monday',
                                                         'Tuesday',
                                                         'Wednesday',
                                                         'Thursday',
                                                         'Friday',
                                                         'Saturday',
                                                         'Sunday'));

    $this->assertEqual($locale->getWeekDayNames($short = true), array('Mon',
                                                                      'Tue',
                                                                      'Wed',
                                                                      'Thu',
                                                                      'Fri',
                                                                      'Sat',
                                                                      'Sun'));

    $this->assertEqual($locale->getMonthNames(), array('January',
                                                       'February',
                                                       'March',
                                                       'April',
                                                       'May',
                                                       'June',
                                                       'July',
                                                       'August',
                                                       'September',
                                                       'October',
                                                       'November',
                                                       'December'));

    $this->assertEqual($locale->getMonthNames($short = true), array('Jan',
                                                                    'Feb',
                                                                    'Mar',
                                                                    'Apr',
                                                                    'May',
                                                                    'Jun',
                                                                    'Jul',
                                                                    'Aug',
                                                                    'Sep',
                                                                    'Oct',
                                                                    'Nov',
                                                                    'Dec'));

    $this->assertEqual($locale->getMeridiemName(10), 'am');
    $this->assertEqual($locale->getMeridiemName(22), 'pm');
  }
}
?>