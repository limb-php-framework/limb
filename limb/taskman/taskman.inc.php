<?php

$GLOBALS['TASKMAN_TASKS'] = array();
$GLOBALS['TASKMAN_VERBOSE'] = false;
$GLOBALS['TASKMAN_BATCH'] = false;

class TaskmanException extends Exception{}

class TaskmanTask
{
  private $func;
  private $props = array();
  private $is_running = false;
  private $has_run = false;

  function __construct($func)
  {
    if(!is_callable($func))
      throw new TaskmanException("Task '{$func}' is non-callable");

    $this->_parseProps($func);

    $this->func = $func;
  }

  function getName()
  {
    return substr($this->func, strlen('task_'), strlen($this->func));
  }

  function run($args = array())
  {
    if($this->has_run || $this->is_running)
      return;

    $this->is_running = true;

    $this->_runDeps();

    taskman_sysmsg("************************ Running task '" . $this->getName() . "' ************************\n");
    call_user_func_array($this->func, $args);

    $this->has_run = true;
    $this->is_running = false;
  }

  private function _getDeps()
  {
    $deps = explode(',', $this->getPropOr('deps', ""));
    $tasks = array();
    foreach($deps as $dep)
    {
      if($dep)
        $tasks[] = taskman_gettask($dep);
    }
    return $tasks;
  }

  private function _runDeps()
  {
    foreach($this->_getDeps() as $task)
      $task->run();
  }

  private function _parseProps($func)
  {
    $refl = new ReflectionFunction($func);
    if(preg_match_all('~@(\S+)([^\n]+)?\n~', $refl->getDocComment(), $matches))
    {
      foreach($matches[1] as $idx => $match)
        $this->props[$match] = trim($matches[2][$idx]);
    }
  }

  function getPropOr($name, $def)
  {
    return isset($this->props[$name]) ? $this->props[$name] : $def;
  }

  function getProp($name)
  {
    return $this->getPropOr($name, null);
  }

  function hasProp($name)
  {
    return isset($this->props[$name]);
  }

  function getProps()
  {
    return $this->props;
  }
}

function taskman_run($argv = null, $help_func = 'task_help')
{
  if(is_null($argv))
    $argv = $GLOBALS['argv'];

  taskman_process_argv($argv);

  if(sizeof($argv) < 2)
  {
    $help_func();
    exit();
  }

  taskman_collecttasks();

  foreach(taskman_gettasks() as $task)
  {
    if($task->hasProp('always'))
      $task->run();
  }

  array_shift($argv);//shifting first element
  $task = array_shift($argv);

  //TODO: think better about multiple tasks execution and their args
  $tasks = explode(",", $task);
  foreach($tasks as $task)
    taskman_runtask($task, $argv);

  taskman_sysmsg("************************ All done ************************\n");
}

function taskman_process_argv(&$argv)
{
  global $TASKMAN_VERBOSE;
  global $TASKMAN_BATCH;

  $filtered = array();
  $process_defs = false;
  foreach($argv as $v)
  {
    if($v == '-D')
    {
      $process_defs = true;
    }
    else if($v == '-v')
    {
      $TASKMAN_VERBOSE = true;
    }
    else if($v == '-b')
    {
      $TASKMAN_BATCH = true;
    }
    else if($process_defs)
    {
      $defs = explode(',', $v);
      foreach($defs as $def)
      {
        taskman_sysmsg("Setting prop '$def'\n");
        @list($def_name, $def_value) = explode("=", $def);
        if(!isset($def_value))
          taskman_propset($def_name, "yes");
        else
          taskman_propset($def_name, $def_value);
      }
      $process_defs = false;
    }
    else
      $filtered[] = $v;
  }
  $argv = $filtered;
}

function taskman_collecttasks()
{
  global $TASKMAN_TASKS;
  $TASKMAN_TASKS = array();

  $funcs = get_defined_functions();
  sort($funcs['user']);
  foreach($funcs['user'] as $func)
  {
    if(strpos($func, "task_") === 0)
      $TASKMAN_TASKS[$func] = new TaskmanTask($func);
  }
}

function taskman_gettasks()
{
  global $TASKMAN_TASKS;
  return $TASKMAN_TASKS;
}

function taskman_gettask($task)
{
  global $TASKMAN_TASKS;

  $func = "task_" . $task;
  if(!isset($TASKMAN_TASKS[$func]))
    throw new TaskmanException("Task '{$task}' does not exist");

  return $TASKMAN_TASKS[$func];
}

function taskman_runtask($task, $args = array())
{
  $task_obj = taskman_gettask($task);
  $task_obj->run($args);
}

function taskman_shell_ensure($cmd)
{
  taskman_shell($cmd, $ret);
  if($ret != 0)
    throw new TaskmanException("Script execution error(return code $ret)");
}

function taskman_echo($msg)
{
  global $TASKMAN_VERBOSE;

  if($TASKMAN_VERBOSE)
    echo($msg);
}

function taskman_sysmsg($msg)
{
  global $TASKMAN_BATCH;

  if($TASKMAN_BATCH == false)
    echo($msg);
}

function taskman_shell($cmd, &$ret=null)
{
  global $TASKMAN_VERBOSE;

  if($TASKMAN_VERBOSE)
  {
    echo " shell: $cmd\n";
    echo " ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^\n";
    system($cmd, $ret);
  }
  else
    exec($cmd, $out, $ret);
}

function taskman_prop($name)
{
  if(!isset($_ENV['TASKMAN_' . $name]))
    throw new TaskmanException("Property '$name' is not set");
  return $_ENV['TASKMAN_' . $name];
}

function taskman_propor($name, $def)
{
  if(!isset($_ENV['TASKMAN_' . $name]))
    return $def;
  return $_ENV['TASKMAN_' . $name];
}

function taskman_propset($name, $value)
{
  $_ENV['TASKMAN_' . $name] = $value;
}

function taskman_propsetor($name, $value)
{
  if(!isset($_ENV['TASKMAN_' . $name]))
    $_ENV['TASKMAN_' . $name] = $value;
}

function taskman_getprops()
{
  foreach($_ENV as $key => $value)
  {
    if(($idx = strpos($key, 'TASKMAN_')) === 0)
      $props[substr($key, strlen('TASKMAN_'))] = $value;
  }
  return $props;
}

function taskman_isprop($name)
{
  return isset($_ENV['TASKMAN_' . $name]);
}

function taskman_rmdir_recurse($path)
{
  $path= rtrim($path, '/').'/';
  $handle = opendir($path);
  for(;false !== ($file = readdir($handle));)
  {
    if($file != "." and $file != ".." )
    {
      $fullpath= $path.$file;
      if( is_dir($fullpath) )
      {
        taskman_rmdir_recurse($fullpath);
        rmdir($fullpath);
      }
      else
        unlink($fullpath);
    }
  }
  closedir($handle);
}

//{{{builtin tasks
/**
 * @desc Shows this help
 */
function task_help()
{
  $maxlen = -1;
  foreach(taskman_gettasks() as $task)
  {
    if(strlen($task->getName()) > $maxlen)
      $maxlen = strlen($task->getName());
  }

  echo "Available tasks:\n";
  foreach(taskman_gettasks() as $task)
  {
    $props_string = '';
    $pad = $maxlen - strlen($task->getName());
    foreach($task->getProps() as $name => $value)
    {
      $props_string .= str_repeat(" ", $pad) .'  @' . $name . ' ' . $value . "\n";
      $pad = $maxlen + 1;
    }
    $props_string = rtrim($props_string);

    echo " " . $task->getName() .  $props_string . "\n";
  }
}

/**
 * @doc Encodes tasks as json array
 */
function task_json()
{
  $json = array();
  foreach(taskman_gettasks() as $task)
  {
    $json[$task->getName()] = $task->getProps();
  }
  echo json_encode($json);
}

//}}}

