<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cache2/src/lmbNonTransparentCache.interface.php');
lmb_require('limb/cache2/src/logs/lmbCacheLogFile.class.php');
lmb_require('limb/cache2/src/logs/lmbCacheLogMemory.class.php');
lmb_require('limb/core/src/lmbBacktrace.class.php');

class lmbCacheLoggedConnection extends lmbCacheAbstractConnection
{
  protected $cache_connection;
  protected $cache_name;
  protected $db_connection;
  protected $fake_ttl;
  protected $default_ttl;
  protected $logger;

  function __construct($cache_connection, $cache_name, $file = false)
  {
    $this->cache_connection = $cache_connection;
    $this->cache_name = $cache_name;

    if($file)
      $logger = lmbCacheLogFile::instance($file);
    else
      $logger = lmbCacheLogMemory::instance();

    $this->setLogger($logger);
  }

  function setLogger(lmbCacheLog $logger)
  {
    $this->logger = $logger;
  }

  function add($key, $value, $ttl = false)
  {
    $time = microtime(true);
    $result = $this->cache_connection->add($key, $value, $ttl);
    $this->logger->addRecord($key, lmbCache::OPERATION_ADD, microtime(true) - $time, $result);
    return $result;
  }

  function set($key, $value, $ttl = false)
  {
    $time = microtime(true);
    $value = $this->cache_connection->set($key, $value, $ttl);
    $this->logger->addRecord($key, lmbCache::OPERATION_SET, microtime(true) - $time, $value);
    return $value;
  }

  function get($key)
  {
    $time = microtime(true);
    $value = $this->cache_connection->get($key);
    $this->logger->addRecord($key, lmbCache::OPERATION_GET, microtime(true) - $time, !is_null($value));
    return $value;
  }

  function delete($key)
  {
    $time = microtime(true);
    $value = $this->cache_connection->delete($key);
    $this->logger->addRecord($key, lmbCache::OPERATION_DELETE, microtime(true) - $time, (bool) $value);
    return $value;
  }

  function flush()
  {
    return $this->cache_connection->flush();
  }

  function getLogRecords()
  {
    return $this->logger->getRecords();
  }

  function getStats()
  {
    return $this->logger->getStatistic();
  }

  function getRuntimeStats()
  {
    $queries = array();
    $operation_names = array(
      lmbCache::OPERATION_ADD => 'ADD',
      lmbCache::OPERATION_GET => 'GET',
      lmbCache::OPERATION_SET => 'SET',
      lmbCache::OPERATION_DELETE => 'DELETE',
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

  function getType()
  {
    return 'logged';
  }
}
