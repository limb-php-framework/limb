<?php
require_once('limb/dbal/src/lmbDbDump.class.php');
$this->dump = new lmbDbDump(dirname(__FILE__) . '/.fixtures/init_tests.mysql');
$this->dump->load();
?>
