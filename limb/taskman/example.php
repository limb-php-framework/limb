<?php

require_once(dirname(__FILE__) . '/taskman.inc.php');

taskman_run();

/**
 * @always
 */
function task_setup()
{
  taskman_propsetor('PROJECT_DIR', dirname(__FILE__));
}

/**
 * @desc Update working copy
 */
function task_svnup()
{
  taskman_sysmsg("Updating working copy...\n");
}

/**
 * @desc Run tests
 */
function task_runtests()
{
  taskman_sysmsg("Running tests for project '" . taskman_prop('PROJECT_DIR') . "'...\n");
}

/**
 * @desc Build project
 * @deps svnup,runtests
 */
function task_build()
{
  taskman_sysmsg("Building project '" . taskman_prop('PROJECT_DIR') . "'...\n");
}
