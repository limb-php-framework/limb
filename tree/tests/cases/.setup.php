<?php

require_once(dirname(__FILE__) . '/../../common.inc.php');
require_once('limb/core/tests/cases/init.inc.php');

lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var');

require_once('limb/dbal/src/lmbDbDump.class.php');

$type = lmbToolkit :: instance()->getDefaultDbConnection()->getType();
$file = dirname(__FILE__) . '/../../init/init_tests.' . $type;
if(file_exists($file))
{
  $this->dump = new lmbDbDump($file);
  $this->dump->load();
}
