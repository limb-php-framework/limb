<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbProxyResolverTest.class.php 4987 2007-02-08 15:35:15Z pachanga $
 * @package    classkit
 */
lmb_require('limb/classkit/src/lmbObject.class.php');
lmb_require('limb/classkit/src/lmbProxy.class.php');
lmb_require('limb/classkit/src/lmbProxyResolver.class.php');

Mock :: generatePartial('lmbProxy',
                        'ProxyResolverTestVersion',
                        array('_createOriginalObject'));

class lmbProxyResolverTest extends UnitTestCase
{
  function testResolveNonProxy()
  {
    $object = new lmbObject();
    $this->assertReference($object, lmbProxyResolver :: resolve($object));
  }

  function testDirectResolveCall()
  {
    $proxy = new ProxyResolverTestVersion($this);
    $proxy->expectOnce('_createOriginalObject');
    $proxy->setReturnValue('_createOriginalObject', $obj = new lmbObject());

    $this->assertEqual($proxy->resolve(), $obj);
    $this->assertEqual($proxy->resolve(), $obj); //checking caching
  }

  function testResolveObjectAttributeProxy()
  {
    $proxy = new ProxyResolverTestVersion($this);

    $object = new lmbObject();
    $object->proxy = $proxy;

    $proxy->expectOnce('_createOriginalObject');
    $proxy->setReturnValue('_createOriginalObject', new lmbObject());

    $result = lmbProxyResolver :: resolve($object);

    $expected = new lmbObject();
    $expected->proxy = new lmbObject();

    $this->assertEqual($result, $expected);
  }

  function testResolveProxyArray()
  {
    $proxy1 = new ProxyResolverTestVersion($this);
    $proxy2 = new ProxyResolverTestVersion($this);

    $object = new lmbObject();
    $object->arr = array('foo' => $proxy1, 'bar' => $proxy2);

    $proxy1->expectOnce('_createOriginalObject');
    $proxy1->setReturnValue('_createOriginalObject', new lmbObject());

    $proxy2->expectOnce('_createOriginalObject');
    $proxy2->setReturnReference('_createOriginalObject', new lmbObject());

    $result = lmbProxyResolver :: resolve($object);

    $expected = new lmbObject();
    $expected->arr = array('foo' => new lmbObject(), 'bar' => new lmbObject());

    $this->assertEqual($result, $expected);
  }

  function testResolveWithCyclicDependencies()
  {
    $parent = new lmbObject();
    $child = new lmbObject();

    $parent->child = $child;
    $child->parent = $parent;

    $this->assertReference($parent, lmbProxyResolver :: resolve($parent));
  }
}

?>