<?php

require_once('limb/dbal/tests/cases/init.inc.php');

return lmb_tests_is_db_dump_exists(dirname(__FILE__) . '/.fixture/init_tests.', 'WEB_APP');
