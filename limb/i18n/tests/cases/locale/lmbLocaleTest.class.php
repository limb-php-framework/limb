<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/config/src/lmbIni.class.php');
lmb_require('limb/i18n/src/locale/lmbLocaleSpec.class.php');
lmb_require('limb/i18n/src/locale/lmbLocale.class.php');

class lmbLocaleTest extends UnitTestCase
{
  function testGetLocaleSpec()
  {
    $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));
    $this->assertEqual($locale->getLocaleSpec(), new lmbLocaleSpec('en'));
  }

  function testGetMonthName()
  {
    $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

    $this->assertEqual($locale->getMonthName(0), 'January');
    $this->assertEqual($locale->getMonthName(11), 'December');
    $this->assertNull($locale->getMonthName(12));
  }

  function testGetDayName()
  {
    $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

    $this->assertEqual($locale->getDayName(0, $short = false), 'Sunday');
    $this->assertEqual($locale->getDayName(0, $short = true), 'Sun');
    $this->assertEqual($locale->getDayName(6, $short = false), 'Saturday');
    $this->assertEqual($locale->getDayName(6, $short = true), 'Sat');
  }

  function testGetOtherOptions()
  {
    $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

    $this->assertEqual($locale->getCharset(), 'utf-8');
    $this->assertEqual($locale->getLanguageDirection(), 'ltr');
  }

  function testGetCountryOptions()
  {
    $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

    $this->assertEqual($locale->getCountryName(), 'USA');
    $this->assertEqual($locale->getCountryComment(), '');
  }

  function testGetLanguageOptions()
  {
    $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

    $this->assertEqual($locale->getLanguageName(), 'English (American)');
    $this->assertEqual($locale->getIntlLanguageName(), 'English (American)');
  }

  function testGetCurrencyOptions()
  {
    $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

    $this->assertEqual($locale->getCurrencySymbol(), '$');
    $this->assertEqual($locale->getCurrencyName(), 'US Dollar');
    $this->assertEqual($locale->getCurrencyShortName(), 'USD');
  }

  function testGetDateTimeFormatOptions()
  {
    $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

    $this->assertEqual($locale->getTimeFormat(), '%H:%M:%S %p');
    $this->assertEqual($locale->getShortTimeFormat(), '%H:%M %p');
    $this->assertEqual($locale->getDateFormat(), '%A %d %B %Y');
    $this->assertEqual($locale->getShortDateFormat(), '%m/%d/%Y');
    $this->assertEqual($locale->getShortDateTimeFormat(), '%m/%d/%Y %H:%M:%S');
    $this->assertEqual($locale->getDateTimeFormat(), '%A %d %B %Y %H:%M:%S');
  }

  function testGetWeekDaysOptions()
  {
    $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

    $this->assertFalse($locale->isMondayFirst());
    $this->assertEqual($locale->getWeekDays(), array(0, 1, 2, 3, 4, 5, 6));
    $this->assertEqual($locale->getMonths(), array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11));
    $this->assertEqual($locale->getWeekDayNames(), array('Sunday',
                                                         'Monday',
                                                         'Tuesday',
                                                         'Wednesday',
                                                         'Thursday',
                                                         'Friday',
                                                         'Saturday'));

    $this->assertEqual($locale->getWeekDayNames($short = true), array('Sun',
                                                                      'Mon',
                                                                      'Tue',
                                                                      'Wed',
                                                                      'Thu',
                                                                      'Fri',
                                                                      'Sat'));

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

