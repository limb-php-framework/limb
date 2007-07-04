<?php
require_once(dirname(__FILE__) . '/../../common.inc.php');

$type = lmbToolkit :: instance()->getDefaultDbConnection()->getType();
$ignore = !file_exists(dirname(__FILE__) . '/../../init/init_tests.' . $type);
$test_dir = basename(dirname(__FILE__));

if($ignore)
  echo "\nTREE package '$test_dir' tests are skipped!(no compatible database fixture found for '$type' connection)\n\n";

return $ignore;
?>
