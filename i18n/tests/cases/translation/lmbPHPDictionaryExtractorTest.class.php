<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/i18n/src/translation/lmbPHPDictionaryExtractor.class.php');
lmb_require('limb/i18n/src/translation/lmbI18NDictionary.class.php');

class lmbPHPDictionaryExtractorTest extends UnitTestCase
{
  function testExtractDefault()
  {
    $src = <<< EOD
<?php
lmb_i18n("Hello");
?>
EOD;

    $dicts = array();
    $parser = new lmbPHPDictionaryExtractor();
    $parser->extract($src, $dicts);

    $this->assertTrue($dicts['default']->has('Hello'));
  }

  function testExtractSeveralDomains()
  {
    $src = <<< EOD
<?php
lmb_i18n("Hello", "foo");
lmb_i18n("Dog", "bar");
lmb_i18n("Apple", "zzz");
?>
EOD;

    $dicts = array();
    $parser = new lmbPHPDictionaryExtractor();
    $parser->extract($src, $dicts);

    $this->assertTrue($dicts['foo']->has('Hello'));
    $this->assertTrue($dicts['bar']->has('Dog'));
    $this->assertTrue($dicts['zzz']->has('Apple'));
  }

  function testMergeWithExisting()
  {
    $src = <<< EOD
<?php
lmb_i18n("Hello", "foo");
lmb_i18n("Dog", "bar");
lmb_i18n("Apple", "zzz");
?>
EOD;

    $dicts = array('foo' => new lmbI18NDictionary(array('Doll' => '')));
    $parser = new lmbPHPDictionaryExtractor();
    $parser->extract($src, $dicts);

    $this->assertTrue($dicts['foo']->has('Hello'));
    $this->assertTrue($dicts['foo']->has('Doll'));//merged
    $this->assertTrue($dicts['bar']->has('Dog'));
    $this->assertTrue($dicts['zzz']->has('Apple'));
  }

  function testExtractDefaultParametrizized()
  {
    $src = <<< EOD
<?php
lmb_i18n("Hello %1 %2", null, array('1' => 'foo', '2' => 'bar'));
?>
EOD;

    $dicts = array();
    $parser = new lmbPHPDictionaryExtractor();
    $parser->extract($src, $dicts);

    $this->assertTrue($dicts['default']->has('Hello %1 %2'));
  }

  function testExtractSkipVariables()
  {
    $src = <<< EOD
<?php
lmb_i18n(\$a);
lmb_i18n(\$b, \$a);
lmb_i18n('Hello');
?>
EOD;

    $dicts = array();
    $parser = new lmbPHPDictionaryExtractor();
    $parser->extract($src, $dicts);

    $this->assertTrue($dicts['default']->has('Hello'));
  }

  function testExtractSkipFunctionDeclaration()
  {
    $src = <<< EOD
<?php
function lmb_i18n(\$a = 'Hello'){}
?>
EOD;

    $dicts = array();
    $parser = new lmbPHPDictionaryExtractor();
    $parser->extract($src, $dicts);

    $this->assertFalse($dicts);
  }
}


