<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbDecorator.class.php');

interface DecorateeTestInterface
{
  function set($value);
  function get();
  function typehint(DecorateeTestStub $value);
}

class DecorateeTestStub implements DecorateeTestInterface
{
  var $value;

  function set($value)
  {
    $this->value = $value;
  }

  function get()
  {
    return $this->value;
  }

  function typehint(DecorateeTestStub $value){}
}

class lmbDecoratorTest extends UnitTestCase
{
  function testDecoratorIsInstanceOfDecoratee()
  {
    $rnd = mt_rand();
    $class = 'DecoratorTestStub' .$rnd;
    $this->assertTrue(lmbDecorator :: generate('DecorateeTestStub', $class));
    $obj = new $class(new DecorateeTestStub());
    $this->assertTrue($obj instanceof DecorateeTestStub);
  }

  function testDoubleDeclarationIsOk()
  {
    $rnd = mt_rand();
    $class = 'DecoratorTestStub' .$rnd;
    $this->assertTrue(lmbDecorator :: generate('DecorateeTestStub', $class));
    //false here means that decorator with such name already exists, it's NOT an error 
    //a bit misleading but it's simple and works :)
    $this->assertFalse(lmbDecorator :: generate('DecorateeTestStub', $class));
  }

  function testThrowsExceptionOnExistingClasses()
  {
    //exception must be thrown since lmbDecoratorTest class already exists
    try
    {
      lmbDecorator :: generate('DecorateeTestStub', 'lmbDecoratorTest');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testImplementsInterface()
  {
    $rnd = mt_rand();
    $class = 'DecoratorTestStub' .$rnd;
    $this->assertTrue(lmbDecorator :: generate('DecorateeTestStub', $class));

    $refl = new ReflectionClass($class);
    $this->assertTrue($refl->implementsInterface('DecorateeTestInterface'));
  }

  function testHasMethods()
  {
    $rnd = mt_rand();
    $class = 'DecoratorTestStub' .$rnd;
    $this->assertTrue(lmbDecorator :: generate('DecorateeTestStub', $class));

    $decorator = new $class(new DecorateeTestStub());

    foreach(get_class_methods('DecorateeTestStub') as $method)
      $this->assertTrue(method_exists($decorator, $method));
  }

  function testMethodArgumentsTypehinting()
  {
    $rnd = mt_rand();
    $class = 'DecoratorTestStub' .$rnd;
    $this->assertTrue(lmbDecorator :: generate('DecorateeTestStub', $class));

    $refl = new ReflectionClass($class);
    $params = $refl->getMethod('typehint')->getParameters();
    $this->assertEqual(sizeof($params), 1);
    $this->assertEqual($params[0]->getClass()->getName(), 'DecorateeTestStub');
  }

  function testCallsPassedToDecorated()
  {
    $rnd = mt_rand();
    $class = 'DecoratorTestStub' .$rnd;
    $this->assertTrue(lmbDecorator :: generate('DecorateeTestStub', $class));

    $decorator = new $class(new DecorateeTestStub());
    $decorator->set('foo');
    $this->assertEqual($decorator->get(), 'foo');
  }
}


