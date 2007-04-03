<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbI18NTranslationTest.class.php 5411 2007-03-29 10:07:12Z pachanga $
 * @package    i18n
 */
lmb_require('limb/i18n/common.inc.php');
lmb_require('limb/i18n/src/translation/lmbQtDictionaryBackend.class.php');
lmb_require('limb/util/src/system/lmbFs.class.php');

class lmbI18NTranslationTest extends UnitTestCase
{
  function setUp()
  {
    lmbFs :: mkdir(LIMB_VAR_DIR . '/translations');
  }

  function tearDown()
  {
    lmbFs :: rm(LIMB_VAR_DIR . '/translations');
  }

  function testTranslate()
  {
    $toolkit = lmbToolkit :: save();
    $back = new lmbQtDictionaryBackend();
    $back->setSearchPath($translations_dir = LIMB_VAR_DIR . '/translations');
    $toolkit->setDictionaryBackend($back);

    $xml = <<< EOD
<?xml version="1.0"?>
<!DOCTYPE TS><TS>
<context>
<message>
    <source>Hello</source>
    <translation>Привет</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($translations_dir . '/foo.ru_RU.ts', $xml);

    $toolkit->setLocale('ru_RU');
    $this->assertEqual(lmb_i18n('Hello', 'foo'), 'Привет');

    lmbToolkit :: restore();
  }

  function testTranslateDefaultContext()
  {
    $toolkit = lmbToolkit :: save();
    $back = new lmbQtDictionaryBackend();
    $back->setSearchPath($translations_dir = LIMB_VAR_DIR . '/translations');
    $toolkit->setDictionaryBackend($back);

    $xml = <<< EOD
<?xml version="1.0"?>
<!DOCTYPE TS><TS>
<context>
<message>
    <source>Hello</source>
    <translation>Привет</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($translations_dir . '/default.ru_RU.ts', $xml);

    $toolkit->setLocale('ru_RU');
    $this->assertEqual(lmb_i18n('Hello'), 'Привет');

    lmbToolkit :: restore();
  }

  function testTranslateSubstituteParameters()
  {
    $toolkit = lmbToolkit :: save();
    $back = new lmbQtDictionaryBackend();
    $back->setSearchPath($translations_dir = LIMB_VAR_DIR . '/translations');
    $toolkit->setDictionaryBackend($back);

    $xml = <<< EOD
<?xml version="1.0"?>
<!DOCTYPE TS><TS>
<context>
<message>
    <source>Hello {name}</source>
    <translation>Привет {name}</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($translations_dir . '/foo.ru_RU.ts', $xml);

    $toolkit->setLocale('ru_RU');
    $this->assertEqual(lmb_i18n('Hello {name}', array('{name}' => 'Bob'), 'foo'), 'Привет Bob');

    lmbToolkit :: restore();
  }

  function testTranslateSubstituteParametersDefaultContext()
  {
    $toolkit = lmbToolkit :: save();
    $back = new lmbQtDictionaryBackend();
    $back->setSearchPath($translations_dir = LIMB_VAR_DIR . '/translations');
    $toolkit->setDictionaryBackend($back);

    $xml = <<< EOD
<?xml version="1.0"?>
<!DOCTYPE TS><TS>
<context>
<message>
    <source>Hello {name}</source>
    <translation>Привет {name}</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($translations_dir . '/default.ru_RU.ts', $xml);

    $toolkit->setLocale('ru_RU');
    $this->assertEqual(lmb_i18n('Hello {name}', array('{name}' => 'Bob')), 'Привет Bob');

    lmbToolkit :: restore();
  }
}

?>
