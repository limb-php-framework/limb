<?php
set_include_path(dirname(__FILE__) . '/../../../../');

define('LIMB_VAR_DIR', dirname(__FILE__) . '/../../../var/');

$mark = microtime(true);

require_once('limb/core/common.inc.php');
require_once('limb/web_app/common.inc.php');
require_once('limb/web_app/src/controller/lmbController.class.php');
require_once('limb/filter_chain/src/lmbFilterChain.class.php');
require_once('limb/core/src/lmbHandle.class.php');

class DefaultController extends lmbController
{
  function doDisplay()
  {
    return "Hello, world!";
  }
}

$includes_time = microtime(true) - $mark;

$mark = microtime(true);

$application = new lmbFilterChain();

$application->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbUncaughtExceptionHandlingFilter'));
$application->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbSessionStartupFilter'));
$application->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbRequestDispatchingFilter',
                                    array(new lmbHandle('limb/web_app/src/request/lmbRoutesRequestDispatcher'), 'default')));
$application->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbResponseTransactionFilter'));
$application->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbActionPerformingFilter'));
$application->registerFilter(new lmbHandle('limb/web_app/src/filter/lmbViewRenderingFilter'));

$config_time = microtime(true) - $mark;

$mark = microtime(true);

$application->process();

$exec_time = microtime(true) - $mark;

echo "<pre>\n==============\n";
echo "Includes time: $includes_time\n";
echo "Configuration time: $config_time\n";
echo "Execution time: $exec_time\n";
echo "Total time: " . ($includes_time + $config_time + $exec_time) . "\n";
echo "<pre>";

