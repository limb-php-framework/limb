<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/i18n/utf8.inc.php');
lmb_require('limb/i18n/src/translation/lmbI18NDictionary.class.php');

class lmbI18NMacroFilterTest extends lmbBaseMacroTest
{
  /**
   * @var lmbI18NTools
   */
  protected $toolkit;

  function setUp()
  {
    parent::setUp();
    $this->toolkit = lmbToolkit::instance();
  }

  function testUseDefaultDomain()
  {
    $dictionary = new lmbI18NDictionary();
    $dictionary->add('Apply filter', 'Применить фильтр');

    $this->toolkit->addLocaleObject(new lmbLocale('ru'), 'ru');
    $this->toolkit->setDictionary('ru', 'default', $dictionary);

    $this->toolkit->setLocale('ru');

    $out = $this->_createMacroTemplate('{$var|i18n}')
             ->render(array('var' => 'Apply filter'));

    $this->assertEqual($out, 'Применить фильтр');
  }

  function testUseCustomDomain()
  {
    $domain = 'my_domain';
    $dictionary = new lmbI18NDictionary();
    $dictionary->add('Apply filter', 'Применить фильтр');

    $this->toolkit->addLocaleObject(new lmbLocale('ru'), 'ru');
    $this->toolkit->setDictionary('ru', $domain, $dictionary);

    $this->toolkit->setLocale('ru');

    $out = $this->_createMacroTemplate('{$var|i18n:$domain}')
             ->render(array('var' => 'Apply filter',
                            'domain' => $domain));

    $this->assertEqual($out, 'Применить фильтр');
  }
}

