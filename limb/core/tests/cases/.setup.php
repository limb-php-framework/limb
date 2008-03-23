<?php

if(!defined('LIMB_VAR_DIR'))
{
  @define('LIMB_VAR_DIR', dirname(__FILE__) . '/../../../var');
  @mkdir(LIMB_VAR_DIR);
}

require_once(dirname(__FILE__) . '/../../common.inc.php');

