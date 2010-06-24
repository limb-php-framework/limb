<?php
set_include_path(dirname(__FILE__) . '/../../../../');

define('LIMB_VAR_DIR', dirname(__FILE__) . '/../../../var/');

$mark = microtime(true);

require_once('limb/core/common.inc.php');
require_once('limb/web_app/common.inc.php');
require_once('limb/web_app/src/lmbWebApplication.class.php');

class DefaultController extends lmbController
{
  function doDisplay()
  {
    return "Hello, world!";
  }
}

$includes_time = microtime(true) - $mark;

$application = new lmbWebApplication();
$application->setDefaultControllerName('default');

$mark = microtime(true);

$application->process();

$exec_time = microtime(true) - $mark;

echo "<pre>\n==============\n";
echo "Includes time: $includes_time\n";
echo "Execution time: $exec_time\n";
echo "Total time: " . ($includes_time + $exec_time) . "\n";
echo "<pre>";

