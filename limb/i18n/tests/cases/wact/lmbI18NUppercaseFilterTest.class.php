<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
require_once('limb/i18n/utf8.inc.php');

class lmbI18NUppercaseFilterTest extends lmbWactTestCase
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
    $template = '{$"тест"|i18n_uppercase}';

    $this->registerTestingTemplate('/limb/locale_uppercase_filter.html', $template);

    $page = $this->initTemplate('/limb/locale_uppercase_filter.html');

    $this->assertEqual($page->capture(), 'ТЕСТ');
  }

  function testDBE()
  {
    $template = '{$var|i18n_uppercase}';

    $this->registerTestingTemplate('/limb/locale_uppercase_filter_dbe.html', $template);

    $page = $this->initTemplate('/limb/locale_uppercase_filter_dbe.html');
    $page->set('var', 'ТесТ');

    $this->assertEqual($page->capture(), 'ТЕСТ');
  }

}

