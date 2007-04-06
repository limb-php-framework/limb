<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDataspaceTest.class.php 5558 2007-04-06 13:02:07Z pachanga $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbDataspace.class.php');

class lmbDataSpaceTestObject
{
  public $var;
}

class lmbDataspaceTest extends UnitTestCase
{
  function testGetFromEmptyDataspace()
  {
    $ds = new lmbDataspace();
    $this->assertNull($ds->get('test'));
  }

  function testSetAndGet()
  {
    $ds = new lmbDataspace();
    $ds->set('test', 'value');
    $this->assertTrue($ds->has('test'));
    $this->assertEqual($ds->get('test'), 'value');
  }

  function testGetInteger()
  {
    $ds = new lmbDataspace();
    $ds->set('test', '10b');
    $this->assertIdentical($ds->getInteger('test'), 10);
  }

  function testGetNumeric()
  {
    $ds = new lmbDataspace();
    $ds->set('test', '10.1');
    $this->assertIdentical($ds->getNumeric('test'), 10.1);
  }

  function testGetArrayForScalars()
  {
    $ds = new lmbDataspace();
    $ds->set('test', 'foo');
    $this->assertIdentical($ds->getArray('test'), array());
  }

  function testGetArray()
  {
    $ds = new lmbDataspace();
    $ds->set('test', array('foo'));
    $this->assertIdentical($ds->getArray('test'), array('foo'));
  }

  function testGetPropertyList()
  {
    $ds = new lmbDataspace();
    $ds->set('test', 'value');
    $this->assertEqual(count($ds->getPropertyList()), 1);
    $this->assertEqual($ds->getPropertyList(), array('test'));
  }

  function testImportExport()
  {
    $ds = new lmbDataspace();
    $ds->import($value = array('test' => 'value'));
    $this->assertEqual($ds->export(), $value);
  }

  function testRemove()
  {
    $ds = new lmbDataspace(array('test' => 'value'));
    $this->assertEqual($ds->get('test'), 'value');
    $ds->remove('test');
    $this->assertNull($ds->get('test'));

    $ds->remove('junk');//shouldn't produce notice
  }

  function testRemoveAll()
  {
    $ds = new lmbDataspace(array('test' => 'value'));
    $this->assertEqual($ds->getPropertyList(), array('test'));
    $ds->reset();
    $this->assertEqual($ds->getPropertyList(), array());
  }

  function testMerge()
  {
    $ds = new lmbDataspace(array('test' => 'value'));
    $ds->merge(array('foo' => 'bar'));
    $this->assertEqual($ds->getPropertyList(), array('test', 'foo'));
    $this->assertEqual($ds->get('test'), 'value');
    $this->assertEqual($ds->get('foo'), 'bar');
  }

  function testImplementsArrayAccessInterface()
  {
    $o = new lmbDataspace();

    $o->set('foo', 'Bar');
    $this->assertEqual($o['foo'], 'Bar');

    $o['foo'] = 'Zoo';
    $this->assertEqual($o->get('foo'), 'Zoo');

    unset($o['foo']);
    $this->assertNull($o->get('foo'));

    $o->set('foo', 'Bar');
    $this->assertTrue(isset($o['foo']));
    $this->assertFalse(isset($o['bar']));
  }
}

?>