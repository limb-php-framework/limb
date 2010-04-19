<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cache2/src/lmbCacheBaseWrapper.class.php');
lmb_require('limb/cache2/src/lmbCacheLog.class.php');
lmb_require('limb/core/src/lmbBacktrace.class.php');
lmb_require('limb/dbal/src/query/lmbInsertQuery.class.php');

class lmbLoggedCache extends lmbCacheBaseWrapper
{
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

  function __construct($cache_connection, $cache_name = 'default_cache')
  {
    parent::__construct($cache_connection);
    $this->cache_name = $cache_name;
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

  function add($key, $value, $ttl = false)
  {
    $time = microtime(true);
    $args = func_get_args();
    $result = call_user_func_array(array($this->wrapped_cache, __FUNCTION__), $args);
    $this->_addMessage($key, $this->OPERATION_ADD, microtime(true) - $time, $result);
    return $result;
  }

  function set($key, $value, $ttl = false)
  {
    $time = microtime(true);
    $args = func_get_args();
    $value = call_user_func_array(array($this->wrapped_cache, __FUNCTION__), $args);
    $this->_addMessage($key, $this->OPERATION_SET, microtime(true) - $time, $value);
    return $value;
  }

  function get($key)
  {
    $time = microtime(true);
    $value = $this->wrapped_cache->get($key);
    $this->_addMessage($key, $this->OPERATION_GET, microtime(true) - $time, !is_null($value));
    return $value;
  }

  function delete($key)
  {
    $time = microtime(true);
    $value = $this->wrapped_cache->delete($key);
    $this->_addMessage($key, $this->OPERATION_DELETE, microtime(true) - $time, (bool) $value);
    return $value;
  }

  function flush()
  {
    return $this->wrapped_cache->flush();
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
    if(!is_callable(array($this->wrapped_cache, $method)))
      throw new lmbException('Decorated cache driver does not support method "' . $method . '"');

    return call_user_func_array(array($this->cache_connection, $method), $args);
  }
}
