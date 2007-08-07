<?php
require_once(dirname(__FILE__) . '/../../common.inc.php');
require_once(dirname(__FILE__) . '/lmbCmsTestCase.class.php');
require_once('limb/dbal/src/lmbDbDump.class.php');
$this->dump = new lmbDbDump(dirname(__FILE__) . '/.fixtures/init_tests.mysql');
$this->dump->load();

