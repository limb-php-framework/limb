<?php
set_include_path(dirname(__FILE__) . '/' . PATH_SEPARATOR .
                 '%LIMB_PARENT_DIR%' . PATH_SEPARATOR);

lmb_env_setor('LIMB_CACHE_DB_META_IN_FILE', false);
lmb_env_setor('LIMB_APP_MODE', 'devel');

?>