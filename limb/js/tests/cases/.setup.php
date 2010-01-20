<?php

require_once(dirname(__FILE__) . '/../../common.inc.php');
require_once('limb/core/tests/cases/init.inc.php');

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var');

lmb_env_setor('LIMB_JS_INCLUDE_PATH', rtrim(lmb_var_dir(), '/\\'));
