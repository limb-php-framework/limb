<?php
lmb_require('limb/cache2/src/logs/lmbCacheLog.interface.php');

class lmbCacheLogFile implements lmbCacheLog
{
  protected static $instance = null;

  protected $log_file;
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
    $this->log_file = $log_file;
    $this->log_file_pointer = fopen($this->log_file, 'a+');
  }
  
  function __destruct()
  {
    fclose($this->log_file_pointer);
  }

  function addRecord($key, $operation, $time, $result)
  {
    $params = array($key, $operation, $time, (int) $result);
    fwrite($this->log_file_pointer, implode(' ', $params).PHP_EOL);
  }

  function getRecords(){
    $result = array();
    foreach(file($this->log_file) as $record_str)
    {
      list($record['key'], $record['operation'], $record['time'], $record['result']) = explode(' ', trim($record_str));
      $record['result'] = (bool) $record['result'];
      $record['time'] = (integer) $record['time'];
      $result[] = array_reverse($record);
    }
    return $result;
  }

  function getStatistic(){}

}
