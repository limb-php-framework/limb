<?php
set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());

require_once(dirname(__FILE__) . '/tests_runner/common.inc.php');
require_once(dirname(__FILE__) . '/tests_runner/src/lmbTestRunner.class.php');
require_once(dirname(__FILE__) . '/tests_runner/src/lmbTestTreeFilePathNode.class.php');
require_once(dirname(__FILE__) . '/tests_runner/src/lmbTestTreeGlobNode.class.php');

$fork = false;
$quiet = false;
$tests = array();
$failed_tests = array();

function out($msg)
{
  global $quiet;

  if(!$quiet)
    echo $msg;
}

function process_argv(&$argv)
{
  global $quiet;
  global $fork;

  $new_argv = array();
  foreach($argv as $arg)
  {
    if($arg == '-q')
      $quiet = true;
    else if($arg == '--fork')
      $fork = true;
    else
      $new_argv[] = $arg;
  }
  $argv = $new_argv;
}

process_argv($argv);

if(sizeof($argv) > 1)
  $tests = array_splice($argv, 1);

if(!$tests)
  $tests = glob("*/tests/cases");

if($fork)
  out("=========== Forking procees for each test ===========\n");

$res = true;
foreach($tests as $test)
{
  if(file_exists($test) || is_dir($test))
  {
    out("=========== Running tests from '$test' ===========\n");

    if($fork)
    {
      //TODO: get rid of hardocoded php call here
      system("php " . __FILE__ . " -q $test", $ret);
      if($ret != 0)
      {
        $res = false;
        $failed_tests[] = $test;
      }
    }
    else
    {
      $runner = new lmbTestRunner();
      if(!$runner->run(new lmbTestTreeFilePathNode($test)))
      {
        $res = false;
        $failed_tests[] = $test;
      }
    }
  }
  else
    out("=========== Test path '$test' is not valid, skipping ==========\n");
}

if(!$res)
{
  out("=========== TESTS HAD ERRORS ===========\n");
  out("Failed tests: [" . implode(", ", $failed_tests) . "]\n");
}
else
  out("=========== ALL TESTS PASSED ===========\n");

exit($res ? 0 : 1);
