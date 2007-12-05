<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/toolkit/src/lmbToolkitTools.interface.php');
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/toolkit/src/lmbToolkit.class.php');

class TestTools extends lmbAbstractTools
{
  var $foo_counter = 0;

  function foo()
  {
    $this->foo_counter++;
    return 'a';
  }

  function getFooCounter()
  {
    return $this->foo_counter;
  }

  function bar($arg)
  {
    return $arg;
  }

  function getVar()
  {
    return $this->_getRaw('var'); // this way we can prevent recursion
  }

  function setVar($value)
  {
    $this->_setRaw('var', $value); // this way we can prevent recursion
  }
}

class TestExtendingTools implements lmbToolkitTools
{
  function getToolsSignatures()
  {
    return array('baz' => $this);
  }

  function baz()
  {
    return 'c';
  }
}

class TestIntersectingTools implements lmbToolkitTools
{
  function getToolsSignatures()
  {
    return array('baz' => $this, 'foo' => $this);
  }

  function baz()
  {
    return 'c';
  }

  function foo()
  {
    return 'd';
  }
}

class lmbToolkitTest extends UnitTestCase
{
  function setUp()
  {
    lmbToolkit :: save();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testInstance()
  {
    return;
    $this->assertReference(lmbToolkit :: instance(),
                           lmbToolkit :: instance());
  }

  function testNoSuchMethod()
  {
    $toolkit = lmbToolkit :: setup(new TestTools());

    try
    {
      $toolkit->noSuchMethod();
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testUseTools()
  {
    $toolkit = lmbToolkit :: setup(new TestTools());
    $this->assertEqual($toolkit->foo(), 'a');
    $this->assertEqual($toolkit->bar('b'), 'b');
  }

  function testSaveRestoreToolkit()
  {
    $toolkit = lmbToolkit :: setup(new TestTools());
    $toolkit->foo();
    $toolkit->foo();
    $this->assertEqual($toolkit->getFooCounter(), 2);

    $toolkit = lmbToolkit :: save();
    $toolkit->foo();
    $this->assertEqual($toolkit->getFooCounter(), 1);
    $toolkit = lmbToolkit :: restore();

    $toolkit->foo();
    $this->assertEqual($toolkit->getFooCounter(), 3);

    $toolkit = lmbToolkit :: save();
    $toolkit->foo();
    $this->assertEqual($toolkit->getFooCounter(), 1);
    $toolkit = lmbToolkit :: restore();
  }

  function testSaveAndRestoreAlwaysReturnTheSameToolkitInstance()
  {
    $toolkit = lmbToolkit :: setup(new TestTools());
    $toolkit1 = lmbToolkit :: save();
    $toolkit1->foo();
    $toolkit2 = lmbToolkit :: restore();
    $this->assertReference($toolkit1, $toolkit2);
    $toolkit3 = lmbToolkit :: save();
    $this->assertReference($toolkit1, $toolkit3);
    lmbToolkit :: restore();
  }

  function testExtendToolkit()
  {
    $toolkit = lmbToolkit :: setup(new TestTools());
    $toolkit->foo();
    $toolkit->foo();
    $this->assertEqual($toolkit->getFooCounter(), 2);

    try
    {
      $toolkit->baz();
      $this->assertTrue(false);
    }
    catch(lmbException $e){}

    $toolkit = lmbToolkit :: extend(new TestExtendingTools());
    $this->assertEqual($toolkit->baz(), 'c');

    $toolkit->foo();
    $this->assertEqual($toolkit->getFooCounter(), 1);
  }

  function testExtendPreserveCleanCopy()
  {
    lmbToolkit :: setup(new TestTools());
    $toolkit = lmbToolkit :: instance();
    $toolkit->foo();
    $toolkit->foo();
    $this->assertEqual($toolkit->getFooCounter(), 2);

    lmbToolkit :: extend(new TestExtendingTools());
    $toolkit = lmbToolkit :: instance();
    $this->assertEqual($toolkit->getFooCounter(), 0);

    lmbToolkit :: save();
    $toolkit = lmbToolkit :: instance();
    $toolkit->foo();
    $this->assertEqual($toolkit->getFooCounter(), 1);
    lmbToolkit :: restore();

    $toolkit = lmbToolkit :: instance();
    $this->assertEqual($toolkit->getFooCounter(), 0);
  }

  function testIntersectingToolkit()
  {
    lmbToolkit :: setup(new TestTools());
    $toolkit = lmbToolkit :: extend(new TestIntersectingTools());

    try
    {
      $toolkit->foo(); //we must call this method due to lazy loading of signatures
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testSubstitute()
  {
    lmbToolkit :: setup(new TestTools());
    $toolkit = lmbToolkit :: merge(new TestIntersectingTools());
    $this->assertEqual($toolkit->foo(), 'd');
  }

  function testSubstitutePreserveCleanCopy()
  {
    lmbToolkit :: setup(new TestTools());
    $toolkit = lmbToolkit :: instance();
    $toolkit->foo();
    $toolkit->foo();
    $this->assertEqual($toolkit->getFooCounter(), 2);

    lmbToolkit :: merge(new TestTools());
    $toolkit = lmbToolkit :: instance();
    $this->assertEqual($toolkit->getFooCounter(), 0);

    lmbToolkit :: save();
    $toolkit = lmbToolkit :: instance();
    $toolkit->foo();
    $this->assertEqual($toolkit->getFooCounter(), 1);
    lmbToolkit :: restore();

    $toolkit = lmbToolkit :: instance();
    $this->assertEqual($toolkit->getFooCounter(), 0);
  }

  function testSetGet()
  {
    $toolkit = lmbToolkit :: instance();
    $toolkit->set('my_var', 'value1');

    $this->assertEqual($toolkit->get('my_var'), 'value1');
  }
  
  function testGetWithDefaultValue()
  {
    $toolkit = lmbToolkit :: instance();
    try
    {
      $toolkit->get('foo');
      $this->fail();
    } catch (Exception $e) {
      $this->pass();
    }
   
    $this->assertEqual($toolkit->get('foo', 'baz'), 'baz');    
  }

  function testSaveAndRestoreProperties()
  {
    $toolkit = lmbToolkit :: instance();
    $toolkit->set('my_var', 'value1');

    lmbToolkit :: save();

    $toolkit->set('my_var', 'value2');

    lmbToolkit :: restore();

    $this->assertEqual($toolkit->get('my_var'), 'value1');
  }

  function testOverloadGetterByTools()
  {
    $toolkit = lmbToolkit :: setup(new TestTools());
    $toolkit->set('var', 'value1');

    $this->assertEqual($toolkit->getVar(), 'value1');

    lmbToolkit :: save();

    $toolkit->setVar('value2');
    $this->assertEqual($toolkit->getVar(), 'value2');

    lmbToolkit :: restore();

    $this->assertEqual($toolkit->get('var'), 'value1');
  }
}


