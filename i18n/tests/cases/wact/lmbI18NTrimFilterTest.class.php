<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class lmbI18NTrimFilterTest extends lmbWactTestCase
{
  function testSimple()
  {
    $template = '{$" тест "|i18n_trim}';

    $this->registerTestingTemplate('/limb/locale_trim_filter.html', $template);

    $page = $this->initTemplate('/limb/locale_trim_filter.html');

    $this->assertEqual($page->capture(), 'тест');
  }

  function testCharacters()
  {
    $template = '{$"ф:тест:ф"|i18n_trim:"ф"}';

    $this->registerTestingTemplate('/limb/locale_trim_filter_characters.html', $template);

    $page = $this->initTemplate('/limb/locale_trim_filter_characters.html');

    $this->assertEqual($page->capture(), ':тест:');
  }

  function testDBE()
  {
    $template = '{$var|i18n_trim}';

    $this->registerTestingTemplate('/limb/locale_trim_filter_dbe.html', $template);

    $page = $this->initTemplate('/limb/locale_trim_filter_dbe.html');
    $page->set('var', ' тест ');

    $this->assertEqual($page->capture(), 'тест');
  }

  function testDBECharacters()
  {
    $template = '{$var|i18n_trim: "ф"}';

    $this->registerTestingTemplate('/limb/locale_trim_filter_dbe_characters.html', $template);

    $page = $this->initTemplate('/limb/locale_trim_filter_dbe_characters.html');
    $page->set('var', 'ф:тест:ф');

    $this->assertEqual($page->capture(), ':тест:');
  }

}

