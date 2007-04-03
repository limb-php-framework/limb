<?php
require_once('limb/dbal/src/lmbTestDbDump.class.php');

$type = lmbToolkit :: instance()->getDefaultDbConnection()->getType();
$this->dump = new lmbTestDbDump(dirname(__FILE__) . '/../../init/init_tests.' . $type);
?>
