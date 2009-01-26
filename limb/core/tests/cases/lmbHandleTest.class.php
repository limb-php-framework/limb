<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class lmbHandleDeclaredInSameFile
{
  var $test_var;

  function __construct($var = 'default')
  {
    $this->test_var = $var;
  }

  function foo()
  {
    return 'foo';
  }
}

class lmbHandleTest extends UnitTestCase
{
  function testDeclaredInSameFile()
  {
    $handle = new lmbHandle('lmbHandleDeclaredInSameFile');
    $this->assertIsA($handle->resolve(), 'lmbHandleDeclaredInSameFile');
  }

  function testPassMethodCalls()
  {
    $handle = new lmbHandle('lmbHandleDeclaredInSameFile');
    $this->assertEqual($handle->foo(), 'foo');
  }

  function testPassAttributes()
  {
    $handle = new lmbHandle('lmbHandleDeclaredInSameFile');
    $this->assertEqual($handle->test_var, 'default');

    $handle->test_var = 'foo';
    $this->assertEqual($handle->test_var, 'foo');
  }

  function testPassArgumentsDeclaredInSameFile()
  {
    $handle = new lmbHandle('lmbHandleDeclaredInSameFile', array('some_value'));
    $this->assertEqual($handle->test_var, 'some_value');
  }

  function testShortClassPath()
  {
    $handle = new lmbHandle(dirname(__FILE__) . '/lmbTestHandleClass');
    $this->assertIsA($handle->resolve(), 'lmbTestHandleClass');
  }

  function testShortClassPathWithExtension()
  {
    $handle = new lmbHandle(dirname(__FILE__) . '/lmbTestHandleClass.class.php');
    $this->assertIsA($handle->resolve(), 'lmbTestHandleClass');
  }

  function testShortClassPathPassArguments()
  {
    $handle = new lmbHandle(dirname(__FILE__) . '/lmbTestHandleClass', array('some_value'));
    $this->assertEqual($handle->test_var, 'some_value');
  }

  function testFullClassPath()
  {
    $handle = new lmbHandle(dirname(__FILE__) . '/handle.inc.php', array(), 'lmbLoadedHandleClass');
    $this->assertIsA($handle->resolve(), 'lmbLoadedHandleClass');
  }

  function testFullClassPathPassArguments()
  {
    $handle = new lmbHandle(dirname(__FILE__) . '/handle.inc.php', array('some_value'), 'lmbLoadedHandleClass');
    $this->assertEqual($handle->test_var, 'some_value');
    $this->assertEqual($handle->bar(), 'bar');
  }
}

