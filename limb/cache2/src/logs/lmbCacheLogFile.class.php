<?php
class lmbCacheLogFile implements lmbCacheLog
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

  function addRecord($key, $operation, $time, $result){}
  function getStatistic(){}
  function getRecords(){}
}
