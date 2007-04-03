<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbLocaleDateTest.class.php 5353 2007-03-27 16:20:49Z pachanga $
 * @package    i18n
 */
lmb_require('limb/i18n/src/datetime/lmbLocaleDate.class.php');

class lmbLocaleDateTest extends UnitTestCase
{
  function testCreateByLocaleString()
  {
    $locale = new lmbLocale(dirname(__FILE__) . '/../en.ini');

    $date = lmbLocaleDate :: localStringToDate($locale, 'Thursday 20 January 2005', '%A %d %B %Y');

    $this->assertEqual($date->getMonth(), 1);
    $this->assertEqual($date->getYear(), 2005);
    $this->assertEqual($date->getDay(), 20);
  }

  function testCreateByAnotherLocaleString()
  {
    $locale = new lmbLocale(dirname(__FILE__) . '/../en.ini');

    $date = lmbLocaleDate :: localStringToDate($locale, 'Thu 20 Jan 2005', '%a %d %b %Y');

    $this->assertEqual($date->getMonth(), 1);
    $this->assertEqual($date->getYear(), 2005);
    $this->assertEqual($date->getDay(), 20);
  }

  function testCreateByWrongStringThrowsException()
  {
    $locale = new lmbLocale(dirname(__FILE__) . '/../en.ini');

    try
    {
      $date = lmbLocaleDate :: localStringToDate($locale, '02-29-2003', '%a %d %b %Y');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testLocalizedDateStringToISODateString()
  {
    $locale = new lmbLocale(dirname(__FILE__) . '/../ru.ini');

    $date_string = '24.10.2005';
    $this->assertEqual(lmbLocaleDate :: localStringToISO($locale, $date_string), '2005-10-24 00:00:00');
  }

  function testIsoDateStringToLocalStringizedDateString()
  {
    $locale = new lmbLocale(dirname(__FILE__) . '/../ru.ini');

    $iso_date_string = '2005-10-24 00:00:00';
    $this->assertEqual(lmbLocaleDate :: ISOToLocalString($locale, $iso_date_string), '24.10.2005');
  }
}
?>