<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cli/src/lmbCliInput.class.php');
lmb_require('limb/cli/src/lmbCliOption.class.php');
lmb_require('limb/cli/src/lmbCliException.class.php');

class lmbCliInputTest extends UnitTestCase
{
  function testReadEmpty()
  {
    $cli = new lmbCliInput();
    $this->assertEqual($cli->getOptions(), array());
    $this->assertEqual($cli->getArguments(), array());

    $this->assertNull($cli->getOption('f'));
    $this->assertNull($cli->getOptionValue('f'));
    $this->assertFalse($cli->hasOption('f'));
    $this->assertEqual($cli->getOptionValue('f', 'wow'), 'wow');
    $this->assertNull($cli->getArgument(0));
    $this->assertEqual($cli->getArgument(0, 'wow'), 'wow');
  }

  function testReadSimpleOptionsWithArguments()
  {
    $argv = array('foo.php', '-f', 'wow', '--bar=1', 'foo', 'bar');

    $cli = new lmbCliInput();

    $this->assertTrue($cli->read($argv));
    $this->assertEqual($cli->getOptionValue('f'), 'wow');
    $this->assertEqual($cli->getOptionValue('bar'), '1');
    $this->assertEqual($cli->getArguments(), array('foo', 'bar'));
  }

  function testReadOptionsHoldingSpaces()
  {
    $argv = array('foo.php', '--foo', 'wow hey test', '-f', 'spaces spaces', '--bar', 1, 'foo', 'bar');

    $cli = new lmbCliInput();

    $this->assertTrue($cli->read($argv));
    $this->assertEqual($cli->getOptionValue('foo'), 'wow hey test');
    $this->assertEqual($cli->getOptionValue('f'), 'spaces spaces');
    $this->assertEqual($cli->getOptionValue('bar'), 1);
    $this->assertEqual($cli->getArguments(), array('foo', 'bar'));
  }

  function testNoValueOptionValuesBecomeArguments()
  {
    $cli = new lmbCliInput();
    $this->assertTrue($cli->read(array('foo.php', '-f', 'foo', 'bar')));
    $this->assertEqual($cli->getArguments(), array('bar'));

//    foreach($cli->getOptions() as $option)
//      echo 'option: '.$option->getName().' = "'.$option->getValue().'"'.PHP_EOL;
//
//    var_dump($cli->getArguments());
//
//        die();
  }

  function testReadWithEqualSignPresent()
  {
    $argv = array('foo.php', '--foo=1', '-b', 2);

    $cli = new lmbCliInput('f|foo=;b|bar=');

    $this->assertTrue($cli->read($argv));
    $this->assertEqual($cli->getOptionValue('foo'), 1);
    $this->assertEqual($cli->getOptionValue('b'), 2);
  }

  function testReadOptionsWithEqualSignMissing()
  {
    $argv = array('foo.php', '--foo', 1, '-b', 2);

    $cli = new lmbCliInput('f|foo=;b|bar=');

    $this->assertTrue($cli->read($argv));
    $this->assertEqual($cli->getOptionValue('foo'), 1);
    $this->assertEqual($cli->getOptionValue('b'), 2);
  }

  function testReadMixedOptions()
  {
    $argv = array('foo.php', '--foo=1', '-b', 2, '--zoo', 3);

    $cli = new lmbCliInput('f|foo=;b|bar=;z|zoo=');

    $this->assertTrue($cli->read($argv));
    $this->assertEqual($cli->getOptionValue('foo'), 1);
    $this->assertEqual($cli->getOptionValue('b'), 2);
    $this->assertEqual($cli->getOptionValue('zoo'), 3);
  }

  function testLongOptionsWithNonAlphabeticChars()
  {
    $argv = array('foo.php', '--foo-Bar=1', '--bar-foo_now', 2, '--zoo', 3);

    $cli = new lmbCliInput();

    $this->assertTrue($cli->read($argv));
    $this->assertEqual($cli->getOptionValue('foo-Bar'), 1);
    $this->assertEqual($cli->getOptionValue('bar-foo_now'), 2);
    $this->assertEqual($cli->getOptionValue('zoo'), 3);
  }

  function testShortOptionsWithUppercaseChars()
  {
    $argv = array('foo.php', '-B', '-C', 2);

    $cli = new lmbCliInput();

    $this->assertTrue($cli->read($argv));
    $this->assertTrue($cli->hasOption('B'));
    $this->assertEqual($cli->getOptionValue('C'), 2);
  }

  function testShortOptionsWithNumberChars()
  {
    $argv = array('foo.php', '-1', '-2');

    $cli = new lmbCliInput();

    $this->assertTrue($cli->read($argv));
    $this->assertTrue($cli->hasOption('1'));
    $this->assertTrue($cli->hasOption('2'));
    $this->assertFalse($cli->hasOption('3'));
  }

  function testReadMixedOptionsArgsComeFirst()
  {
    $argv = array('foo.php', 'arg1', '--opt1', 'opt1', 'arg2');
    $cli = new lmbCliInput();

    $this->assertTrue($cli->read($argv));
    $this->assertEqual($cli->getOptionValue('opt1'), 'opt1');
    $this->assertEqual($cli->getArguments(), array('arg1', 'arg2'));
  }

  function testShortOptionsGluing()
  {
    $argv = array('foo.php', '-ibk');

    $cli = new lmbCliInput();

    $this->assertTrue($cli->read($argv));
    $this->assertTrue($cli->hasOption('i'));
    $this->assertTrue($cli->hasOption('b'));
    $this->assertTrue($cli->hasOption('k'));
    $this->assertFalse($cli->hasOption('z'));
  }

  function testOptionsGluingWithLastValue()
  {
    $argv = array('foo.php', '-ibk', 2);

    $cli = new lmbCliInput();

    $this->assertTrue($cli->read($argv));
    $this->assertNull($cli->getOptionValue('i'));
    $this->assertNull($cli->getOptionValue('b'));
    $this->assertEqual($cli->getOptionValue('k'), 2);
  }

  function testUseRelaxedMode()
  {
    $argv = array('foo.php', 'arg1', '--opt1', 'arg2', 'arg3');
    $cli = new lmbCliInput();
    $this->assertTrue($cli->read($argv));
    $this->assertTrue($cli->hasOption('opt1'));
    $this->assertEqual($cli->getArguments(), array('arg1', 'arg3'));
  }

  function testUseRelaxedModeWithDescription()
  {
    $argv = array('foo.php', 'arg1', '--opt1', '--opt2', 'arg2', 'arg3');
    $cli = new lmbCliInput('i=');
    $this->assertTrue($cli->read($argv));
    $this->assertTrue($cli->hasOption('opt1'));
    $this->assertEqual($cli->getArguments(), array('arg1', 'arg3'));
  }
}

