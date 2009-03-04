<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/i18n/src/translation/lmbWACTDictionaryExtractor.class.php');
lmb_require('limb/i18n/src/translation/lmbI18NDictionary.class.php');

class lmbWACTDictionaryExtractorTest extends UnitTestCase
{
  function testExtractDefault()
  {
    $src = <<< EOD
<html>
{\$"Hello"|i18n}
</html>
EOD;

    $dicts = array();
    $loader = new lmbWACTDictionaryExtractor();
    $loader->extract($src, $dicts);

    $this->assertTrue($dicts['default']->has('Hello'));
  }

  function testExtractSeveralDomains()
  {
    $src = <<< EOD
<html>
{\$"Hello"|i18n:"foo"}
{\$"Dog"|i18n:"bar"}
{\$"Apple"|i18n:"zzz"}
</html>
EOD;

    $dicts = array();
    $loader = new lmbWACTDictionaryExtractor();
    $loader->extract($src, $dicts);

    $this->assertTrue($dicts['foo']->has('Hello'));
    $this->assertTrue($dicts['bar']->has('Dog'));
    $this->assertTrue($dicts['zzz']->has('Apple'));
  }

  function testExtractMergeSeveralDomains()
  {
    $src = <<< EOD
<html>
{\$"Hello"|i18n:"foo"}
{\$"Dog"|i18n:"bar"}
{\$"Apple"|i18n:"zzz"}
</html>
EOD;

    $dicts = array('foo' => new lmbI18NDictionary(array('Doll' => '')));
    $loader = new lmbWACTDictionaryExtractor();
    $loader->extract($src, $dicts);

    $this->assertTrue($dicts['foo']->has('Hello'));
    $this->assertTrue($dicts['foo']->has('Doll'));//merged
    $this->assertTrue($dicts['bar']->has('Dog'));
    $this->assertTrue($dicts['zzz']->has('Apple'));
  }

  function testExtractSeveralFilters()
  {
    $src = <<< EOD
<html>
{\$"Hello"|i18n|trim|hex}
</html>
EOD;

    $dicts = array();
    $loader = new lmbWACTDictionaryExtractor();
    $loader->extract($src, $dicts);

    $this->assertTrue($dicts['default']->has('Hello'));
  }

  function testFilterMustBeFirst()
  {
    $src = <<< EOD
<html>
{\$hello|trim|i18n:"/foo"}
</html>
EOD;

    $dicts = array();
    $loader = new lmbWACTDictionaryExtractor();
    $loader->extract($src, $dicts);

    $this->assertFalse($dicts);
  }

  function testSkipVariable()
  {
    $src = <<< EOD
<html>
{\$hello|i18n:"/foo"}
</html>
EOD;

    $dicts = array();
    $loader = new lmbWACTDictionaryExtractor();
    $loader->extract($src, $dicts);

    $this->assertFalse($dicts);
  }
}

