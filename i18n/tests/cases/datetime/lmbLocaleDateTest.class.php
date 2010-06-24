<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/i18n/src/datetime/lmbLocaleDateTime.class.php');

class lmbLocaleDateTimeTest extends UnitTestCase
{
  function testCreateByLocaleString()
  {
    $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

    $date = lmbLocaleDateTime :: localStringToDate($locale, 'Thursday 20 January 2005', '%A %d %B %Y');

    $this->assertEqual($date->getMonth(), 1);
    $this->assertEqual($date->getYear(), 2005);
    $this->assertEqual($date->getDay(), 20);
  }

  function testCreateByAnotherLocaleString()
  {
    $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

    $date = lmbLocaleDateTime :: localStringToDate($locale, 'Thu 20 Jan 2005', '%a %d %b %Y');

    $this->assertEqual($date->getMonth(), 1);
    $this->assertEqual($date->getYear(), 2005);
    $this->assertEqual($date->getDay(), 20);
  }

  function testCreateByWrongStringThrowsException()
  {
    $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

    try
    {
      $date = lmbLocaleDateTime :: localStringToDate($locale, '02-29-2003', '%a %d %b %Y');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testIsLocalStringValid()
  {
    $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

    $this->assertTrue(lmbLocaleDateTime :: isLocalStringValid($locale, 'Mon 01', '%a %d'));
  }

  function testIsLocalStringNotValid()
  {
    $locale = new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini'));

    $this->assertFalse(lmbLocaleDateTime :: isLocalStringValid($locale, '02-29-2003', '%a %d %b %Y'));
  }
}

