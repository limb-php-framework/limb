<?php
require_once(dirname(__FILE__) . '/../../common.inc.php');
lmb_require('limb/dbal/tests/cases/init.inc.php');

lmb_tests_init_db_dsn();

return lmb_tests_db_dump_does_not_exist(dirname(__FILE__).'/../../init/db.', 'CRON');