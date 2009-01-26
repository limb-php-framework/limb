<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbMixable.class.php');
lmb_require('limb/core/src/lmbMixin.class.php');

class MixinFoo extends lmbMixin
{
  function foo()
  {
    return 'foo';
  }
}

class MixinBar extends lmbMixin
{
  function bar()
  {
    return 'bar';
  }
}

class MixinCallingOwnerMethod extends lmbMixin
{
  function ownerMy()
  {
    return $this->owner->my();
  }
}

class MixinCallingOwnerVar extends lmbMixin
{
  function ownerVar()
  {
    return $this->owner->_get('var');
  }
}

class MixinOverridinFoo extends lmbMixin
{
  function foo()
  {
    return 'overriden foo';
  }
}

class MixableTestVersion extends lmbMixable
{
  protected $var = 'var';

  function __construct($mixins = array())
  {
    $this->mixins = $mixins;
  }

  function my()
  {
    return 'my';
  }
}

class MixedTestStub
{
  function my()
  {
    return 'stub';
  }
}

class lmbMixableTest extends UnitTestCase
{
  function testMixinObjects()
  {
    $mixed = new lmbMixable();
    $mixed->mixin(new MixinFoo());
    $mixed->mixin(new MixinBar());
    $this->assertEqual($mixed->foo(), 'foo');
    $this->assertEqual($mixed->bar(), 'bar');
  }

  function testMixinClasses()
  {
    $mixed = new lmbMixable();
    $mixed->mixin('MixinFoo');
    $mixed->mixin('MixinBar');
    $this->assertEqual($mixed->foo(), 'foo');
    $this->assertEqual($mixed->bar(), 'bar');
  }

  function testSetOwner()
  {
    $mixed = new lmbMixable();
    $mixed->setOwner(new MixedTestStub());
    $mixed->mixin('MixinCallingOwnerMethod');
    $this->assertEqual($mixed->ownerMy(), 'stub');
  }

  function testOwnerMethodInvokation()
  {
    $mixed = new MixableTestVersion(array('MixinFoo', 'MixinBar'));
    $this->assertEqual($mixed->my(), 'my'); //native method of mixable
    $this->assertEqual($mixed->foo(), 'foo');
    $this->assertEqual($mixed->bar(), 'bar');
  }

  function testCallOwnerFromMixinForObjects()
  {
    $mixed = new MixableTestVersion(array(new MixinCallingOwnerMethod()));
    $this->assertEqual($mixed->ownerMy(), 'my');
  }

  function testCallOwnerFromMixinForClasses()
  {
    $mixed = new MixableTestVersion(array('MixinCallingOwnerMethod'));
    $this->assertEqual($mixed->ownerMy(), 'my');
  }

  function testGetOwnerVarFromMixin()
  {
    $mixed = new MixableTestVersion(array(new MixinCallingOwnerVar()));
    $this->assertEqual($mixed->ownerVar(), 'var');
  }

  function testMixinsOverriding()
  {
    $mixed = new lmbMixable();
    $mixed->mixin(new MixinFoo());
    $mixed->mixin(new MixinOverridinFoo());
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

