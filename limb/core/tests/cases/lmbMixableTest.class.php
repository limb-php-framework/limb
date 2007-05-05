<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbObjectTest.class.php 5567 2007-04-06 14:37:24Z serega $
 * @package    core
 */
lmb_require('limb/core/src/lmbMixable.class.php');
lmb_require('limb/core/src/lmbMixin.class.php');

class FooMixin extends lmbMixin
{
  function foo()
  {
    return 'foo';
  }
}

class BarMixin extends lmbMixin
{
  function bar()
  {
    return 'bar';
  }
}

class OwnerCallingMixin extends lmbMixin
{
  function ownerMy()
  {
    return $this->owner->my();
  }
}

class FooOverridingMixin extends lmbMixin
{
  function foo()
  {
    return 'overriden foo';
  }
}

class MixableTestVersion extends lmbMixable
{
  function __construct($mixins)
  {
    $this->mixins = $mixins;
  }

  function my()
  {
    return 'my';
  }
}

class lmbMixableTest extends UnitTestCase
{
  function testMixinObjects()
  {
    $mixed = new lmbMixable();
    $mixed->mixin(new FooMixin());
    $mixed->mixin(new BarMixin());
    $this->assertEqual($mixed->foo(), 'foo');
    $this->assertEqual($mixed->bar(), 'bar');
  }

  function testMixinClasses()
  {
    $mixed = new lmbMixable();
    $mixed->mixin('FooMixin');
    $mixed->mixin('BarMixin');
    $this->assertEqual($mixed->foo(), 'foo');
    $this->assertEqual($mixed->bar(), 'bar');
  }

  function testOwnerMethodInvokation()
  {
    $mixed = new MixableTestVersion(array('FooMixin', 'BarMixin'));
    $this->assertEqual($mixed->my(), 'my'); //native method of mixable
    $this->assertEqual($mixed->foo(), 'foo');
    $this->assertEqual($mixed->bar(), 'bar');
  }

  function testCallOwnerFromMixinForObjects()
  {
    //we need to ensure owner is set in mixin
    $mixed = new MixableTestVersion(array(new OwnerCallingMixin()));
    $this->assertEqual($mixed->ownerMy(), 'my');
  }

  function testCallOwnerFromMixinForClasses()
  {
    //we need to ensure owner is set in mixin
    $mixed = new MixableTestVersion(array('OwnerCallingMixin'));
    $this->assertEqual($mixed->ownerMy(), 'my');
  }

  function testMixinsOverriding()
  {
    $mixed = new lmbMixable();
    $mixed->mixin(new FooMixin());
    $mixed->mixin(new FooOverridingMixin());
    $this->assertEqual($mixed->foo(), 'overriden foo');
  }

  function testNoSuchMethodThrowsException()
  {
    $mixed = new lmbMixable();

    try
    {
      $mixed->hey();
      $this->assertFalse(true);
    }
    catch(lmbException $e){}
  }
}
?>