<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDataspaceTest.class.php 4992 2007-02-08 15:35:40Z pachanga $
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
    $this->assertTrue($ds->hasProperty('test'));
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
    $ds->removeAll();
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

  function testGetByPathFromEmptyDataspace()
  {
    $ds = new lmbDataspace();
    $this->assertNull($ds->getByPath('foo'));
    $this->assertNull($ds->getByPath('foo.var'));
  }

  function testGetByPath()
  {
    $ds = new lmbDataspace();
    $ds->set('foo', array('x' => 'a', 'y' => 'b'));
    $this->assertEqual($ds->getByPath('foo'), array('x' => 'a', 'y' => 'b'));
    $this->assertEqual($ds->getByPath('foo.x'), 'a');
    $this->assertEqual($ds->getByPath('foo.y'), 'b');
    $this->assertNull($ds->getByPath('foo.z'));
    $this->assertNull($ds->getByPath('zoo.aaa'));
  }

  function testSetPath()
  {
    $ds = new lmbDataspace();
    $ds->set('foo', array('x' => 'a', 'y' => 'b'));
    $this->assertEqual($ds->getByPath('foo.x'), 'a');
    $ds->setByPath('foo.x', 'c');
    $this->assertEqual($ds->getByPath('foo.x'), 'c');
  }

  function testSetPathNestedObjects()
  {
    $value = new lmbDataspaceTestObject();
    $nested = new lmbDataspaceTestObject();

    $ds = new lmbDataspace();
    $ds->set('foo', $nested);

    $get_obj = $ds->setByPath('foo.var', $value);
    $this->assertIdentical($nested->var, $value);
  }

  function testGetPathDataspaceObject()
  {
    $value = new lmbDataspace(array('x' => 'a'));

    $ds = new lmbDataspace();
    $ds->set('foo', array('var' => $value));

    $get_obj = $ds->getByPath('foo.var');
    $this->assertTrue(is_object($get_obj));
    $this->assertEqual($get_obj, $value);

    $this->assertEqual($ds->getByPath('foo.var.x'), 'a');
  }

  function testGetPathNestedObject()
  {
    $value = new lmbDataspaceTestObject();
    $nested = new lmbDataspaceTestObject();
    $nested->var = $value;

    $ds = new lmbDataspace();
    $ds->set('foo', $nested);

    $get_obj = $ds->getByPath('foo.var');
    $this->assertTrue(is_object($get_obj));
    $this->assertIsA($get_obj, 'lmbDataspaceTestObject');
    $this->assertIdentical($get_obj, $value);
  }

  function testGetPathNestedObjectAndDataspaceObject()
  {
    $value = new lmbDataspace(array('x' => 'a'));
    $nested = new lmbDataspaceTestObject();
    $nested->var = $value;

    $ds = new lmbDataspace();
    $ds->set('foo', $nested);

    $this->assertEqual($ds->getByPath('foo'), $nested);
    $this->assertEqual($ds->getByPath('foo.var'), $value);
    $this->assertEqual($ds->getByPath('foo.var.x'), 'a');
  }

  function testGetPathNestedDataspaceObjects()
  {
    $value = new lmbDataspace(array('x' => 'a'));
    $nested = new lmbDataspace(array('y' => $value));
    $nested->var = $value;

    $ds = new lmbDataspace();
    $ds->set('foo', $nested);

    $this->assertEqual($ds->getByPath('foo'), $nested);
    $this->assertEqual($ds->getByPath('foo.y'), $value);
    $this->assertEqual($ds->getByPath('foo.y.x'), 'a');
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