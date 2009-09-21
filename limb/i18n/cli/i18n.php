<?php

/* See details in README file */

$project_dir = realpath(dirname(__FILE__).'/../../../../');

define("LIMB_VAR_DIR", $project_dir . '/var/i18n');


require_once($project_dir . '/setup.php');

lmb_require('limb/taskman/taskman.inc.php');

taskman_propset('PROJECT_DIR', $project_dir);
taskman_propset('TEMPLATE_DIR',$project_dir.'/template');


lmb_require('limb/i18n/cli/i18n.inc.php');
taskman_run();

lmbFs::rm(LIMB_VAR_DIR);
