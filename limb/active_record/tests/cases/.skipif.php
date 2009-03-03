<?php

require_once(dirname(__FILE__) . '/../../common.inc.php');

lmb_env_setor('LIMB_VAR_DIR', dirname(__FILE__) . '/../../../var');

if(!is_dir(lmb_env_get('LIMB_VAR_DIR')) && !mkdir(lmb_env_get('LIMB_VAR_DIR')))
  throw new Exception("Could not create LIMB_VAR_DIR at '" . lmb_env_get('LIMB_VAR_DIR') . "' during tests execution");

lmb_env_setor('LIMB_TEST_DB_DSN', 'sqlite://localhost/' . lmb_env_get('LIMB_VAR_DIR') . '/sqlite_tests.db');

if(!lmbToolkit::instance()->isDefaultDbDSNAvailable())
{
  $dsn = lmb_env_get('LIMB_TEST_DB_DSN');
  echo "Using default sqlite test database '$dsn'\n";
  lmbToolkit::instance()->setDefaultDbDSN($dsn);
}

$type = lmbToolkit :: instance()->getDefaultDbConnection()->getType();
$skip = !file_exists(dirname(__FILE__) . '/.fixture/init_tests.' . $type);
$test_dir = basename(dirname(__FILE__));

if($skip)
  echo "\nACTIVE_RECORD package '$test_dir' tests are skipped!(no compatible database fixture found for '$type' connection)\n\n";

return $skip;

