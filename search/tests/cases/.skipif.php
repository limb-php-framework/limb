<?php
require_once('limb/dbal/common.inc.php');

if(!lmbToolkit::instance()->isDefaultDbDSNAvailable())
{
  echo "\nThere is no default database connection DSN available, SEARCH package tests skipped\n\n";
  return true;
}

if(strpos(lmbToolkit :: instance()->getDefaultDbConnection()->getType(), 'mysql') === false)
{
  echo "\nSEARCH package tests are skipped! Only MySQL database is supported\n\n";
  return true;
}

return false;

