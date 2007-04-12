<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSetTest.class.php 5626 2007-04-11 11:50:45Z pachanga $
 * @package    core
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

  function testGetPropertyList()
  {
    $ds = new lmbSet();
    $ds->set('test', 'value');
    $this->assertEqual(count($ds->getPropertyList()), 1);
    $this->assertEqual($ds->getPropertyList(), array('test'));
  }

  function testImportExport()
  {
    $ds = new lmbSet();
    $ds->import($value = array('test' => 'value'));
    $this->assertEqual($ds->export(), $value);
  }

  function testRemove()
  {
    $ds = new lmbSet(array('test' => 'value'));
    $this->assertEqual($ds->get('test'), 'value');
    $ds->remove('test');
    $this->assertNull($ds->get('test'));

    $ds->remove('junk');//shouldn't produce notice
  }

  function testReset()
  {
    $ds = new lmbSet(array('test' => 'value'));
    $this->assertEqual($ds->getPropertyList(), array('test'));
    $ds->reset();
    $this->assertEqual($ds->getPropertyList(), array());
  }

  function testMerge()
  {
    $ds = new lmbSet(array('test' => 'value'));
    $ds->merge(array('foo' => 'bar'));
    $this->assertEqual($ds->getPropertyList(), array('test', 'foo'));
    $this->assertEqual($ds->get('test'), 'value');
    $this->assertEqual($ds->get('foo'), 'bar');
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

  function testImplementsIterator()
  {
    $ds = new lmbSet($array = array('test1' => 'foo',
                                          'test2' => 'bar'));

    $result = array();
    foreach($ds as $key => $value)
      $result[$key] = $value;

    $this->assertEqual($array, $result);
  }
}

?>