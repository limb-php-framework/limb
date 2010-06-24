#!/usr/bin/env php
<?php

require_once(dirname(__FILE__) . '/../taskman.inc.php');

taskman_run();

function write_to_file($text)
{
  $fp = fopen(taskman_prop('LOG'), 'a');
  fwrite($fp, $text);
  fclose($fp);
}

/**
 * @always
 */
function task_setup($args)
{
  write_to_file($args[0]);
  taskman_propsetor('PROJECT_DIR', dirname(__FILE__));
}

/**
 * @desc Update working copy
 */
function task_svnup()
{
  write_to_file('u');
  taskman_sysmsg("Updating working copy...\n");
}

/**
 * @desc Run tests
 * @deps svnup
 */
function task_runtests()
{
  write_to_file('r');
  taskman_sysmsg("Running tests for project '" . taskman_prop('PROJECT_DIR') . "'...\n");
}

/**
 * @desc Run tests
 */
function task_create_shares()
{
  write_to_file('s');
  taskman_sysmsg("Running tests for project '" . taskman_prop('PROJECT_DIR') . "'...\n");
}

/**
 * @desc Build project
 * @deps runtests,create_shares
 * @todo deps must call in || requests
 */
function task_build()
{
  sleep(1);
  write_to_file('b');
  taskman_sysmsg("Building project '" . taskman_prop('PROJECT_DIR') . "'...\n");
}

/**
 * @desc Remove some old stuff
 */
function task_remove_old()
{
  write_to_file('o');
  taskman_sysmsg("Remove uselles files from '" . taskman_prop('PROJECT_DIR') . "'...\n");
}