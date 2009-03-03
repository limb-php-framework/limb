<?php

lmb_env_setor('LIMB_CONF_INCLUDE_PATH', 'tests/settings;settings');
lmb_env_setor('LIMB_CACHE_DB_META_IN_FILE', false);
lmb_env_setor('LIMB_VAR_DIR', dirname(__FILE__) . '/../var/');

require_once(dirname(__FILE__) . '/../../setup.php');


