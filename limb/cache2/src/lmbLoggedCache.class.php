<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cache2/src/lmbNonTransparentCache.interface.php');
lmb_require('limb/cache2/src/lmbCacheLog.class.php');
lmb_require('limb/core/src/lmbBacktrace.class.php');
lmb_require('limb/dbal/src/query/lmbInsertQuery.class.php');

class lmbLoggedCache implements lmbNonTransparentCache
{
  protected $cache_connection;
  protected $cache_name;
  protected $db_connection;
  protected $fake_ttl;
  protected $default_ttl;
  protected $messages = array();
  protected $logger;

  public $OPERATION_ADD = 1;
  public $OPERATION_SET = 3;
  public $OPERATION_GET = 5;
  public $OPERATION_DELETE = 7;

  function __construct($cache_connection, $cache_name)
  {
    $this->cache_connection = $cache_connection;
    $this->cache_name = $cache_name;
    /*$conf = lmbToolkit::instance()->getConf('cache');
    if($conf->get('cache_log_enabled', false))
      $this->logger = lmbCacheLog::instance(
        $conf->get('cache_log_file')
      );*/
  }

  protected function _addMessage($key, $operation, $time, $result = true)
  {
  	$trace = new lmbBacktrace($trace_length = 8, $offset = 4);

    $this->messages[] = array(
      'key' => $key,
      'operation' => $operation,
      'result' => $result,
      'trace' => $trace->toString(),
      'time' => $time
    );

    if($this->logger && $this->OPERATION_GET === $operation)
    {
      if ($result)
        $this->logger->logHit($this->cache_name);
      else
        $this->logger->logMiss($this->cache_name);
    }
  }

  protected function _addLogRecord($key, $operation, $time, $result = true)
  {
    $result = ($result) ? '1' : '0';
    $query = new lmbInsertQuery(self::$log_table, $this->db_connection);
    $query->addField('name', $this->cache_name);
    $query->addField('key', $key);
    $query->addField('operation', $operation);
    $query->addField('time', $time);
    $query->addField('result', $result);

    $query->getStatement()->execute();
  }

  function add($key, $value, $ttl = false)
  {
    $time = microtime(true);
    $result = $this->cache_connection->add($key, $value, $ttl);
    $this->_addMessage($key, $this->OPERATION_ADD, microtime(true) - $time, $result);
    return $result;
  }

  function set($key, $value, $ttl = false)
  {
    $time = microtime(true);
    $value = $this->cache_connection->set($key, $value, $ttl);
    $this->_addMessage($key, $this->OPERATION_SET, microtime(true) - $time, $value);
    return $value;
  }

  function get($key)
  {
    $time = microtime(true);
    $value = $this->cache_connection->get($key);
    $this->_addMessage($key, $this->OPERATION_GET, microtime(true) - $time, !is_null($value));
    return $value;
  }

  function delete($key)
  {
    $time = microtime(true);
    $value = $this->cache_connection->delete($key);
    $this->_addMessage($key, $this->OPERATION_DELETE, microtime(true) - $time, (bool) $value);
    return $value;
  }

  function flush()
  {
    return $this->cache_connection->flush();
  }

  function getLogRecords()
  {
    //$result = lmbDBAL :: selectQuery(self::$log_table, $this->db_connection)->getRecordSet()->getFlatArray();
    //return $result;
  }

  function getStats()
  {
    $records = lmbDBAL :: selectQuery(self::$log_table, $this->db_connection)->where("name = '".$this->cache_name."'")->getRecordSet()->getFlatArray();

    $stat = array(
      'add_count' => 0,
      'get_count' => 0,
      'set_count' => 0,
      'delete_count' => 0,
      'expected_items_count' => 0,
      'misses' => 0,
      'hits' => 0,
      'repeated_add' => 0
    );

    foreach($records as $record)
    {
      switch ($record['operation']) {
      	case $this->OPERATION_ADD:
          $stat['add_count']++;
          if($record['result'])
            $stat['expected_items_count']++;
          else
            $stat['repeated_add']++;
      		break;
     	  case $this->OPERATION_GET:
     	    if($record['result'])
     	      $stat['hits']++;
     	    else
     	      $stat['misses']++;
          $stat['get_count']++;
      		break;
      	case $this->OPERATION_SET:
          $stat['set_count']++;
      		break;
      	case $this->OPERATION_DELETE:
          $stat['delete_count']++;
          if($record['result'])
            $stat['expected_items_count']--;
      		break;
      }
    }

    return $stat;
  }

  function getRuntimeStats()
  {
    $queries = array();
    $operation_names = array(
      $this->OPERATION_ADD => 'ADD',
      $this->OPERATION_GET => 'GET',
      $this->OPERATION_SET => 'SET',
      $this->OPERATION_DELETE => 'DELETE',
    );

    foreach ($this->messages as $message)
    {
      $queries[] = array(
        'command' => $operation_names[$message['operation']],
        'key' => $message['key'],
        'query' => $operation_names[$message['operation']].' - '.$message['key'],
        'trace' => $message['trace'],
        'time'  => $message['time'],
        'result' => ($message['result'])  ? 'SUCCESS' : 'ERROR'
      );
    }
    return $queries;
  }

  function getName()
  {
    return $this->cache_name;
  }

  function __call($method, $args)
  {
    if(!is_callable(array($this->cache_connection, $method)))
      throw new lmbException('Decorated cache driver does not support method "' . $method . '"');

    return call_user_func_array(array($this->cache_connection, $method), $args);
  }
}
