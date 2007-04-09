<?php
if(!class_exists('lmbTestGroup'))
  return false;

$file = realpath(dirname(__FILE__) . '/../../src/lmbTestGroup.class.php');
if($res = !in_array($file, get_included_files()))
  echo "Running tests using another installation of TESTS_RUNNER is restricted";

return $res;
?>