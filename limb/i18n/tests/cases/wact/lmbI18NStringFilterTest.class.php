<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/i18n/src/translation/lmbI18NDictionary.class.php');

class lmbI18NStringFilterTest extends lmbWactTestCase
{
  function testUseCurrentLocale()
  {
    $dictionary = new lmbI18NDictionary();
    $dictionary->add('Apply filter', 'Применить фильтр');

    $this->toolkit->setDictionary('ru', 'foo', $dictionary);
    $this->toolkit->setLocale('ru');

    $template = '{$"Apply filter"|i18n:"foo"}';

    $this->registerTestingTemplate('/limb/locale_string_filter_locale.html', $template);

    $page = $this->initTemplate('/limb/locale_string_filter_locale.html');

    $this->assertEqual($page->capture(), 'Применить фильтр');
  }

  function testWithAttributes()
  {
    $dictionary = new lmbI18NDictionary();
    $dictionary->add('Apply %1 filter and %2', 'Применить фильтр %1 и %2');

    $this->toolkit->setDictionary('ru', 'foo', $dictionary);
    $this->toolkit->setLocale('ru');

    $template = '{$"Apply %1 filter and %2"|i18n:"foo", "%1", "1", "%2", "2"}';

    $this->registerTestingTemplate('/limb/locale_string_filter_with_attributes.html', $template);

    $page = $this->initTemplate('/limb/locale_string_filter_with_attributes.html');

    $this->assertEqual($page->capture(), 'Применить фильтр 1 и 2');
  }

  function testWithDBEAttributes()
  {
    $dictionary = new lmbI18NDictionary();
    $dictionary->add('Apply %name% filter to %var%', 'Применить фильтр %name% к %var%');

    $this->toolkit->setDictionary('ru', 'foo', $dictionary);
    $this->toolkit->setLocale('ru');

    $template = '{$"Apply %name% filter to %var%"|i18n:"foo", "%name%", name, "%var%", variable.name}';

    $this->registerTestingTemplate('/limb/locale_string_filter_with_dbe_attributes.html', $template);

    $page = $this->initTemplate('/limb/locale_string_filter_with_dbe_attributes.html');
    $page->set('name', 'ИмяФильтра');
    $page->set('variable', array('name' =>'ИмяПеременной'));
    $this->assertEqual($page->capture(), 'Применить фильтр ИмяФильтра к ИмяПеременной');
  }

  function testDBEVariable()
  {
    $dictionary = new lmbI18NDictionary();
    $dictionary->add('Apply filter', 'Применить фильтр');

    $this->toolkit->setDictionary('ru', 'foo', $dictionary);
    $this->toolkit->setLocale('ru');

    $template = '{$var|i18n:"foo"}';

    $this->registerTestingTemplate('/limb/locale_string_filter_dbe.html', $template);

    $page = $this->initTemplate('/limb/locale_string_filter_dbe.html');
    $page->set('var', 'Apply filter');

    $this->assertTrue($page->capture(), 'Применить фильтр');
  }

}

