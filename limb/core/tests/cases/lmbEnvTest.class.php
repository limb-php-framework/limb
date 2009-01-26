<?php

lmb_require('limb/core/src/lmbEnv.class.php');

class lmbEnvTest extends UnitTestCase
{
  function testGet_Negative()
  {
    $this->assertEqual(lmbEnv::get('foo'), '');
  }

  function testSetGet()
  {
    lmbEnv::add('foo', 'bar');
    $this->assertEqual(lmbEnv::get('foo'), 'bar');
    lmbEnv::add('foo', 'baz');
    $this->assertEqual(lmbEnv::get('foo'), 'bar');
  }

  function testReplace()
  {
    lmbEnv::add('foo', 'bar');
    lmbEnv::set('foo', 'baz');
    $this->assertEqual(lmbEnv::get('foo'), 'baz');
  }
  
  function testTrace()
  {    
    lmbEnv::trace('foo');
    
    ob_start();
    lmbEnv::add($key = 'foo', $value = 'bar');
    $trace_info = ob_get_clean();
    
    $this->assertTrue(strstr($trace_info, __FILE__));
    $this->assertTrue(strstr($trace_info, $call_line = '32')); 
    $this->assertTrue(strstr($trace_info, $method_name = 'add')); 
    $this->assertTrue(strstr($trace_info, $key));
    $this->assertTrue(strstr($trace_info, $value));     
        
    ob_start();
    lmbEnv::set($key, $value = 'baz');    
    $trace_info = ob_get_clean();
    
    $this->assertTrue(strstr($trace_info, __FILE__));
    $this->assertTrue(strstr($trace_info, $call_line = '42'));
    $this->assertTrue(strstr($trace_info, $method_name = 'set')); 
    $this->assertTrue(strstr($trace_info, $key));
    $this->assertTrue(strstr($trace_info, $value));
  }
}