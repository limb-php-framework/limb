<?php
require_once(dirname(__FILE__) . '/../../common.inc.php');

lmb_require('limb/dbal/tests/cases/init.inc.php');
lmb_tests_init_db_dsn();
lmb_tests_setup_db(dirname(__FILE__) . '/.fixture/init_tests.');

lmb_require('limb/core/src/lmbSet.class.php');
lmb_require('limb/core/src/lmbCollection.class.php');
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/active_record/tests/cases/lmbARBaseTestCase.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLRawCriteria.class.php');