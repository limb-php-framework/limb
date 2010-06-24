<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbSet.class.php');

class lmbSetTestObject
{
  public $var;
}

class lmbSetTest extends UnitTestCase
{
  function testGetFromEmptySet()
  {
    $ds = new lmbSet();
    $this->assertNull($ds->get('test'));
  }

  function testSetAndGet()
  {
    $ds = new lmbSet();
    $ds->set('test', 'value');
    $this->assertTrue($ds->has('test'));
    $this->assertEqual($ds->get('test'), 'value');
  }
  
  function testGetWithDefaultValue()
  {
    $ds = new lmbSet();    
    $this->assertEqual($ds->get('test', 'default'), 'default');
  }

  function testSetAndGetForGuardedProperty()
  {
    $ds = new lmbSet();
    $ds->_test = 10;
    $ds->set('_test', 100);
    $this->assertNull($ds->get('_test'));
    $this->assertEqual($ds->_test, 10);
  }

  function testGetInteger()
  {
    $ds = new lmbSet();
    $ds->set('test', '10b');
    $this->assertIdentical($ds->getInteger('test'), 10);
  }

  function testGetNumeric()
  {
    $ds = new lmbSet();
    $ds->set('test', '10.1');
    $this->assertIdentical($ds->getNumeric('test'), 10.1);
  }

  function testGetArrayForScalars()
  {
    $ds = new lmbSet();
    $ds->set('test', 'foo');
    $this->assertIdentical($ds->getArray('test'), array());
  }

  function testGetArray()
  {
    $ds = new lmbSet();
    $ds->set('test', array('foo'));
    $this->assertIdentical($ds->getArray('test'), array('foo'));
  }
  
  function testGetFloat()
  {
    $ds = new lmbSet();
    $ds->set('test', '3.14');
    $this->assertIdentical($ds->getFloat('test'), 3.14);
  }
  
  function testGetCorrectedFloat()
  {
    $ds = new lmbSet();
    $ds->set('test', '3,14');
    $this->assertIdentical($ds->getFloat('test'), 3.14);
  }

  function testGetPropertyList()
  {
    $ds = new lmbSet();
    $ds->set('test', 'value');
    $this->assertEqual(count($ds->getPropertyList()), 1);
    $this->assertEqual($ds->getPropertyList(), array('test'));
  }

  function testGetPropertyListWithGuardedProps()
  {
    $ds = new lmbSet();
    $ds->test = 'value';
    $ds->_test = 'value2';
    $this->assertEqual(count($ds->getPropertyList()), 1);
    $this->assertEqual($ds->getPropertyList(), array('test'));
  }

  function testImportExport()
  {
    $ds = new lmbSet();
    $ds->import($value = array('test' => 'value'));
    $this->assertEqual($ds->export(), $value);
  }

  function testImportExportWithGuardedProps()
  {
    $ds = new lmbSet();
    $ds->_test = 'value2';
    $ds->import(array('test' => 'value', '_test' => 'junk'));
    $this->assertEqual($ds->export(), array('test' => 'value'));
    $this->assertEqual($ds->_test, 'value2');
  }

  function testRemove()
  {
    $ds = new lmbSet(array('test' => 'value'));
    $this->assertEqual($ds->get('test'), 'value');
    $ds->remove('test');
    $this->assertNull($ds->get('test'));

    $ds->remove('junk');//shouldn't produce notice
  }

  function testRemoveGuardedProperty()
  {
    $ds = new lmbSet();
    $ds->_test = 1;
    $ds->remove('_test');
    $this->assertEqual($ds->_test, 1);
  }

  function testReset()
  {
    $ds = new lmbSet(array('test' => 'value'));
    $this->assertEqual($ds->getPropertyList(), array('test'));
    $ds->reset();
    $this->assertEqual($ds->getPropertyList(), array());
  }

  function testResetWithGuardedProps()
  {
    $ds = new lmbSet();
    $ds->_test = 10;
    $ds->reset();
    $this->assertEqual($ds->_test, 10);
  }

  function testMerge()
  {
    $ds = new lmbSet(array('test' => 'value'));
    $ds->merge(array('foo' => 'bar'));
    $this->assertEqual($ds->getPropertyList(), array('test', 'foo'));
    $this->assertEqual($ds->get('test'), 'value');
    $this->assertEqual($ds->get('foo'), 'bar');
  }

  function testMergeWithGuardedProps()
  {
    $ds = new lmbSet(array('test' => 'value'));
    $ds->_test = 100;
    $ds->merge(array('foo' => 'bar', '_test' => 10));
    $this->assertEqual($ds->getPropertyList(), array('test', 'foo'));
    $this->assertEqual($ds->get('test'), 'value');
    $this->assertEqual($ds->get('foo'), 'bar');
    $this->assertEqual($ds->_test, 100);
  }

  function testImplementsArrayAccessInterface()
  {
    $ds = new lmbSet();

    $ds->set('foo', 'Bar');
    $this->assertEqual($ds['foo'], 'Bar');

    $ds['foo'] = 'Zoo';
    $this->assertEqual($ds->get('foo'), 'Zoo');

    unset($ds['foo']);
    $this->assertNull($ds->get('foo'));

    $ds->set('foo', 'Bar');
    $this->assertTrue(isset($ds['foo']));
    $this->assertFalse(isset($ds['bar']));
  }
  
  function testImplementsMagicGetSetUnsetMethods()
  {
    $ds = new lmbSet();

    $ds->set('foo', 'Bar');
    $this->assertEqual($ds->foo, 'Bar');

    $ds->foo = 'Zoo';
    $this->assertEqual($ds->foo, 'Zoo');

    unset($ds->foo);
    $this->assertFalse(property_exists($ds, 'foo'));

    $ds->set('foo', 'Bar');
    $this->assertTrue(isset($ds->foo));
    $this->assertFalse(isset($ds->bar));
  }  

  function testImplementsIterator()
  {
    $ds = new lmbSet($array = array('test1' => 'foo',
                                          'test2' => 'bar'));

    $result = array();
    foreach($ds as $key => $value)
      $result[$key] = $value;

    $this->assertEqual($array, $result);
  }

  function testImplementsIteratorWithFalseElementsInArray()
  {
    $ds = new lmbSet($array = array('test1' => 'foo',
                                    'test2' => false,
                                    'test3' => 'bar'));

    $result = array();
    foreach($ds as $key => $value)
      $result[$key] = $value;

    $this->assertEqual($array, $result);
  }
}


