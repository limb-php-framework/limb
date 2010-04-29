<?php
require_once(dirname(__FILE__) . '/../../common.inc.php');
lmb_require('limb/dbal/tests/cases/init.inc.php');

lmb_tests_init_db_dsn();

return lmb_tests_is_db_dump_exists(dirname(__FILE__).'/../../init/db.', 'CRON');