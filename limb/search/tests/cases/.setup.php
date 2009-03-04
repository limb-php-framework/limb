<?php
require_once('limb/search/common.inc.php');
require_once('limb/i18n/utf8.inc.php');
require_once('limb/dbal/src/lmbDbDump.class.php');
$this->dump = new lmbDbDump(dirname(__FILE__) . '/../../init/init_tests.sql');
$this->dump->load();

