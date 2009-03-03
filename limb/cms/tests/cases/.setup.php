<?php
require_once(dirname(__FILE__) . '/../../common.inc.php');
require_once('limb/dbal/src/lmbDbDump.class.php');

$type = lmbToolkit :: instance()->getDefaultDbConnection()->getType();
$this->dump = new lmbDbDump(dirname(__FILE__) . '/../../init/db.' . $type);
$this->dump->load();

$type = lmbToolkit :: instance()->getDefaultDbConnection()->getType();
$this->dump = new lmbDbDump(dirname(__FILE__) . '/fixture/init_tests.' . $type);
$this->dump->load();

lmb_require('limb/cms/tests/cases/lmbCmsTestCase.class.php');