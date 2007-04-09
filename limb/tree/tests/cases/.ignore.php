<?php
require_once(dirname(__FILE__) . '/../../common.inc.php');
return !file_exists(dirname(__FILE__) . '/../../init/init_tests.' .
                    lmbToolkit :: instance()->getDefaultDbConnection()->getType());
?>
