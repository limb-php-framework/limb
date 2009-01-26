<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

Mock :: generate('lmbMacroAnnotationParserListener', 'MockMacroAnnotationParserListener');

class lmbMacroAnnotationParserTest extends UnitTestCase
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

    $listener = new MockMacroAnnotationParserListener();
    $listener->expectOnce('createByAnnotations', array($file, "Foo{$rnd}Tag", array('tag' => "foo_{$rnd}")));
    $info = lmbMacroAnnotationParser :: extractFromFile($file, $listener);
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

    $listener = new MockMacroAnnotationParserListener();
    $listener->expectCallCount('createByAnnotations', 2);
    $listener->expectArgumentsAt(0, 'createByAnnotations', array($file, "Foo{$rnd}Tag", array('tag' => "foo_{$rnd}")));
    $listener->expectArgumentsAt(1, 'createByAnnotations', array($file, "Bar{$rnd}Tag", array('tag' => "bar_{$rnd}")));

    $info = lmbMacroAnnotationParser :: extractFromFile($file, $listener);
  }
}

