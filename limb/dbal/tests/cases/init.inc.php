<?php

require_once('limb/core/tests/cases/init.inc.php');

function lmb_tests_init_db_dsn()
{
  if(lmbToolkit::instance()->isDefaultDbDSNAvailable())
    return;
    
  $default_value = 'sqlite://localhost/' . lmb_env_get('LIMB_VAR_DIR') . '/sqlite_tests.db';  
  $dsn = lmb_env_get('LIMB_TEST_DB_DSN', $default_value);
  
  echo "Using default test database '$dsn'\n";
  lmbToolkit::instance()->setDefaultDbDSN($dsn);  
}

function lmb_tests_is_db_dump_exists($prefix, $package)
{
	$type = lmbToolkit :: instance()->getDefaultDbConnection()->getType();
  $skip = !file_exists($prefix.$type);

  if($skip)
    echo PHP_EOL.$package." package tests are skipped!(no compatible database fixture found for '$type' connection)".PHP_EOL.PHP_EOL;

  return $skip;
}