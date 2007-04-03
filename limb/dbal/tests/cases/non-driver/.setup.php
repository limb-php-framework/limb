<?php
lmb_require('limb/dbal/src/lmbTestDbDump.class.php');
$type = lmbToolkit :: instance()->getDefaultDbConnection()->getType();
$this->dump = new lmbTestDbDump(dirname(__FILE__) . '/.fixture/init_tests.' . $type);

?>
