<?php
require_once(dirname(__FILE__) . '/../../common.inc.php');
require_once('limb/core/tests/cases/init.inc.php');

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var/macro/');

lmb_require('limb/fs/src/lmbFs.class.php');
lmb_require('limb/core/src/lmbObject.class.php');
lmb_require('limb/macro/src/lmbMacroTemplate.class.php');
lmb_require('limb/macro/src/*.class.php');
lmb_require('limb/macro/src/compiler/*.interface.php');
lmb_require('limb/macro/src/compiler/*.class.php');

require_once(dirname(__FILE__) . '/lmbBaseMacroTest.class.php');


