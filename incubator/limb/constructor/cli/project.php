<?php

$project_dir = realpath(dirname(__FILE__) . '/../');

define("LIMB_VAR_DIR", $project_dir . '/var/constructor');

require_once($project_dir . '/setup.php');

lmb_require('limb/taskman/taskman.inc.php');

taskman_propset('PROJECT_DIR', $project_dir);
lmb_require(dirname(__FILE__).'/project.inc.php');
taskman_run();