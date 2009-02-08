<?php

lmb_require('limb/core/src/lmbEnv.class.php');

class lmbEnvFunctionsTest extends UnitTestCase
{
  function testGet_Negative()
  {
    $this->assertNull(lmb_env_get('foo0'));
  }

  function testAdd()
  {
    lmb_env_add('foo1', 'bar');
    $this->assertEqual(lmb_env_get('foo1'), 'bar');
    lmb_env_add('foo1', 'baz');
    $this->assertEqual(lmb_env_get('foo1'), 'bar');
  }
  
  function testHas()
  {
    $this->assertFalse(lmb_env_has('foo2'));
    lmb_env_add('foo2', 'bar');
    $this->assertTrue(lmb_env_has('foo2'));    
  }

  function testReplace()
  {
    lmb_env_add('foo3', 'bar');
    lmb_env_set('foo3', 'baz');
    $this->assertEqual(lmb_env_get('foo3'), 'baz');
  }
  
  function testTrace()
  { 
    lmb_env_trace('foo4');
    
    ob_start();
    lmb_env_add($key = 'foo4', $value = 'bar');
    $trace_info = ob_get_clean();
    
    $this->assertTrue(strstr($trace_info, __FILE__));
    $this->assertTrue(strstr($trace_info, $call_line = '39')); 
    $this->assertTrue(strstr($trace_info, $method_name = 'add')); 
    $this->assertTrue(strstr($trace_info, $key));
    $this->assertTrue(strstr($trace_info, $value));     
        
    ob_start();
    lmb_env_set($key, $value = 'baz');
    $trace_info = ob_get_clean();
    
    $this->assertTrue(strstr($trace_info, __FILE__));
    $this->assertTrue(strstr($trace_info, $call_line = '49'));
    $this->assertTrue(strstr($trace_info, $method_name = 'set')); 
    $this->assertTrue(strstr($trace_info, $key));
    $this->assertTrue(strstr($trace_info, $value));
  }
  
  function testBackCompability()
  {
    $name = 'LIMB_FOO';
    
    $this->assertFalse(lmb_env_has($name));
    
    define($name, 'bar');
    $this->assertTrue(lmb_env_has($name));
    $this->assertEqual(lmb_env_get($name), 'bar');        
    $this->assertFalse(lmb_env_add($name, 'yargh'));
    
    lmb_env_set($name, 'baz');
    $this->assertEqual(lmb_env_get($name), 'baz');
  }
}