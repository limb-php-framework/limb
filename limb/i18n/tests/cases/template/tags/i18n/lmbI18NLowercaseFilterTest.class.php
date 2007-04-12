<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbI18NLowercaseFilterTest.class.php 5646 2007-04-12 08:38:15Z pachanga $
 * @package    i18n
 */
require_once('limb/i18n/utf8.inc.php');

class lmbI18NLowercaseFilterTest extends lmbWactTestCase
{
  var $prev_driver;

  function setUp()
  {
    $this->prev_driver = lmb_use_charset_driver(new lmbUTF8BaseDriver());
    parent :: setUp();
  }

  function tearDown()
  {
    lmb_use_charset_driver($this->prev_driver);
    parent :: tearDown();
  }

  function testSimple()
  {
    $template = '{$"ТЕСТ"|i18n_lowercase}';

    $this->registerTestingTemplate('/limb/locale_lowercase_filter.html', $template);

    $page = $this->initTemplate('/limb/locale_lowercase_filter.html');

    $this->assertEqual($page->capture(), 'тест');
  }

  function testDBE()
  {
    $template = '{$var|i18n_lowercase}';

    $this->registerTestingTemplate('/limb/locale_lowercase_filter_dbe.html', $template);

    $page = $this->initTemplate('/limb/locale_lowercase_filter_dbe.html');
    $page->set('var', 'ТесТ');

    $this->assertEqual($page->capture(), 'тест');
  }

}
?>
