<?php
set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());

require_once(dirname(__FILE__) . '/tests_runner/common.inc.php');
require_once(dirname(__FILE__) . '/tests_runner/src/lmbTestRunner.class.php');
require_once(dirname(__FILE__) . '/tests_runner/src/lmbTestTreeFilePathNode.class.php');
require_once(dirname(__FILE__) . '/tests_runner/src/lmbTestTreeGlobNode.class.php');

$tests = array();
if(sizeof($argv) > 1)
  $tests = array_splice($argv, 1);


if($tests)
{
  $paths = array();
  $res = true;
  foreach($tests as $test)
  {
    $runner = new lmbTestRunner();
    if(file_exists($test) || is_dir($test))
      $res &= $runner->run(new lmbTestTreeFilePathNode($test));
    else
      echo "Test path '$test' is not valid, skipping\n";
  }
}
else
{
  $runner = new lmbTestRunner();
  $res = $runner->run(new lmbTestTreeGlobNode(dirname(__FILE__) . '/*/tests/cases/'));
}

exit($res ? 0 : 1);
