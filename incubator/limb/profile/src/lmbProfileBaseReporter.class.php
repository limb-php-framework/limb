<?php

abstract class lmbProfileBaseReporter
{
  protected $sql_queries = array();
  protected $cache_queries = array();

  protected $script_time = 0;
  protected $script_memory = 0;
  protected $script_peak_memory = 0;


  function _getPHPVariables()
  {
    $result = array();
    foreach($_SERVER as $key => $value)
      $result['$_SERVER["'. $key .'"]'] = $value;
    foreach($_REQUEST as $key => $value)
      $result['$_REQUEST["'. $key .'"]'] = $value;
    foreach($_ENV as $key => $value)
      $result['$_ENV["'. $key .'"]'] = $value;
    return $result;
  }

  /**
   * @param array $query_info array with keys 'query', 'trace' and 'time'
   */
  function addSqlQuery($query_info)
  {
    $this->sql_queries[] = $query_info;
  }

  /**
   * @param array $query_info array with keys 'query', 'trace' and 'time'
   */
  function addCacheQuery($query_info)
  {
    $this->cache_queries[] = $query_info;
  }

  function setScriptStatistic($time, $memory, $peak_memory)
  {
    $this->script_time = $time;
    $this->script_memory = $memory;
    $this->script_peak_memory = $peak_memory;
  }

  abstract function getReport();
}