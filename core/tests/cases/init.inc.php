<?php
require_once('limb/fs/src/lmbFs.class.php');

function lmb_tests_init_var_dir($value)
{
  if(file_exists($value))
    lmbFs::rm($value);
  lmbFs :: mkdir($value);
  $real = realpath($value).'/';
  lmb_var_dir($real);
  echo "INFO: Var dir inited in {$real}". PHP_EOL;
}

function lmb_tests_cleanup_var_dir()
{
  $var_dir = lmb_var_dir();
  lmbFs::rm($var_dir);
  echo "INFO: Var dir cleaned up in {$var_dir}". PHP_EOL;
}