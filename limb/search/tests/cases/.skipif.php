<?php
require_once('limb/dbal/common.inc.php');
return lmbToolkit :: instance()->getDefaultDbConnection()->getType() != 'mysql';

