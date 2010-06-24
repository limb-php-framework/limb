<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cli/src/lmbCliOption.class.php');


class lmbCliOptionTest extends UnitTestCase
{
  function testCreateException()
  {
    try
    {
      $opt = new lmbCliOption('foo', 'f');
      $this->assertTrue(false);
    }
    catch(lmbCliException $e){}
  }

  function testCreateWithShortNameOnly()
  {
    $opt = new lmbCliOption('s');
    $this->assertNull($opt->getLongName());
    $this->assertEqual($opt->getName(), 's');
    $this->assertEqual($opt->getShortName(), 's');
    $this->assertEqual($opt->toString(), '-s');
  }

  function testCreateWithLongNameOnly()
  {
    $opt = new lmbCliOption('foo');
    $this->assertNull($opt->getShortName());
    $this->assertEqual($opt->getName(), 'foo');
    $this->assertEqual($opt->getLongName(), 'foo');
    $this->assertEqual($opt->toString(), '--foo');
  }

  function testCreateWithBothNames()
  {
    $opt = new lmbCliOption('f', 'foo');
    $this->assertEqual($opt->getName(), 'foo');
    $this->assertEqual($opt->getLongName(), 'foo');
    $this->assertEqual($opt->toString(), '-f|--foo');
  }

  function testMatchSingleName()
  {
    $opt = new lmbCliOption('s');
    $this->assertTrue($opt->match('s'));
    $this->assertFalse($opt->match('b'));

    $opt = new lmbCliOption('foo');
    $this->assertTrue($opt->match('foo'));
    $this->assertFalse($opt->match('aaa'));
  }

  function testMatchAnyName()
  {
    $opt = new lmbCliOption('f', 'foo');
    $this->assertTrue($opt->match('foo'));
    $this->assertTrue($opt->match('f'));
  }

  function testGetSetValue()
  {
    $opt = new lmbCliOption('f');

    $this->assertNull($opt->getValue());

    $opt->setValue('wow');
    $this->assertEqual($opt->getValue(), 'wow');
  }

}

