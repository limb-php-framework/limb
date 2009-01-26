<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/i18n/src/toolkit/lmbI18NTools.class.php');

class lmbI18NNumberFilterTest extends lmbWactTestCase
{
  protected $locale;

  function setUp()
  {
    parent :: setUp();

    $this->locale = new lmbLocale('en');
    $this->locale->fract_digits = 2;
    $this->locale->decimal_symbol = '.';
    $this->locale->thousand_separator = ',';
    $this->toolkit->addLocaleObject($this->locale);
  }

  function testUseDefaultLocale()
  {
    $this->toolkit->setLocale('en');

    $template = '{$"100000"|i18n_number}';

    $this->registerTestingTemplate('/limb/locale_number_filter_default.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_default.html');

    $this->assertEqual($page->capture(), '100,000.00');
  }

  function testUseOtherLocale()
  {
    $this->locale->fract_digits = 4;
    $this->toolkit->addLocaleObject($this->locale, 'foo');

    $template = '{$"100000"|i18n_number:"foo"}';

    $this->registerTestingTemplate('/limb/locale_number_filter_russian.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_russian.html');

    $this->assertEqual($page->capture(), '100,000.0000');
  }

  function testUseFractDigits()
  {
    $template = '{$"100000"|i18n_number:"en","3"}';

    $this->registerTestingTemplate('/limb/locale_number_filter_fract_digits.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_fract_digits.html');

    $this->assertEqual($page->capture(), '100,000.000');
  }

  function testUseDecimalSymbol()
  {
    $template = '{$"100000"|i18n_number:"en","",","}';

    $this->registerTestingTemplate('/limb/locale_number_filter_decimal_symbol.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_decimal_symbol.html');

    $this->assertEqual($page->capture(), '100,000,00');
  }

  function testUseThousandSeparator()
  {
    $template = '{$"100000"|i18n_number:"en","",""," "}';

    $this->registerTestingTemplate('/limb/locale_number_filter_thousand_separator.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_thousand_separator.html');

    $this->assertEqual($page->capture(), '100 000.00');
  }

  function testDefaultDBE()
  {
    $template = '{$var|i18n_number}';

    $this->registerTestingTemplate('/limb/locale_number_filter_DBE.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_DBE.html');

    $page->set('var', '100000');

    $this->assertEqual($page->capture(), '100,000.00');
  }

  function testDBEUseOtherLocale()
  {
    $this->locale->fract_digits = 4;
    $this->toolkit->addLocaleObject($this->locale, 'foo');

    $template = '{$var|i18n_number:"foo"}';

    $this->registerTestingTemplate('/limb/locale_number_filter_DBE_other_locale.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_DBE_other_locale.html');
    $page->set('var', '100000');

    $this->assertEqual($page->capture(), '100,000.0000');
  }

  function testDBEUseFractDigits()
  {
    $template = '{$var|i18n_number:"en","3"}';

    $this->registerTestingTemplate('/limb/locale_number_filter_DBE_fract_digits.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_DBE_fract_digits.html');
    $page->set('var', '100000');

    $this->assertEqual($page->capture(), '100,000.000');
  }

  function testDBEUseDecimalSymbol()
  {
    $template = '{$var|i18n_number:"en","",","}';

    $this->registerTestingTemplate('/limb/locale_number_filter_DBE_decimal_symbol.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_DBE_decimal_symbol.html');
    $page->set('var', '100000');

    $this->assertEqual($page->capture(), '100,000,00');
  }

  function testDBEUseThousandSeparator()
  {
    $template = '{$var|i18n_number:"en","",""," "}';

    $this->registerTestingTemplate('/limb/locale_number_filter_DBE_thousand_separator.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_DBE_thousand_separator.html');
    $page->set('var', '100000');

    $this->assertEqual($page->capture(), '100 000.00');
  }
}

