<?php

class lmbEnv
{
  protected static $_values = array();

  static function get($key)
  {
    if(isset(self::$_values[$key]))
      return self::$_values[$key];
    else
      return '';
  }

  static function add($key, $value)
  {
    if(!isset(self::$_values[$key]))
      self::$_values[$key] = $value;
  }

  static function set($key, $value)
  {
    self::$_values[$key] = $value;
  }
}