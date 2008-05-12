<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbObject.class.php');

class ObjectTestVersion extends lmbObject
{
  public $bar;
  protected $protected = 'me';
  public $_guarded = '';

  function getBar()
  {
    return $this->bar . '_get_called';
  }

  function setBar($value)
  {
    $this->bar = $value . '_set_called';
  }

  function isOk()
  {
    return true;
  }

  function getIsError()
  {
    return true;
  }

  function isError()
  {
    return false;
  }
}

class ObjectTestVersion2 extends lmbObject
{
}

class ObjectTestVersion3 extends lmbObject 
{
  protected $protected;
  protected $protected2;
  
  public $getter_called_count = 0;  
  public $setter_called_count = 0;
  
  function setProtected($value)
  {
    $this->setter_called_count++;
    $this->protected = $value;
  }
  
  function getProtected()
  {
    $this->getter_called_count++;
    return $this->protected;
  }
  
  function rawSet($value)
  {
    $this->_setRaw('protected', $value);
  }
  
  function rawGet()
  {
    return $this->_getRaw('protected');
  }
}

class lmbObjectTest extends UnitTestCase
{
  function testHasAttribute()
  {
    $object = new lmbObject();
    $object->set('bar', 1);

    $this->assertFalse($object->has('foo'));
    $this->assertTrue($object->has('bar'));
  }

  function testHasAttributeForNullValue()
  {
    $object = new lmbObject();
    $object->set('bar', null);

    $this->assertTrue($object->has('bar'));
  }
  
  function testHasAttributeForExistingButNullProperty()
  {
    $object = new lmbObject();
    $object->set('foo', null);
    $this->assertTrue($object->has('foo'));
  }

  function testHasAttributeForGuardedProperty()
  {
    $object = new ObjectTestVersion();
    $object->_other_guarded = 'yeah';
    $this->assertFalse($object->has('_other_guarded'));
  }

  function testHasAttributeForVirtualProperty()
  {
    $object = new ObjectTestVersion();
    $this->assertTrue($object->has('is_error'));
  }

  function testGetAttributesNames()
  {
    $object = new ObjectTestVersion();
    $this->assertEqual($object->getAttributesNames(), array('bar', 'protected'));
  }

  function testSetGet()
  {
    $object = new lmbObject();
    $object->set('foo', 1);

    $this->assertEqual($object->get('foo'), 1);
  }

  function testSetGetNullValue()
  {
    $object = new lmbObject();
    $object->set('foo', null);

    $this->assertNull($object->get('foo'));
  }
  
  function testGetWithDefaultValue()
  {
    $object = new lmbObject();
    $this->assertEqual($object->get('foo', 'bar'), 'bar');
  }

  function testCallingGetterForNoneExistingPropertyThrowsException()
  {
    $object = new lmbObject();
    try
    {
      $object->get('no_such_property');
      $this->assertTrue(false);
    }
    catch(lmbNoSuchPropertyException $e)
    {
    }
  }

  function testCallGetterForGuardedPropertyThrowsException()
  {
    $object = new ObjectTestVersion();
    $object->_other_guarded = 'yeah';

    try
    {
      $object->get('_other_guarded');
      $this->assertTrue(false);
    }
    catch(lmbNoSuchPropertyException $e)
    {
    }
  }

  function testNonExistingGetter()
  {
    $object = new lmbObject();
    $object->set('foo_bar_yo', 1);

    $this->assertEqual($object->getFooBarYo(), 1);
  }

  function testNonExistingSetter()
  {
    $object = new lmbObject();
    $object->setFooBarYo(1);

    $this->assertEqual($object->getFooBarYo(), 1);
  }

  function testCallGetterForPropertyIfItExists()
  {
    $object = new ObjectTestVersion();
    $object->bar = 'BAR';
    $this->assertEqual($object->get('bar'), 'BAR_get_called');
  }

  function testCallSetterForPropertyIfItExists()
  {
    $object = new ObjectTestVersion();
    $object->set('bar', 'BAR');
    $this->assertEqual($object->bar, 'BAR_set_called');
  }

  function testGetterForIsPropertyIsMappedToIsMethodIfItExists()
  {
    $object = new ObjectTestVersion();
    $object->set('is_ok', false);
    $this->assertTrue($object->get('is_ok'));//isOk overriden in ObjectTestVersion
  }

  function testGetterForIsPropertyIsMappedToGetIsMethodFirst()
  {
    $object = new ObjectTestVersion();
    $object->set('is_error', false);
    $this->assertTrue($object->get('is_error'));//getIsError overriden in ObjectTestVersion
  }

  function testCallingMagicGetterForNoneExistingPropertyThrowsException()
  {
    $object = new lmbObject();
    try
    {
      $object->getNoSuchPropety();
      $this->assertTrue(false);
    }
    catch(lmbNoSuchMethodException $e)
    {
    }
  }

  function testNoneExistingMethodThrowsProperException()
  {
    $object = new lmbObject();
    try
    {
      $object->noSuchMethod();
      $this->assertTrue(false);
    }
    catch(lmbNoSuchMethodException $e)
    {
    }
  }

  function testImportMergesWithExistingProps()
  {
    $object = new lmbObject();
    $object->set('foo', 'hey');
    $object->set('baz', 'wow');
    $object->import(array('foo' => 'test', 'bar' => 'test2'));

    $this->assertEqual($object->get('foo'), 'test');
    $this->assertEqual($object->get('bar'), 'test2');
    $this->assertEqual($object->get('baz'), 'wow');
  }

  function testImportIgnoresGuardedProperties()
  {
    $object = new ObjectTestVersion();
    $object->_guarded = 'yeah';
    $object->import(array('_guarded' => 'no'));
    $this->assertEqual($object->_guarded, 'yeah');
  }

  function testPassAttributesInConstructor()
  {
    $object = new lmbObject(array('foo' => 'hey', 'baz' => 'wow'));
    $this->assertEqual($object->get('foo'), 'hey');
    $this->assertEqual($object->get('baz'), 'wow');
  }

  function testExport()
  {
    $object = new lmbObject();
    $object->set('foo', 'yo-yo');
    $object->set('bar', 'zoo');

    $this->assertEqual($object->export(), array('foo' => 'yo-yo', 'bar' => 'zoo'));
  }

  function testExportOnlyNonGuardedProperties()
  {
    $object = new ObjectTestVersion();
    $object->set('foo', 'FOO');

    $this->assertEqual($object->export(), array('bar' => null, 'foo' => 'FOO', 'protected' => 'me'));
  }

  function testRemove()
  {
    $object = new lmbObject();
    $object->set('bar', 1);
    $object->set('foo', 2);

    $object->remove('bar');

    $this->assertEqual($object->get('foo'), 2);
    $this->assertTrue($object->has('foo'));
    $this->assertFalse($object->has('bar'));
  }

  function testRemoveForGuardedProperty()
  {
    $object = new ObjectTestVersion();
    $object->_guarded = 'yeah';
    $object->remove('_guarded');

    $this->assertEqual($object->_guarded, 'yeah');
  }

  function testReset()
  {
    $object = new lmbObject();
    $object->set('bar', 1);
    $object->set('foo', 2);

    $object->reset();

    $this->assertEqual($object->export(), array());
  }

  function testResetExceptGuardedProperties()
  {
    $object = new ObjectTestVersion();
    $object->_guarded = 'yeah';
    $object->reset();
    $this->assertEqual($object->_guarded, 'yeah');
  }

  function testGetHash()
  {
    $o1 = new lmbObject();
    $o2 = new lmbObject();

    $this->assertNotNull($o1->getHash());
    $this->assertEqual($o1->getHash(), $o2->getHash());
  }

  function testGetClass()
  {
    $o1 = new lmbObject();
    $this->assertEqual($o1->getClass(), 'lmbObject');

    $o2 = new ObjectTestVersion($this);
    $this->assertEqual($o2->getClass(), 'ObjectTestVersion');
  }

  function testImplementsArrayAccessInterface()
  {
    $o = new lmbObject();

    $o->set('foo', 'Bar');
    $this->assertEqual($o['foo'], 'Bar');

    $o['foo'] = 'Zoo';
    $this->assertEqual($o->get('foo'), 'Zoo');

    unset($o['foo']);
    $this->assertFalse($o->has('foo'));

    $o->set('foo', 'Bar');
    $this->assertTrue(isset($o['foo']));
    $this->assertFalse(isset($o['bar']));
  }

  function testGettersCacheWorksForDifferentClassesProperly()
  {
    $object = new ObjectTestVersion();
    $object->get('bar');
    $object2 = new ObjectTestVersion2();
    $object2->set('bar', 1);
    $object2->get('bar');
    $this->assertTrue(true);
  }
  
  function testBetterCheckForAccessByMethod()
  {
    $obj = new ObjectTestVersion3();
    $obj->protected = 'value';
    $this->assertEqual($obj->setter_called_count, 1);
    $this->assertEqual($obj->protected, 'value');
    $this->assertEqual($obj->getter_called_count, 1);

    $obj = new ObjectTestVersion3();
    $obj['protected'] = 'value';
    $this->assertEqual($obj->setter_called_count, 1);
    $this->assertEqual($obj['protected'], 'value');
    $this->assertEqual($obj->getter_called_count, 1);
    
    $obj = new ObjectTestVersion3();
    $obj->set('protected', 'value');
    $this->assertEqual($obj->setter_called_count, 1);
    $this->assertEqual($obj->get('protected'), 'value');
    $this->assertEqual($obj->getter_called_count, 1);    
  }  
  
  function testAccessByMethodForProtectedPropertiesSeveralTimes()
  {
    $obj = new ObjectTestVersion3();
    $obj->protected = 'value1';
    $obj->protected = 'value2';
    $this->assertEqual($obj->setter_called_count, 2);
    $this->assertEqual($obj->protected, 'value2');
    
    $obj = new ObjectTestVersion3();
    $obj['protected'] = 'value1';
    $obj['protected'] = 'value2';
    $this->assertEqual($obj->setter_called_count, 2);
    $this->assertEqual($obj['protected'], 'value2');
    
    $obj = new ObjectTestVersion3();
    $obj->set('protected', 'value1');
    $obj->set('protected', 'value2');
    $this->assertEqual($obj->setter_called_count, 2);
    $this->assertEqual($obj->get('protected'), 'value2');
  }
  
  function testRawSetDoNotCallTheMagick()
  {
    $obj = new ObjectTestVersion3();
    $obj->rawSet($obj->rawGet());
    $this->assertIdentical(0, $obj->setter_called_count);
    $this->assertIdentical(0, $obj->getter_called_count);
  }  
}

