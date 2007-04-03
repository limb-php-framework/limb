<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbHandleTest.class.php 4987 2007-02-08 15:35:15Z pachanga $
 * @package    classkit
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
    $this->assertIsA(lmbProxyResolver :: resolve($handle), 'lmbHandleDeclaredInSameFile');
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
    $this->assertIsA(lmbProxyResolver :: resolve($handle), 'lmbTestHandleClass');
  }

  function testShortClassPathWithExtension()
  {
    $handle = new lmbHandle(dirname(__FILE__) . '/lmbTestHandleClass.class.php');
    $this->assertIsA(lmbProxyResolver :: resolve($handle), 'lmbTestHandleClass');
  }

  function testShortClassPathPassArguments()
  {
    $handle = new lmbHandle(dirname(__FILE__) . '/lmbTestHandleClass', array('some_value'));
    $this->assertEqual($handle->test_var, 'some_value');
  }

  function testFullClassPath()
  {
    $handle = new lmbHandle(dirname(__FILE__) . '/handle.inc.php', array(), 'lmbLoadedHandleClass');
    $this->assertIsA(lmbProxyResolver :: resolve($handle), 'lmbLoadedHandleClass');
  }

  function testFullClassPathPassArguments()
  {
    $handle = new lmbHandle(dirname(__FILE__) . '/handle.inc.php', array('some_value'), 'lmbLoadedHandleClass');
    $this->assertEqual($handle->test_var, 'some_value');
    $this->assertEqual($handle->bar(), 'bar');
  }
}
?>