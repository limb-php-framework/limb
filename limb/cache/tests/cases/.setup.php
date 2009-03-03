<?php

require_once(dirname(__FILE__) . '/../../common.inc.php');

lmb_env_setor('LIMB_VAR_DIR', dirname(__FILE__) . '/../../var');

if(!is_dir(lmb_env_get('LIMB_VAR_DIR')) && !mkdir(lmb_env_get('LIMB_VAR_DIR')))
  throw new Exception("Could not create LIMB_VAR_DIR at '" . lmb_env_get('LIMB_VAR_DIR') . "' during tests execution");

lmb_require('limb/fs/src/lmbFs.class.php');
try { lmbFs :: mkdir(LIMB_VAR_DIR); } catch(lmbFsException $e) {}