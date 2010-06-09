<?php

$project_dir = realpath(getcwd());

define("LIMB_VAR_DIR", $project_dir . '/var/constructor');

$setup = $project_dir . '/setup.php';
if(!file_exists($setup))
{
  set_include_path($project_dir . PATH_SEPARATOR .
                   $project_dir . '/lib/' . PATH_SEPARATOR);
  @define('LIMB_VAR_DIR', dirname(__FILE__) . '/var/');
  require_once('limb/cms/common.inc.php');
}else{
  require_once($setup);
}

lmb_require('limb/taskman/taskman.inc.php');

taskman_propset('PROJECT_DIR', $project_dir);
lmb_require(dirname(__FILE__).'/project.inc.php');
taskman_run();