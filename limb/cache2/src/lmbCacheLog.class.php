<?php
class lmbCacheLog
{
  protected static $instance = null;

  const PREFIX_MISS = 'miss';
  const PREFIX_HIT = 'hit';

  protected $log_file_pointer;

  static function instance($log_file = false)
  {
    if (!isset(self::$instance)){
      $class = __CLASS__;
      self::$instance = new $class($log_file);
    }
    return self::$instance;
  }

  function __construct($log_file)
  {
    $this->log_file_pointer = fopen($log_file, 'a+');
  }

  function logMiss($cache_name)
  {
    fwrite($this->log_file_pointer, self::PREFIX_MISS.' '.$cache_name."\n");
  }

  function logHit($cache_name)
  {
    fwrite($this->log_file_pointer, self::PREFIX_HIT.' '.$cache_name."\n");
  }

}
