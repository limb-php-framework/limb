<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/fs/src/lmbFs.class.php');
lmb_require('limb/macro/src/lmbMacroTag.class.php');
lmb_require('limb/macro/src/lmbMacroTagInfo.class.php');
lmb_require('limb/macro/src/lmbMacroTagDictionary.class.php');
lmb_require('limb/macro/src/lmbMacroConfig.class.php');

class lmbMacroTagDictionaryTest extends UnitTestCase
{
  function setUp()
  {
    lmbFs :: rm(LIMB_VAR_DIR . '/tags/');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/tags/');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/tags/subfolder/');
  }

  function testFindTagInfo()
  {
    $tag_info = new lmbMacroTagInfo('testtag', 'SomeTagClass');
    $dictionary = new lmbMacroTagDictionary();
    $dictionary->register($tag_info, $file = 'whatever');

    $this->assertIsA($dictionary->findTagInfo('testtag'), 'lmbMacroTagInfo');
  }

  function testRegisterTagInfoOnceOnly()
  {
    $dictionary = new lmbMacroTagDictionary();
    $tag_info1 = new lmbMacroTagInfo('some_tag', 'SomeTagClass');
    $tag_info2 = new lmbMacroTagInfo('some_tag', 'SomeTagClass');
    $dictionary->register($tag_info1, $file1 = 'whatever1');
    $dictionary->register($tag_info2, $file2 = 'whatever2');

    $this->assertEqual($dictionary->findTagInfo('some_tag'), $tag_info1);
  }

  function testTagNotFound()
  {
    $tag_info = new lmbMacroTagInfo('testtag', 'SomeTagClass');
    $dictionary = new lmbMacroTagDictionary();
    $dictionary->register($tag_info, $file = 'whatever');

    $this->assertFalse($dictionary->findTagInfo('junk'));
  }

  function testRegisterFromFile()
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

    $tag_info1 = new lmbMacroTagInfo("foo_$rnd", "Foo{$rnd}Tag");
    $tag_info1->setFile($file);
    $tag_info2 = new lmbMacroTagInfo("bar_$rnd", "Bar{$rnd}Tag");
    $tag_info2->setFile($file);

    $dictionary = new lmbMacroTagDictionary();
    $dictionary->registerFromFile($file);

    $this->assertEqual($dictionary->findTagInfo("foo_$rnd"), $tag_info1);
    $this->assertEqual($dictionary->findTagInfo("bar_$rnd"), $tag_info2);
  }
  
  function testLoad()
  {
    $rnd = mt_rand();
    $content1 = <<<EOD
<?php
/**
 * @tag foo_{$rnd}
 */
class Foo{$rnd}Tag extends lmbMacroTag{}
EOD;

    $content2 = <<<EOD
<?php
/**
 * @tag bar_{$rnd}
 */
class Bar{$rnd}Tag extends lmbMacroTag{}
EOD;

    file_put_contents($file1 = LIMB_VAR_DIR . '/tags/foo_' . $rnd . '.tag.php', $content1);
    file_put_contents($file2 = LIMB_VAR_DIR . '/tags/subfolder/bar_' . $rnd . '.tag.php', $content2);

    $tag_info1 = new lmbMacroTagInfo("foo_$rnd", "Foo{$rnd}Tag");
    $tag_info1->setFile($file1);
    $tag_info2 = new lmbMacroTagInfo("bar_$rnd", "Bar{$rnd}Tag");
    $tag_info2->setFile($file2);

    $config = new lmbMacroConfig();
    $config->setTagsScanDirectories(array(LIMB_VAR_DIR . '/tags/'));
    
    $dictionary = new lmbMacroTagDictionary();
    $dictionary->load($config);

    $this->assertEqual($dictionary->findTagInfo("foo_$rnd")->getTag(), $tag_info1->getTag());
    $this->assertEqual($dictionary->findTagInfo("bar_$rnd")->getTag(), $tag_info2->getTag());
  }
  
}

