<?php

class lmbEnv
{
  protected static $_values = array();
  protected static $_trace_enabled = false;
  protected static $_keys_for_trace = array();

  static function get($key)
  {
    if(isset(self::$_values[$key]))
      return self::$_values[$key];
    else
      return '';
  }
  
  static function _trace($key)
  {
    if(!array_key_exists($key, self::$_keys_for_trace))
      return;
    
    $trace = debug_backtrace();
    $trace = $trace[1];
        
    $file_str = 'Called '.$trace['file'].'::'.$trace['line'];
    $call_str = 'lmbEnv::'.$trace['function'].'('.$trace['args'][0].','.$trace['args'][1].')';
    echo $file_str.' '.$call_str.PHP_EOL;    
  }

  static function add($key, $value)
  {
    if(!isset(self::$_values[$key]))
      self::$_values[$key] = $value;
      
    if(self::$_trace_enabled)
      self::_trace($key);
  }  

  static function set($key, $value)
  {
    self::$_values[$key] = $value;
    
    if(self::$_trace_enabled)
      self::_trace($key);
  }
  
  static function trace($key)
  {
    self::$_trace_enabled = true;
    self::$_keys_for_trace[$key] = $key;
  }
}