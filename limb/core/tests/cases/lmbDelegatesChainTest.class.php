<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2008 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/core/src/lmbDelegatesChain.class.php');

class DelegatesChainTestingStubObject 
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

class lmbDelegatesChainTest extends UnitTestCase {
  
  function testInvoke()
  {    
    $obj1 = new DelegatesChainTestingStubObject(null);
    $obj2 = new DelegatesChainTestingStubObject('result');
    $obj3 = new DelegatesChainTestingStubObject();
    
    $chain = new lmbDelegatesChain();    
    $chain->add(new lmbDelegate($obj1, 'invokable'));
    $chain->add(new lmbDelegate($obj2, 'invokable'));
    $chain->add(new lmbDelegate($obj3, 'invokable'));
    
    $result = $chain->invoke('invoked');
    $this->assertEqual($result, 'result');
    $this->assertEqual($obj1->invoked, 1);
    $this->assertEqual($obj2->invoked, 1);
    $this->assertEqual($obj3->invoked, 0);
    $this->assertEqual($obj1->last_arg, 'invoked');
    $this->assertEqual($obj2->last_arg, 'invoked');
  }
  
  function testFind()
  {
    $obj1 = new DelegatesChainTestingStubObject();
    $obj2 = new DelegatesChainTestingStubObject();
    
    $chain = new lmbDelegatesChain();    
    $chain->add(new lmbDelegate($obj1, 'invokable'));
    $chain->add(new lmbDelegate($obj2, 'invokable'));
    
    $num = $chain->find(array($obj2, 'invokable'));
    $this->assertEqual($num, 1);
    
    $num = $chain->find(array(new DelegatesChainTestingStubObject(), 'invokable'));
    $this->assertFalse($num);
  }
  
  function testExists()
  {
    $obj1 = new DelegatesChainTestingStubObject();
    $obj2 = new DelegatesChainTestingStubObject();
    
    $chain = new lmbDelegatesChain();    
    $chain->add(new lmbDelegate($obj1, 'invokable'));
    $chain->add(new lmbDelegate($obj2, 'invokable'));
    
    $result = $chain->exists(array($obj2, 'invokable'));
    $this->assertTrue($result);
    
    $result = $chain->exists(array(new DelegatesChainTestingStubObject(), 'invokable'));
    $this->assertFalse($result);
  }
  
  function testRemove()
  {
    $obj1 = new DelegatesChainTestingStubObject(null);
    $obj2 = new DelegatesChainTestingStubObject(null);
    
    $chain = new lmbDelegatesChain();    
    $chain->add(new lmbDelegate($obj1, 'invokable'));
    $chain->add(new lmbDelegate($obj2, 'invokable'));
    
    $chain->invoke();
    $chain->remove(new lmbDelegate($obj2, 'invokable'));
    $chain->invoke();
    $this->assertEqual($obj1->invoked, 2);
    $this->assertEqual($obj2->invoked, 1);
  }
  
  function testPassingInvokeArgs()
  {
    $obj = new DelegatesChainTestingStubObject();
    
    $chain = new lmbDelegatesChain();    
    $chain->add(new lmbDelegate($obj, 'invokable'));
    
    $chain->invoke('arg1', 'arg2');
    $this->assertEqual($obj->last_arg, 'arg1');
    $this->assertEqual($obj->last_arg2, 'arg2');
  }
  
}
?>