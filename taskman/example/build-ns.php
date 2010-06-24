#!/usr/bin/env php
<?php

namespace taskman;

require_once(dirname(__FILE__) . '/../taskman.inc.php');

run();

/**
 * @always
 */
function task_setup()
{
  propsetor('PROJECT_DIR', dirname(__FILE__));
}

/**
 * @desc Update working copy
 */
function task_svnup()
{
  sysmsg("Updating working copy...\n");
}

/**
 * @desc Run tests
 * @deps svnup
 */
function task_runtests()
{
  sysmsg("Running tests for project '" . prop('PROJECT_DIR') . "'...\n");
}

/**
 * @desc Build project
 * @deps runtests
 */
function task_build()
{
  sysmsg("Building project '" . prop('PROJECT_DIR') . "'...\n");
}

