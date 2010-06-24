<?php
lmb_require('limb/cache2/src/logs/lmbCacheLog.interface.php');

class lmbCacheLogMemory implements lmbCacheLog
{
  protected static $instance = null;

  protected $records;

  static function instance()
  {
    if (!isset(self::$instance)){
      $class = __CLASS__;
      self::$instance = new $class();
    }
    return self::$instance;
  }

  function addRecord($key, $operation, $time, $result) {
    $this->records[] = array(
      'key' => $key,
      'operation' => $operation,
      'time' => $time,
      'result' => $result
    );

  }

  function getStatistic() {}
  function getRecords()
  {
    return $this->records;
  }


}
