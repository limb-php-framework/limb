<?php

function lmb_tests_init_var_dir($value)
{
  if(!is_dir($value) && !mkdir($value))
    throw new Exception("Could not create LIMB_VAR_DIR at '" . $value . "' during tests execution");

  lmb_env_setor('LIMB_VAR_DIR', realpath($value));

  lmb_require('limb/fs/src/lmbFs.class.php');
  try { lmbFs :: mkdir(LIMB_VAR_DIR); } catch(lmbFsException $e) {}
}