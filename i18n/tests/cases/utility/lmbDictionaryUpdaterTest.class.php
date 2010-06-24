<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * TODO replace cli by taskman
 */
return;

lmb_require('limb/cli/src/lmbCliResponse.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');
lmb_require('limb/i18n/src/translation/lmbDictionary.class.php');
lmb_require('limb/i18n/src/translation/lmbQtDictionaryBackend.class.php');
lmb_require('limb/i18n/src/translation/lmbDictionaryUpdater.class.php');

Mock :: generate('lmbCliResponse', 'MockCliResponse');

class lmbDictionaryUpdaterTest extends UnitTestCase
{	
	
  function skip()
  {
    $this->skipIf(true, 'TODO: replace cli by taskman');
  }
  
  function setUp()
  {
    $this->_cleanUp();
    lmbFs :: mkdir(LIMB_VAR_DIR. '/translations');
    lmbFs :: mkdir(LIMB_VAR_DIR. '/src');
    lmbFs :: mkdir(LIMB_VAR_DIR. '/parse1');
    lmbFs :: mkdir(LIMB_VAR_DIR. '/parse2');
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    lmbFs :: rm(LIMB_VAR_DIR . '/translations');
    lmbFs :: rm(LIMB_VAR_DIR . '/src');
  }

  function testUpdateTranslations()
  {
    $translations_dir = LIMB_VAR_DIR . '/translations';
    $ru_file = $translations_dir . '/foo.ru_RU.ts';
    $de_file = $translations_dir . '/foo.de_DE.ts';

    $source_dir = LIMB_VAR_DIR . '/src/';
    $html_file = $source_dir . '/hourse.html';
    $php_file = $source_dir . '/cat.php';

    $xml = <<< EOD
<?xml version="1.0"?>
<!DOCTYPE TS><TS>
<context>
<message>
    <source>Dog</source>
    <translation>Dog</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($ru_file, $xml);
    file_put_contents($de_file, $xml);

    $php = <<< EOD
<?php
lmb_i18n('Cat', 'foo');
?>
EOD;
    file_put_contents($php_file, $php);

    $html = <<< EOD
{\$'Horse'|i18n:'foo'}
EOD;
    file_put_contents($html_file, $html);

    $cli_responce = new MockCliResponse();
    $backend = new lmbQtDictionaryBackend();
    $backend->setSearchPath($translations_dir);

    $updater = new lmbDictionaryUpdater($backend, $cli_responce);
    $updater->updateTranslations($source_dir);

    $ru_dictionary = $backend->loadFromFile($ru_file);
    $this->assertTrue($ru_dictionary->has('Horse'));
    $this->assertTrue($ru_dictionary->has('Cat'));
    $this->assertTrue($ru_dictionary->has('Dog'));

    $de_dictionary = $backend->loadFromFile($de_file);
    $this->assertTrue($de_dictionary->has('Horse'));
    $this->assertTrue($de_dictionary->has('Cat'));
    $this->assertTrue($de_dictionary->has('Dog'));
  }

  function testUpdateTranslationsForDefaultContext()
  {
    $translations_dir = LIMB_VAR_DIR . '/translations';
    $ru_file = $translations_dir . '/default.ru_RU.ts';
    $de_file = $translations_dir . '/default.de_DE.ts';

    $source_dir = LIMB_VAR_DIR . '/src/';
    $html_file = $source_dir . '/hourse.html';
    $php_file = $source_dir . '/cat.php';

    $xml = <<< EOD
<?xml version="1.0"?>
<!DOCTYPE TS><TS>
<context>
<message>
    <source>Dog</source>
    <translation>Dog</translation>
</message>
</context>
</TS>
EOD;
    file_put_contents($ru_file, $xml);
    file_put_contents($de_file, $xml);

    $php = <<< EOD
<?php
lmb_i18n('Cat');
?>
EOD;
    file_put_contents($php_file, $php);

    $html = <<< EOD
{\$'Horse'|i18n}
EOD;
    file_put_contents($html_file, $html);

    $cli_responce = new MockCliResponse();
    $backend = new lmbQtDictionaryBackend();
    $backend->setSearchPath($translations_dir);

    $updater = new lmbDictionaryUpdater($backend, $cli_responce);
    $updater->updateTranslations($source_dir);

    $ru_dictionary = $backend->loadFromFile($ru_file);
    $this->assertTrue($ru_dictionary->has('Horse'));
    $this->assertTrue($ru_dictionary->has('Cat'));
    $this->assertTrue($ru_dictionary->has('Dog'));

    $de_dictionary = $backend->loadFromFile($de_file);
    $this->assertTrue($de_dictionary->has('Horse'));
    $this->assertTrue($de_dictionary->has('Cat'));
    $this->assertTrue($de_dictionary->has('Dog'));
  }
}

