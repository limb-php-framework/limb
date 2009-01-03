<?php

namespace taskman;

require_once(dirname(__FILE__) . '/taskman.inc.php');

function run($argv = null, $help_func = 'task_help')
{
  return \taskman_run($argv, $help_func);
}

function process_argv(&$argv)
{
  return \taskman_process_argv(&$argv);
}

function collecttasks()
{
  return \taskman_collecttasks();
}

function gettasks()
{
  return \taskman_gettasks();
}

function gettask($task)
{
  return \taskman_gettask($task);
}

function taskman_runtask($task, $args = array())
{
  return \taskman_runtask($task, $args);
}

function shell_ensure($cmd)
{
  return \taskman_shell_ensure($cmd);
}

function msg($msg)
{
  return \taskman_msg($msg);
}

function sysmsg($msg)
{
  return \taskman_sysmsg($msg);
}

function shell($cmd, &$ret=null)
{
  return \taskman_shell($cmd, $ret);
}

function prop($name)
{
  return \taskman_prop($name);
}

function propor($name, $def)
{
  return \taskman_propor($name, $def);
}

function propset($name, $value)
{
  return \taskman_propset($name, $value);
}

function propsetor($name, $value)
{
  return \taskman_propsetor($name, $value);
}

function getprops()
{
  return \taskman_getprops();
}

function isprop($name)
{
  return \taskman_isprop($name);
}

function rmdir_recurse($path)
{
  return \taskman_rmdir_recurse($path);
}

