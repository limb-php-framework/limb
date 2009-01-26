<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/i18n/src/locale/lmbLocaleSpec.class.php');

class lmbLocaleSpecTest extends UnitTestCase
{
  function testParseOnlyLanguage()
  {
    $spec = new lmbLocaleSpec('ru');

    $this->assertEqual($spec->getLocaleString(), 'ru');
    $this->assertEqual($spec->getLanguage(), 'ru');
    $this->assertFalse($spec->getCountry());
    $this->assertFalse($spec->getCountryVariation());
    $this->assertFalse($spec->getCharset());
    $this->assertEqual($spec->getLocale(), 'ru');
  }

  function testParseLanguageAndCountry()
  {
    $spec = new lmbLocaleSpec('ru_RU');

    $this->assertEqual($spec->getLocaleString(), 'ru_RU');
    $this->assertEqual($spec->getLanguage(), 'ru');
    $this->assertEqual($spec->getCountry(), 'RU');
    $this->assertFalse($spec->getCountryVariation());
    $this->assertFalse($spec->getCharset());
    $this->assertEqual($spec->getLocale(), 'ru_RU');
  }

  function testParseLanguageAndCountryAndVariation()
  {
    $spec = new lmbLocaleSpec('eng_GB@euro');

    $this->assertEqual($spec->getLocaleString(), 'eng_GB@euro');
    $this->assertEqual($spec->getLanguage(), 'eng');
    $this->assertEqual($spec->getCountry(), 'GB');
    $this->assertEqual($spec->getCountryVariation(), 'euro');
    $this->assertFalse($spec->getCharset());
    $this->assertEqual($spec->getLocale(), 'eng_GB');
  }

  function testParseLanguageAndCountryAndVariationAndCharset()
  {
    $spec = new lmbLocaleSpec('eng_GB.utf8@euro');

    $this->assertEqual($spec->getLocaleString(), 'eng_GB.utf8@euro');
    $this->assertEqual($spec->getLanguage(), 'eng');
    $this->assertEqual($spec->getCountry(), 'GB');
    $this->assertEqual($spec->getCountryVariation(), 'euro');
    $this->assertEqual($spec->getCharset(), 'utf8');
    $this->assertEqual($spec->getLocale(), 'eng_GB');
  }
}


