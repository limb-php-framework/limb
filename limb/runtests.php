<?php
set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());

require_once(dirname(__FILE__) . '/tests_runner/common.inc.php');
require_once(dirname(__FILE__) . '/tests_runner/src/lmbTestRunner.class.php');
require_once(dirname(__FILE__) . '/tests_runner/src/lmbTestTreeFilePathNode.class.php');
require_once(dirname(__FILE__) . '/tests_runner/src/lmbTestTreeGlobNode.class.php');

$packages = null;
if(sizeof($argv) > 1)
  $packages = array_splice($argv, 1);


if($packages)
{
  $paths = array();
  $res = true;
  foreach($packages as $pkg)
  {
    $runner = new lmbTestRunner();
    $path = dirname(__FILE__) . '/' . $pkg . '/tests/cases/';
    if(is_dir($path))
      $res &= $runner->run(new lmbTestTreeFilePathNode($path));
  }
}
else
{
  $runner = new lmbTestRunner();
  $res = $runner->run(new lmbTestTreeGlobNode(dirname(__FILE__) . '/*/tests/cases/'));
}

exit($res ? 0 : 1);
