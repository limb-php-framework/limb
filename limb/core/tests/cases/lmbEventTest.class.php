<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2008 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/core/src/lmbEvent.class.php');

class EventTestingStubObject 
{
  
  public $invoked = 0;
  public $last_arg = null;
  public $last_arg2 = null;
  
  protected $return;
  
  function __construct($return = 'invoked')
  {
    $this->return = $return;
  }
  
  function invokable($arg = null, $arg2 = null)
  {
    $this->invoked ++;
    $this->last_arg = $arg;
    $this->last_arg2 = $arg2;
    return $this->return; 
  }
  
}

class lmbEventTest extends UnitTestCase {
  
  function testInvokeAll()
  {
    $obj1 = new EventTestingStubObject();
    $obj2 = new EventTestingStubObject();
    
    $event = new lmbEvent();    
    $event->add(new lmbDelegate($obj1, 'invokable'));
    $event->add(new lmbDelegate($obj2, 'invokable'));
    
    $event->invokeAll('invoked');
    $this->assertEqual($obj1->invoked, 1);
    $this->assertEqual($obj2->invoked, 1);
    $this->assertEqual($obj1->last_arg, 'invoked');
    $this->assertEqual($obj2->last_arg, 'invoked');
  }
  
  function testInvokeChain()
  {    
    $obj1 = new EventTestingStubObject(null);
    $obj2 = new EventTestingStubObject('result');
    $obj3 = new EventTestingStubObject();
    
    $event = new lmbEvent();    
    $event->add(new lmbDelegate($obj1, 'invokable'));
    $event->add(new lmbDelegate($obj2, 'invokable'));
    $event->add(new lmbDelegate($obj3, 'invokable'));
    
    $result = $event->invokeChain('invoked');
    $this->assertEqual($result, 'result');
    $this->assertEqual($obj1->invoked, 1);
    $this->assertEqual($obj2->invoked, 1);
    $this->assertEqual($obj3->invoked, 0);
    $this->assertEqual($obj1->last_arg, 'invoked');
    $this->assertEqual($obj2->last_arg, 'invoked');
  }
  
  function testFind()
  {
    $obj1 = new EventTestingStubObject();
    $obj2 = new EventTestingStubObject();
    
    $event = new lmbEvent();    
    $event->add(new lmbDelegate($obj1, 'invokable'));
    $event->add(new lmbDelegate($obj2, 'invokable'));
    
    $num = $event->find(array($obj2, 'invokable'));
    $this->assertEqual($num, 1);
    
    $num = $event->find(array(new EventTestingStubObject(), 'invokable'));
    $this->assertFalse($num);
  }
  
  function testExists()
  {
    $obj1 = new EventTestingStubObject();
    $obj2 = new EventTestingStubObject();
    
    $event = new lmbEvent();    
    $event->add(new lmbDelegate($obj1, 'invokable'));
    $event->add(new lmbDelegate($obj2, 'invokable'));
    
    $result = $event->exists(array($obj2, 'invokable'));
    $this->assertTrue($result);
    
    $result = $event->exists(array(new EventTestingStubObject(), 'invokable'));
    $this->assertFalse($result);
  }
  
  function testRemove()
  {
    $obj1 = new EventTestingStubObject();
    $obj2 = new EventTestingStubObject();
    
    $event = new lmbEvent();    
    $event->add(new lmbDelegate($obj1, 'invokable'));
    $event->add(new lmbDelegate($obj2, 'invokable'));
    
    $event->invokeAll();
    $event->remove(new lmbDelegate($obj2, 'invokable'));
    $event->invokeAll();
    $this->assertEqual($obj1->invoked, 2);
    $this->assertEqual($obj2->invoked, 1);
  }
  
  function testPassingInvokeArgs()
  {
    $obj = new EventTestingStubObject();
    
    $event = new lmbEvent();    
    $event->add(new lmbDelegate($obj, 'invokable'));
    
    $event->invokeAll('arg1', 'arg2');
    $this->assertEqual($obj->last_arg, 'arg1');
    $this->assertEqual($obj->last_arg2, 'arg2');
  }
  
}
?>