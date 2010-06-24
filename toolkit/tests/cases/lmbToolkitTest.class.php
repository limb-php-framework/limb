<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/toolkit/src/lmbToolkitTools.interface.php');
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/toolkit/src/lmbToolkit.class.php');

class TestTools extends lmbAbstractTools
{
  var $calls = 0;

  function commonMethod()
  {
    $this->calls++;
    return 'commonMethod1';
  }

  function getCommonMethodCalls()
  {
    return $this->calls;
  }

  function returnArg($arg)
  {
    return $arg;
  }

  function getVar()
  {
    return $this->_getRaw('var'); // this way we prevent recursion
  }

  function setVar($value)
  {
    $this->_setRaw('var', $value); // this way we prevent recursion
  }
}

class TestTools2 extends lmbAbstractTools
{
  function commonMethod()
  {
    return 'commonMethod2';
  }

  function baz()
  {
    return 'baz2';
  }
}

class lmbToolkitTest extends UnitTestCase
{
  function testInstance()
  {
    //there is a weird "recursion too deep" error on older versions of PHP
    //if(version_compare(phpversion(), '5.2.3', '>'))
    //  $this->assertIdentical(lmbToolkit :: instance(), lmbToolkit :: instance());

    $t1 = lmbToolkit :: instance();
    $t2 = lmbToolkit :: instance();
    $this->assertReference($t1, $t2);
  }

  function testNoSuchMethod()
  {
    $toolkit = new lmbToolkit();

    try
    {
      $toolkit->noSuchMethod();
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testNonExistingSetterThrowsError()
  {
    $toolkit = new lmbToolkit();

    try
    {
      $toolkit->setNonExistingStuff("bar");
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testNonExistingGetterThrowsError()
  {
    $toolkit = new lmbToolkit();

    try
    {
      $toolkit->getNonExistingStuff();
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testAddTools()
  {
    $toolkit = new lmbToolkit();
    $toolkit->add(new TestTools());
    $this->assertEqual($toolkit->commonMethod(), 'commonMethod1');
    $this->assertEqual($toolkit->returnArg('b'), 'b');
  }

  function testAddSeveralTools()
  {
    $toolkit = new lmbToolkit();
    $toolkit->add(new TestTools());
    $this->assertEqual($toolkit->commonMethod(), 'commonMethod1');
    $this->assertEqual($toolkit->returnArg('b'), 'b');

    $toolkit->add(new TestTools2());
    $this->assertEqual($toolkit->commonMethod(), 'commonMethod2');
    $this->assertEqual($toolkit->returnArg('b'), 'b');
  }

  function testSaveRestoreToolkit()
  {
    lmbToolkit :: save();

    $toolkit = lmbToolkit :: setup(new TestTools());
    $toolkit->commonMethod();
    $toolkit->commonMethod();
    $this->assertEqual($toolkit->getCommonMethodCalls(), 2);

    $toolkit = lmbToolkit :: save();
    $toolkit->commonMethod();
    $this->assertEqual($toolkit->getCommonMethodCalls(), 3);

    $toolkit = lmbToolkit :: save();
    $toolkit->commonMethod();
    $this->assertEqual($toolkit->getCommonMethodCalls(), 4);

    $toolkit = lmbToolkit :: restore();
    $this->assertEqual($toolkit->getCommonMethodCalls(), 3);

    $toolkit = lmbToolkit :: restore();
    $this->assertEqual($toolkit->getCommonMethodCalls(), 2);
    lmbToolkit :: restore();

    lmbToolkit :: restore();
  }

  function testSaveAndRestoreAlwaysReturnTheSameToolkitInstance()
  {
    lmbToolkit :: save();

    $toolkit = lmbToolkit :: setup(new TestTools());

    $toolkit1 = lmbToolkit :: save();
    $toolkit1->commonMethod();

    $toolkit2 = lmbToolkit :: restore();
    //if(version_compare(phpversion(), '5.2.3', '>'))
    //  $this->assertIdentical($toolkit1, $toolkit2);
    $this->assertReference($toolkit1, $toolkit2);

    $toolkit3 = lmbToolkit :: save();
    //if(version_compare(phpversion(), '5.2.3', '>'))
    //  $this->assertIdentical($toolkit1, $toolkit3);
    $this->assertReference($toolkit1, $toolkit3);

    lmbToolkit :: restore();
  }

  function testMerge()
  {
    lmbToolkit :: save();

    lmbToolkit :: setup(new TestTools());
    $toolkit = lmbToolkit :: merge(new TestTools2());
    $this->assertEqual($toolkit->commonMethod(), 'commonMethod2');

    lmbToolkit :: restore();
  }

  function testMergeSeveral()
  {
    lmbToolkit :: save();

    lmbToolkit :: merge(new TestTools());
    $toolkit = lmbToolkit :: save();

    $toolkit->commonMethod();
    $toolkit->commonMethod();
    $this->assertEqual($toolkit->getCommonMethodCalls(), 2);

    $toolkit = lmbToolkit :: merge(new TestTools());
    $this->assertEqual($toolkit->getCommonMethodCalls(), 0);

    $toolkit = lmbToolkit :: instance();
    $toolkit->commonMethod();
    $this->assertEqual($toolkit->getCommonMethodCalls(), 1);

    $toolkit = lmbToolkit :: restore();
    $this->assertEqual($toolkit->getCommonMethodCalls(), 0);

    lmbToolkit :: restore();
  }

  function testSetGet()
  {
    $toolkit = new lmbToolkit();
    $toolkit->set('my_var', 'value1');

    $this->assertEqual($toolkit->get('my_var'), 'value1');
  }
  
  function testGetWithDefaultValue()
  {
    $toolkit = new lmbToolkit();
    try
    {
      $toolkit->get('commonMethod');
      $this->fail();
    } catch (Exception $e) {
      $this->pass();
    }
   
    $this->assertEqual($toolkit->get('commonMethod', 'baz'), 'baz');    
  }

  function testSaveAndRestoreProperties()
  {
    lmbToolkit :: save();

    $toolkit = lmbToolkit :: instance();
    $toolkit->set('my_var', 'value1');

    lmbToolkit :: save();

    $toolkit->set('my_var', 'value2');

    lmbToolkit :: restore();

    $this->assertEqual($toolkit->get('my_var'), 'value1');

    lmbToolkit :: restore();
  }

  function testOverloadGetterByTools()
  {
    lmbToolkit :: save();

    $toolkit = lmbToolkit :: setup(new TestTools());
    $toolkit->set('var', 'value1');

    $this->assertEqual($toolkit->getVar(), 'value1');

    lmbToolkit :: save();

    $toolkit->setVar('value2');
    $this->assertEqual($toolkit->getVar(), 'value2');

    lmbToolkit :: restore();

    $this->assertEqual($toolkit->get('var'), 'value1');

    lmbToolkit :: restore();
  }
}


