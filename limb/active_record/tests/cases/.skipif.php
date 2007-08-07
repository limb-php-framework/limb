<?php
require_once(dirname(__FILE__) . '/../../common.inc.php');

$type = lmbToolkit :: instance()->getDefaultDbConnection()->getType();
$skip = !file_exists(dirname(__FILE__) . '/.fixture/init_tests.' . $type);
$test_dir = basename(dirname(__FILE__));

if($skip)
  echo "\nACTIVE_RECORD package '$test_dir' tests are skipped!(no compatible database fixture found for '$type' connection)\n\n";

return $skip;

