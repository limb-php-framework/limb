<?php
if(!defined('LIMB_VAR_DIR'))
{
  @define('LIMB_VAR_DIR', dirname(__FILE__) . '/../../../var');
  if(!is_dir(LIMB_VAR_DIR) && !mkdir(LIMB_VAR_DIR))
    throw new Exception("Could not create LIMB_VAR_DIR at '" . LIMB_VAR_DIR . "' during tests execution");
}

require_once(dirname(__FILE__) . '/../../common.inc.php');
require_once('limb/dbal/src/lmbDbDump.class.php');

if(!lmbToolkit::instance()->isDefaultDbDsnAvailable())
{
  $dsn = 'sqlite://localhost/' . LIMB_VAR_DIR . '/sqlite_tests.db';
  echo "Using default sqlite test database '$dsn'\n";
  lmbToolkit::instance()->setDefaultDbDSN($dsn);
}

$type = lmbToolkit :: instance()->getDefaultDbConnection()->getType();
$file = dirname(__FILE__) . '/../../init/init_tests.' . $type;
if(file_exists($file))
{
  $this->dump = new lmbDbDump($file);
  $this->dump->load();
}
