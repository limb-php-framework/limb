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
    $opt = new lmbCliOption('s', lmbCliOption :: VALUE_REQ);
    $this->assertNull($opt->getLongName());
    $this->assertEqual($opt->getShortName(), 's');
    $this->assertEqual($opt->getValueMode(), lmbCliOption :: VALUE_REQ);
    $this->assertEqual($opt->toString(), '-s');
  }

  function testCreateWithLongNameOnly()
  {
    $opt = new lmbCliOption('foo', lmbCliOption :: VALUE_REQ);
    $this->assertNull($opt->getShortName());
    $this->assertEqual($opt->getLongName(), 'foo');
    $this->assertEqual($opt->getValueMode(), lmbCliOption :: VALUE_REQ);
    $this->assertEqual($opt->toString(), '--foo');
  }

  function testCreateWithBothNames()
  {
    $opt = new lmbCliOption('f', 'foo', lmbCliOption :: VALUE_REQ);
    $this->assertEqual($opt->getShortName(), 'f');
    $this->assertEqual($opt->getLongName(), 'foo');
    $this->assertEqual($opt->getValueMode(), lmbCliOption :: VALUE_REQ);
    $this->assertEqual($opt->toString(), '-f|--foo');
  }

  function testDefaultValueMode()
  {
    $opt = new lmbCliOption('s');
    $this->assertEqual($opt->getValueMode(), lmbCliOption :: VALUE_NO);

    $opt = new lmbCliOption('foo');
    $this->assertEqual($opt->getValueMode(), lmbCliOption :: VALUE_NO);

    $opt = new lmbCliOption('f', 'foo');
    $this->assertEqual($opt->getValueMode(), lmbCliOption :: VALUE_NO);
  }

  function testValueMode()
  {
    $opt = new lmbCliOption('s');
    $this->assertTrue($opt->isValueForbidden());

    $opt = new lmbCliOption('s', lmbCliOption :: VALUE_REQ);
    $this->assertTrue($opt->isValueRequired());

    $opt = new lmbCliOption('s', lmbCliOption :: VALUE_OPT);
    $this->assertTrue($opt->isValueOptional());
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

  function testIsPresent()
  {
    $opt = new lmbCliOption('f');
    $this->assertFalse($opt->isPresent());

    $opt->touch();
    $this->assertTrue($opt->isPresent());
  }

  function testIsPresentAfterSettingValue()
  {
    $opt = new lmbCliOption('f');
    $this->assertFalse($opt->isPresent());

    $opt->setValue(1);
    $this->assertTrue($opt->isPresent());
  }

  function testValidateRequiredValue()
  {
    $opt = new lmbCliOption('f', lmbCliOption :: VALUE_REQ);

    try
    {
      $opt->validate();
      $this->assertTrue(false);
    }
    catch(lmbCliException $e){}
  }

  function testValidateForbiddenValue()
  {
    $opt = new lmbCliOption('f', lmbCliOption :: VALUE_NO);
    $opt->validate(); //should pass

    $opt->setValue(1);

    try
    {
      $opt->validate();
      $this->assertTrue(false);
    }
    catch(lmbCliException $e){}
  }

}

