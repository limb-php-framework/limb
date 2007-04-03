<?php
lmb_require('limb/dbal/src/lmbTestDbDump.class.php');
$this->dump = new lmbTestDbDump(dirname(__FILE__) . '/../../init/init_tests.sql');
?>
