<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/fs/src/lmbFs.class.php');
lmb_require('limb/macro/src/lmbMacroTagInfo.class.php');

class lmbMacroTagInfoTest extends UnitTestCase
{
  function setUp()
  {
    lmbFs :: rm(LIMB_VAR_DIR . '/tags/');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/tags/');
  }

  function testExtractOneFromFile()
  {
    $rnd = mt_rand();
    $contents = <<<EOD
<?php
/**
 * @tag foo_{$rnd}
 */
class Foo{$rnd}Tag extends lmbMacroTag{}
EOD;
    file_put_contents($file = LIMB_VAR_DIR . '/tags/' . $rnd . '.tag.php', $contents);

    $info = lmbMacroTagInfo :: extractFromFile($file);

    $this->assertEqual(sizeof($info), 1);
    $this->assertEqual($info[0], new lmbMacroTagInfo("foo_$rnd", "Foo{$rnd}Tag"));
  }

  function testExtractSeveralFromFile()
  {
    $rnd = mt_rand();
    $contents = <<<EOD
<?php
/**
 * @tag foo_{$rnd}
 */
class Foo{$rnd}Tag extends lmbMacroTag{}

/**
 * @tag bar_{$rnd}
 */
class Bar{$rnd}Tag extends lmbMacroTag{}
EOD;
    file_put_contents($file = LIMB_VAR_DIR . '/tags/' . $rnd . '.tag.php', $contents);

    $info = lmbMacroTagInfo :: extractFromFile($file);

    $this->assertEqual(sizeof($info), 2);
    $this->assertEqual($info[0], new lmbMacroTagInfo("foo_$rnd", "Foo{$rnd}Tag"));
    $this->assertEqual($info[1], new lmbMacroTagInfo("bar_$rnd", "Bar{$rnd}Tag"));
  }
}

