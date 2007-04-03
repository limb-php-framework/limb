<?php
require_once('limb/dbal/src/lmbTestDbDump.class.php');

$this->dump = new lmbTestDbDump(dirname(__FILE__) . '/.fixtures/init_tests.mysql');
?>
