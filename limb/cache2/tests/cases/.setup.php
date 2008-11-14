<?php
if(!defined('LIMB_VAR_DIR'))
{
  @define('LIMB_VAR_DIR', dirname(__FILE__) . '/../../var');
  if(!is_dir(LIMB_VAR_DIR) && !mkdir(LIMB_VAR_DIR))
    throw new Exception("Could not create LIMB_VAR_DIR at '" . LIMB_VAR_DIR . "' during tests execution");
}

require_once(dirname(__FILE__) . '/../../common.inc.php');
require_once(dirname(__FILE__) . '/../../../config/common.inc.php');
require_once(dirname(__FILE__) . '/../../../fs/src/lmbFs.class.php');