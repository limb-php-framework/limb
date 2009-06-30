<?php

namespace taskman;

require_once(dirname(__FILE__) . '/taskman.inc.php'); 

function __($str)
{
  return \taskman_str($str);
}

function reset()
{
  return \taskman_reset();
}

function str($str)
{
  return \taskman_str($str);
}

function run($argv=null, $help_func='task_help')
{
  return \taskman_run($argv,$help_func);
}

function process_argv(&$argv)
{
  return \taskman_process_argv($argv);
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

function parse_taskstr($str)
{
  return \taskman_parse_taskstr($str);
}

function runtask($task, $args=array())
{
  return \taskman_runtask($task,$args);
}

function runtasks($tasks, $args=array(), $isolate=false)
{
  return \taskman_runtasks($tasks,$args,$isolate);
}

function runtasks_parall($tasks, $args=array())
{
  return \taskman_runtasks_parall($tasks,$args);
}

function current_task()
{
  return \taskman_current_task();
}

function shell_ensure($cmd, &$out=null)
{
  return \taskman_shell_ensure($cmd,$out);
}

function shell($cmd, &$ret=null, &$out=null)
{
  return \taskman_shell($cmd,$ret,$out);
}

function msg($msg)
{
  return \taskman_msg($msg);
}

function sysmsg($msg)
{
  return \taskman_sysmsg($msg);
}

function prop($name)
{
  return \taskman_prop($name);
}

function propor($name, $def)
{
  return \taskman_propor($name,$def);
}

function propset($name, $value)
{
  return \taskman_propset($name,$value);
}

function propsetor($name, $value)
{
  return \taskman_propsetor($name,$value);
}

function propunset($name)
{
  return \taskman_propunset($name);
}

function getprops()
{
  return \taskman_getprops();
}

function isprop($name)
{
  return \taskman_isprop($name);
}

function configset($name, $value)
{
  return \taskman_configset($name,$value);
}

function config($name, $def=null)
{
  return \taskman_config($name,$def);
}

function rmdir_recurse($path)
{
  return \taskman_rmdir_recurse($path);
}

