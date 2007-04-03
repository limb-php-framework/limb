<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbI18NDateFilterTest.class.php 5373 2007-03-28 11:10:40Z pachanga $
 * @package    web_app
 */
lmb_require('limb/datetime/src/lmbDate.class.php');

class lmbI18NDateFilterTest extends lmbWactTestCase
{
  function setUp()
  {
    parent :: setUp();
    $this->toolkit->addLocaleObject(new lmbLocale(dirname(__FILE__) . '/../../../en.ini'));
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
    $date = new lmbDate('2004-12-20 10:15:30');

    $template = '{$"' . $date->getStamp() . '"|i18n_date:"en", "stamp"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_stamp.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_stamp.html');

    $this->assertEqual($page->capture(), '12/20/2004');
  }

  function testFormatType()
  {
    $date = new lmbDate('2005-01-20 10:15:30');

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
    $date = new lmbDate('2004-12-20 10:15:30');

    $template = '{$"' . $date->getStamp() . '"|i18n_date:"en", "stamp", "", "%Y %m %d"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_defined_format.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_defined_format.html');

    $this->assertEqual($page->capture(), '2004 12 20');
  }

  function testUseRussianAsCurrentLocale()
  {
    $toolkit = lmbToolkit :: save();
    $toolkit->addLocaleObject(new lmbLocale(dirname(__FILE__) . '/../../../ru.ini'));

    $date = new lmbDate('2004-12-20 10:15:30');

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
    $date = new lmbDate('2004-12-20 10:15:30');

    $template = '{$var|i18n_date:"en", "stamp"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_dbe_stamp.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_dbe_stamp.html');

    $page->set('var', $date->getStamp());

    $this->assertEqual($page->capture(), '12/20/2004');
  }

  function testDBEFormatType()
  {
    $date = new lmbDate('2005-01-20 10:15:30');

    $template = '{$var|i18n_date:"en", "stamp", "date"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_dbe_format_type.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_dbe_format_type.html');

    $page->set('var', $date->getStamp());

    $this->assertEqual($page->capture(), 'Thursday 20 January 2005');
  }

  function testDBEDefinedFormat()
  {
    $date = new lmbDate('2005-01-20 10:15:30');

    $template = '{$var|i18n_date:"en", "stamp", "", "%Y %m %d"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_dbe_defined_format.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_dbe_defined_format.html');

    $page->set('var', $date->getStamp());

    $this->assertEqual($page->capture(), '2005 01 20');
  }

  function testDBEUseRussianAsCurrentLocale()
  {
    $toolkit = lmbToolkit :: save();
    $toolkit->addLocaleObject(new lmbLocale(dirname(__FILE__) . '/../../../ru.ini'));

    $date = new lmbDate('2005-01-20 10:15:30');

    $template = '{$var|i18n_date:"ru", "stamp"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_dbe_use_russian_locale.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_dbe_use_russian_locale.html');

    $page->set('var', $date->getStamp());

    $this->assertEqual($page->capture(), '20.01.2005');

    lmbToolkit :: restore();
  }

  function testComplexPathBasedDBEWithDefinedFormat()
  {
    $date = new lmbDate('2005-01-20 10:15:30');

    $template = '{$my.var|i18n_date:"en", "stamp", "", "%Y %m %d"}';

    $this->registerTestingTemplate('/limb/locale_date_filter_path_based_dbe_defined_format.html', $template);

    $page = $this->initTemplate('/limb/locale_date_filter_path_based_dbe_defined_format.html');

    $my_dataspace = new lmbDataspace(array('var' => $date->getStamp()));
    $page->set('my', $my_dataspace);

    $this->assertEqual($page->capture(), '2005 01 20');
  }
}
?>
