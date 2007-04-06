<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDateFormatTest.class.php 5550 2007-04-06 08:27:14Z pachanga $
 * @package    i18n
 */
lmb_require('limb/datetime/src/lmbDate.class.php');
lmb_require('limb/i18n/toolkit.inc.php');
lmb_require('limb/i18n/src/datetime/lmbDateFormat.class.php');

class lmbDateFormatTest extends UnitTestCase
{
  function testFormat()
  {
    $date = new lmbDate('2005-01-02 23:05:03');
    $printer = new lmbDateFormat();
    $string = $printer->toString($date, '%C %d %D %e %E %H %I %j %m %M %n %R %S %U %y %Y %t %%');

    $this->assertEqual($string, "20 02 01/02/05 2 2453373 23 11 002 01 05 \n 23:05 03 53 05 2005 \t %");
  }

  function testLocalizedFormat()
  {
    $date = new lmbDate('2005-01-20 10:15:30');

    $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));
    $printer = new lmbDateFormat();

    $toStringed_date = $printer->toString($date, $locale->getDateFormat(), $locale);

    $expected = 'Thursday 20 January 2005';
    $this->assertEqual($toStringed_date, $expected);
  }

  function testToISO()
  {
    $date = new lmbDate('2005-01-02 23:05:03');
    $printer = new lmbDateFormat();
    $string = $printer->toISO($date);

    $this->assertEqual($string, '2005-01-02 23:05:03');
  }

  function testToDateISO()
  {
    $date = new lmbDate('2005-01-02 23:05:03');
    $printer = new lmbDateFormat();
    $string = $printer->toDateISO($date);

    $this->assertEqual($string, '2005-01-02');
  }

  function testToTimeISO()
  {
    $date = new lmbDate('2005-01-02 23:05:03');
    $printer = new lmbDateFormat();
    $string = $printer->toTimeISO($date);

    $this->assertEqual($string, '23:05:03');
  }
}
?>
