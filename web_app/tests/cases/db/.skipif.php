<?php

require_once('limb/dbal/tests/cases/init.inc.php');

return lmb_tests_db_dump_does_not_exist(dirname(__FILE__) . '/.fixture/init_tests.', 'WEB_APP');
