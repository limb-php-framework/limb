#!/usr/bin/env php
<?php
set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());

//function error_handler($errno, $errstr, $errfile, $errline, $errcontext)
//{
//  if($errno != E_STRICT)
//    throw new Exception("Error happened: $errstr($errno) in $errfile@$errline($errcontext)");
//}
//set_error_handler('error_handler');

require_once(dirname(__FILE__) . '/tests_runner/common.inc.php');
require_once(dirname(__FILE__) . '/tests_runner/src/lmbTestRunner.class.php');
require_once(dirname(__FILE__) . '/tests_runner/src/lmbTestTreeFilePathNode.class.php');
require_once(dirname(__FILE__) . '/tests_runner/src/lmbTestTreeGlobNode.class.php');

$fork = true;
$quiet = false;
$tests = array();
$skipped = array();

function out($msg)
{
  global $quiet;

  if(!$quiet)
    echo $msg;
}

function process_argv(&$argv, &$defines = array())
{
  global $quiet;
  global $fork;
  global $skipped;

  $new_argv = array();
  $selected_option = '';
  $next_is_def = false;
  foreach($argv as $arg)
  {
    // control arguments
    switch($arg)
    {
      case '-D':
        $next_is_def = true;
        break;
      case '-q':
        $quiet = true;
        break;
      case '--no-fork':
        $fork = false;
        break;
      case '--include-path':
      case '--skip':
        $selected_option = $arg;
        break;
      case '--':
        $selected_option = '';
        break;
      default:
        if($next_is_def)
        {
          list($dn,$dv) = explode('=', $arg);
          $defines[] = "-D \"$dn=$dv\"";
          echo "Defining $dn=$dv\n";
          define("$dn", $dv);
          $next_is_def = false;
          break;
        }
        // value arguments
        switch($selected_option)
        {
          case '--skip':
            $skipped[] = $arg;
            break;
          case '--include-path':
            set_include_path(dirname(__FILE__) . '/' . $arg . PATH_SEPARATOR . get_include_path());
            $selected_option = '';
            break;
          default:
            $new_argv[] = $arg;
            break;
        }
    }
  }
  $argv = $new_argv;
}

function get_php_bin()
{
  ob_start();
  phpinfo(INFO_GENERAL);
  $info = ob_get_contents();
  ob_end_clean();

  if(isset($_ENV["_"]) && basename($_ENV["_"]) != basename(__FILE__))
    $php_bin = $_ENV["_"];
  else
    $php_bin = "php";//any better way to guess it otherwise?

  $php_ini = "";

  $lines = explode("\n", $info);
  foreach($lines as $line)
  {
    if(preg_match('~^Loaded Configuration File\s*=>\s*(.*)$~', $line, $m))
    {
      if(file_exists($m[1]))
        $php_ini = "-c " . $m[1];
    }
  }
  return $php_bin . " " . $php_ini;
}

$defines = array();

process_argv($argv, $defines);

if(sizeof($argv) > 1)
  $tests = array_splice($argv, 1);

if(!$tests)
  $tests = glob("*", GLOB_ONLYDIR);

$tests = array_diff($tests, $skipped);

if($fork)
{
  $php_bin = get_php_bin();
  out("=========== Forking processes for each test path(PHP cmdline '$php_bin') ===========\n");
}

$res = true;
foreach($tests as $test)
{
  if(file_exists($test) || is_dir($test))
  {
    if($fork)
    {
      system($php_bin . " " . __FILE__ . " -q --no-fork " . implode(" ", $defines) . " $test", $ret);
      if($ret != 0)
        $res = false;
    }
    else
    {
      $runner = new lmbTestRunner();
      if(!$runner->run(new lmbTestTreeFilePathNode($test)))
        $res = false;
    }
  }
  else
    out("=========== Test path '$test' is not valid, skipping ==========\n");
}

if(!$res)
  out("=========== TESTS HAD ERRORS(see above) ===========\n");
else
  out("=========== ALL TESTS PASSED ===========\n");

exit($res ? 0 : 1);
