<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/datetime/src/lmbDateTime.class.php');

class lmbI18NDateFilterTest extends lmbWactTestCase
{
  function setUp()
  {
    parent :: setUp();
    $this->toolkit->addLocaleObject(new lmbLocale('en', new lmbIni(dirname(__FILE__) . '/../en.ini')));
  }

  function testSetDateByString()
  {
    $template = '{$"2002-02-20"|i18n_date:"en", "string"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_string.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_string.html');

    $this->assertEqual($page->capture(), '02/20/2002');
  }

  function testSetDateByStampValue()
  {
    $date = new lmbDateTime('2004-12-20 10:15:30');

    $template = '{$"' . $date->getStamp() . '"|i18n_date:"en", "stamp"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_stamp.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_stamp.html');

    $this->assertEqual($page->capture(), '12/20/2004');
  }

  function testFormatType()
  {
    $date = new lmbDateTime('2005-01-20 10:15:30');

    $template = '{$"' . $date->getStamp() . '"|i18n_date:"en", "stamp", "date"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_format_type.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_format_type.html');

    $this->assertEqual($page->capture(), 'Thursday 20 January 2005');
  }

  function testSetDateTimeByString()
  {
    $template = '{$"2002-02-20 10:23:24"|i18n_date:"en", "string", "short_date_time"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_string_date_time.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_string_date_time.html');

    $this->assertEqual($page->capture(), '02/20/2002 10:23:24');
  }

  function testDefinedFormat()
  {
    $date = new lmbDateTime('2004-12-20 10:15:30');

    $template = '{$"' . $date->getStamp() . '"|i18n_date:"en", "stamp", "", "%Y %m %d"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_defined_format.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_defined_format.html');

    $this->assertEqual($page->capture(), '2004 12 20');
  }

  function testUseRussianAsCurrentLocale()
  {
    $toolkit = lmbToolkit :: save();
    $toolkit->addLocaleObject(new lmbLocale('ru', new lmbIni(dirname(__FILE__) . '/../ru.ini')));

    $date = new lmbDateTime('2004-12-20 10:15:30');

    $template = '{$"' . $date->getStamp() . '"|i18n_date:"ru", "stamp"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_use_russian_locale.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_use_russian_locale.html');

    $this->assertEqual($page->capture(), '20.12.2004');

    lmbToolkit :: restore();
  }

  function testDBESetDateByString()
  {
    $template = '{$var|i18n_date:"en", "string"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_dbe_string.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_dbe_string.html');

    $page->set('var', "2002-02-20");

    $this->assertEqual($page->capture(), '02/20/2002');
  }

  function testDBESetDateByStampValue()
  {
    $date = new lmbDateTime('2004-12-20 10:15:30');

    $template = '{$var|i18n_date:"en", "stamp"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_dbe_stamp.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_dbe_stamp.html');

    $page->set('var', $date->getStamp());

    $this->assertEqual($page->capture(), '12/20/2004');
  }

  function testDBEFormatType()
  {
    $date = new lmbDateTime('2005-01-20 10:15:30');

    $template = '{$var|i18n_date:"en", "stamp", "date"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_dbe_format_type.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_dbe_format_type.html');

    $page->set('var', $date->getStamp());

    $this->assertEqual($page->capture(), 'Thursday 20 January 2005');
  }

  function testDBEDefinedFormat()
  {
    $date = new lmbDateTime('2005-01-20 10:15:30');

    $template = '{$var|i18n_date:"en", "stamp", "", "%Y %m %d"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_dbe_defined_format.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_dbe_defined_format.html');

    $page->set('var', $date->getStamp());

    $this->assertEqual($page->capture(), '2005 01 20');
  }

  function testDBEUseRussianAsCurrentLocale()
  {
    $toolkit = lmbToolkit :: save();
    $toolkit->addLocaleObject(new lmbLocale('ru', new lmbIni(dirname(__FILE__) . '/../ru.ini')));

    $date = new lmbDateTime('2005-01-20 10:15:30');

    $template = '{$var|i18n_date:"ru", "stamp"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_dbe_use_russian_locale.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_dbe_use_russian_locale.html');

    $page->set('var', $date->getStamp());

    $this->assertEqual($page->capture(), '20.01.2005');

    lmbToolkit :: restore();
  }

  function testComplexPathBasedDBEWithDefinedFormat()
  {
    $date = new lmbDateTime('2005-01-20 10:15:30');

    $template = '{$my.var|i18n_date:"en", "stamp", "", "%Y %m %d"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_path_based_dbe_defined_format.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_path_based_dbe_defined_format.html');

    $my_dataspace = new lmbSet(array('var' => $date->getStamp()));
    $page->set('my', $my_dataspace);

    $this->assertEqual($page->capture(), '2005 01 20');
  }
}

