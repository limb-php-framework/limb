<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/i18n/src/datetime/lmbLocaleDate.class.php');

class lmbI18NDateMacroFilterTest extends lmbBaseMacroTest
{
  function testSetDateByString()
  {
    $code = '{$#var|i18n_date:"en_US", "string"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $time = mktime(0, 0, 0, 2, 20, 2002);
    $tpl->set('var', $time);
    $out = $tpl->render();
    $this->assertEqual($out, '02/20/2002');
  }

  function testSetDateByStampValue()
  {
    $date = new lmbDateTime('2004-12-20 10:15:30');
    $time=$date->getStamp();

    $code = '{$#var|i18n_date:"en_US", "stamp"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');

    $tpl->set('var', $time);
    $out = $tpl->render();
    $this->assertEqual($out, '12/20/2004');
  }

  function testFormatType()
  {
    $date = new lmbDateTime('2005-01-20 10:15:30');
    $time=$date->getStamp();

    $code = '{$#var|i18n_date:"en_US", "stamp", "date"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');

    $tpl->set('var', $time);
    $out = $tpl->render();
    $this->assertEqual($out, 'Thursday 20 January 2005');
  }

  function testSetDateTimeByString()
  {
    $time='2002-02-20 10:23:24';

    $code = '{$#var|i18n_date:"en_US", "string", "short_date_time"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');

    $tpl->set('var', $time);
    $out = $tpl->render();
    $this->assertEqual($out, '02/20/2002 10:23:24');
  }

  function testDefinedFormat()
  {
    $date = new lmbDateTime('2004-12-20 10:15:30');
    $time=$date->getStamp();

    $code = '{$#var|i18n_date:"en_US", "stamp", "", "%Y %m %d"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');

    $tpl->set('var', $time);
    $out = $tpl->render();
    $this->assertEqual($out, '2004 12 20');
  }

  function testUseRussianAsCurrentLocale()
  {
    $toolkit = lmbToolkit :: save();
    $toolkit->addLocaleObject(new lmbLocale('ru_RU', new lmbIni(dirname(__FILE__).'/../../../i18n/locale/ru_RU.ini')));

    $date = new lmbDateTime('2004-12-20 10:15:30');
    $time=$date->getStamp();

    $code = '{$#var|i18n_date:"ru_RU", "stamp"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');

    $tpl->set('var', $time);
    $out = $tpl->render();
    $this->assertEqual($out, '20.12.2004');

    lmbToolkit :: restore();
  }

  function testComplexPathBasedDBEWithDefinedFormat()
  {
    $date = new lmbDateTime('2005-01-20 10:15:30');
    $my_dataspace = new lmbSet(array('var' => $date->getStamp()));

    $code = '{$#my.var|i18n_date:"en_US", "stamp", "", "%Y %m %d"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');

    $tpl->set('my', $my_dataspace);
    $out = $tpl->render();
    $this->assertEqual($out, '2005 01 20');
  }

  function testDateByCurrentLocale()
  {
    $date = new lmbDateTime('2004-12-20 10:15:30');
    $time=$date->getStamp();

  	$code = '{$#var|i18n_date:"","stamp"}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', $time);
    $out = $tpl->render();
    $this->assertEqual($out, '12/20/2004');
  }

  function testWithOutParams()
  {
    $date = new lmbDateTime('2004-12-20 10:15:30');
    $time=$date->getStamp();

  	$code = '{$#var|i18n_date}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', $time);
    $out = $tpl->render();
    $this->assertEqual($out, '12/20/2004');
  }
}

