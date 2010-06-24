<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
require_once('limb/i18n/utf8.inc.php');

class lmbI18NCapitalizeFilterTest extends lmbWactTestCase
{
  var $prev_driver;

  function setUp()
  {
    parent :: setUp();
    $this->prev_driver = lmb_use_charset_driver(new lmbUTF8BaseDriver());
  }

  function tearDown()
  {
    lmb_use_charset_driver($this->prev_driver);
    parent :: tearDown();
  }

  function testSimple()
  {
    $template = '{$"тест"|i18n_capitalize}';

    $this->registerTestingTemplate('/limb/locale_capitalize_filter.html', $template);

    $page = $this->initTemplate('/limb/locale_capitalize_filter.html');

    $this->assertEqual($page->capture(), 'Тест');
  }

  function testDBE()
  {
    $template = '{$var|i18n_capitalize}';

    $this->registerTestingTemplate('/limb/locale_capitalize_filter_dbe.html', $template);

    $page = $this->initTemplate('/limb/locale_capitalize_filter_dbe.html');
    $page->set('var', 'тест');

    $this->assertEqual($page->capture(), 'Тест');
  }

  function testPathBasedDBE()
  {
    $template = '{$my.var|i18n_capitalize}';

    $this->registerTestingTemplate('/limb/locale_capitalize_filter_path_based_dbe.html', $template);

    $page = $this->initTemplate('/limb/locale_capitalize_filter_path_based_dbe.html');
    $my_dataspace = new lmbSet(array('var' => 'тест'));
    $page->set('my', $my_dataspace);

    $this->assertEqual($page->capture(), 'Тест');
  }
}

