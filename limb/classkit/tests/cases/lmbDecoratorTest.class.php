<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDecoratorTest.class.php 4987 2007-02-08 15:35:15Z pachanga $
 * @package    classkit
 */
lmb_require('limb/classkit/src/lmbDecorator.class.php');

interface DecoratorTestInterface
{
  function set($value);
  function get();
  function typehint(DecoratorTestStub $value);
}

class DecoratorTestStub implements DecoratorTestInterface
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

  function typehint(DecoratorTestStub $value){}
}

lmbDecorator :: generate('DecoratorTestStub', 'DecoratorTestStubDecorator');

class lmbDecoratorTest extends UnitTestCase
{
  function testDoubleDeclaration()
  {
    lmbDecorator :: generate('DecoratorTestStub', 'DecoratorTestStubDecorator');
    lmbDecorator :: generate('DecoratorTestStub', 'DecoratorTestStubDecorator');
  }

  function testImplementsInterface()
  {
    $refl = new ReflectionClass('DecoratorTestStubDecorator');
    $this->assertTrue($refl->implementsInterface('DecoratorTestInterface'));
  }

  function testHasMethods()
  {
    $decorator = new DecoratorTestStubDecorator(new DecoratorTestStub());

    foreach(get_class_methods('DecoratorTestStub') as $method)
      $this->assertTrue(method_exists($decorator, $method));
  }

  function testMethodArgumentsTypehinting()
  {
    $refl = new ReflectionClass('DecoratorTestStubDecorator');
    $params = $refl->getMethod('typehint')->getParameters();
    $this->assertEqual(sizeof($params), 1);
    $this->assertEqual($params[0]->getClass()->getName(), 'DecoratorTestStub');
  }

  function testCallsPassedToDecorated()
  {
    $decorator = new DecoratorTestStubDecorator(new DecoratorTestStub());
    $decorator->set('foo');
    $this->assertEqual($decorator->get(), 'foo');
  }
}

?>