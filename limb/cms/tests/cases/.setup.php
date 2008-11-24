<?php
if(!defined('LIMB_VAR_DIR'))
{
  @define('LIMB_VAR_DIR', dirname(__FILE__) . '/../../../var');
  if(!is_dir(LIMB_VAR_DIR) && !mkdir(LIMB_VAR_DIR))
    throw new Exception("Could not create LIMB_VAR_DIR at '" . LIMB_VAR_DIR . "' during tests execution");
}

require_once(dirname(__FILE__) . '/../../common.inc.php');
require_once(dirname(__FILE__) . '/lmbCmsTestCase.class.php');

if(lmbToolkit::instance()->isDefaultDbDSNAvailable())
{
  require_once('limb/dbal/src/lmbDbDump.class.php');
  $db_connection_type = lmbToolkit::instance()->getDefaultDbConnection()->getType();  
  $fixture_file = dirname(__FILE__) . '/.fixtures/init_tests.' . $db_connection_type;
  if(!file_exists($fixture_file))
    throw new lmbException('fixture for '.$db_connection_type.' not available');
  $this->dump = new lmbDbDump($fixture_file);
  $this->dump->load();
}
