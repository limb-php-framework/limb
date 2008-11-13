<?php
error_reporting(E_ALL);
define('OUT_FORMAT', 'html');

$limb_dir = realpath(dirname(__FILE__).'/../../../../');

set_include_path(get_include_path().PATH_SEPARATOR.$limb_dir);

require_once('limb/cache2/common.inc.php');
require_once(dirname(__FILE__).'/data.inc.php');

function make_test($cacher, $operation, $value) {
  global $operations_with_data;
  $time = microtime(1);
  $iterations = ITERATIONS_COUNT;  
  if(in_array($operation, $operations_with_data)) {
    while($iterations--)
      $cacher->$operation($iterations, $value);
  } else {
    while($iterations--)
      $cacher->$operation($iterations);
  }
  return microtime(1) - $time;
}

function draw_text_report($result_array) {
  global $operations;
  
  echo 'data type | ';
  foreach($operations as $operation)
  {
    echo $operation.' | ';
  }
  echo PHP_EOL;      
  
  foreach($result_array as $data_type_name => $results_for_data_type) {
    echo $data_type_name;
    echo ' | ';    
    foreach($results_for_data_type as $results_for_data_type) {    
      echo round(ITERATIONS_COUNT / $results_for_data_type).' | '; 
    }    
    echo PHP_EOL;
  }
}

function bench_cacher($cacher) {
  global $data, $operations;  
  $cacher->flush();
  $result = array();
  foreach($data as $type => $value) {
    foreach($operations as $operation) {
      $result[$type][$operation] = make_test($cacher, $operation, $value);
    }
  }
  
  return $result;
}