<?php

$GLOBALS['TASKMAN_TASKS'] = array();
$GLOBALS['TASKMAN_VERBOSE'] = true;
$GLOBALS['TASKMAN_BATCH'] = false;
$GLOBALS['TASKMAN_SCRIPT'] = 'taskman-script.php';
$GLOBALS['TASKMAN_CURRENT_TASK'] = null;

class TaskmanException extends Exception{}

class TaskmanTask
{
  private $func;
  private $name;
  private $props = array();
  private $is_running = false;
  private $has_run = false;
  private $args = array();

  function __construct($func)
  {
    if(!is_callable($func))
      throw new TaskmanException("Task '{$func}' is non-callable");

    $this->_parseProps($func);
    $this->_parseName($func);

    $this->func = $func;
  }

  function getName()
  {
	return $this->name;    
  }

  function getArgs()
  {
    return $this->args;
  }

  function getAliases()
  {
    $alias_str = $this->getPropOr("alias", "");
    if(!$alias_str)
      return array();
    return explode(",", $alias_str);
  }

  function run($args = array())
  {
    global $TASKMAN_CURRENT_TASK;

    if($this->has_run || $this->is_running)
      return;

    $this->is_running = true;
    $this->args = $args;

    $this->_runDeps($args);

    taskman_sysmsg("************************ Running task '" . $this->getName() . "' ************************\n");

    $TASKMAN_CURRENT_TASK = $this;
    call_user_func_array($this->func, array($this->args));

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

  private function _runDeps($args = array())
  {
    foreach($this->_getDeps() as $task)
      $task->run($args);
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

  private function _parseName($func)
  {
    $this->name = self :: extractName($func);
  }

  static function extractName($func)
  {
    if(strpos($func, "task_") === 0)
      return substr($func, strlen('task_'), strlen($func));
    else if(strpos($func, 'taskman\task_') === 0)
      return substr($func, strlen('taskman\task_'), strlen($func));
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
  $GLOBALS['TASKMAN_SCRIPT'] = array_shift($argv);//shifting first element

  taskman_collecttasks();

  if(sizeof($argv) < 1)
  {
    $help_func();
    exit();
  }

  foreach(taskman_gettasks() as $task_obj)
  {
    if($task_obj->hasProp('always'))
      $task_obj->run();
  }
  
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
    else if($v == '-q')
    {
      $TASKMAN_VERBOSE = false;
    }
    else if($v == '-v')
    {
      $TASKMAN_VERBOSE = true;
    }
    else if($v == '-b')
    {
      $TASKMAN_BATCH = true;
      $TASKMAN_VERBOSE = false;
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
  $user = $funcs['user'];
  sort($user);
  foreach($user as $func)
  {
    $name = TaskmanTask::extractName($func);
    if(!$name)
      continue;

    if(isset($TASKMAN_TASKS[$name]))
      throw new TaskmanException("Double definition of task with name '$name'");

    $task_obj = new TaskmanTask($func);
    $TASKMAN_TASKS[$name] = $task_obj; 

    foreach($task_obj->getAliases() as $alias)
    {
      if(isset($TASKMAN_TASKS[$alias]))
        throw new TaskmanException("Double alias '$alias' definition of task with name '$name'");
      $TASKMAN_TASKS[$alias] = $task_obj; 
    }
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

  if(!isset($TASKMAN_TASKS[$task]))
    throw new TaskmanException("Task '{$task}' does not exist");

  return $TASKMAN_TASKS[$task];
}

function taskman_runtask($task, $args = array())
{
  $task_obj = taskman_gettask($task);
  $task_obj->run($args);
}

function taskman_current_task()
{
  global $TASKMAN_CURRENT_TASK;
  return $TASKMAN_CURRENT_TASK;
}

function taskman_shell_ensure($cmd)
{
  taskman_shell($cmd, $ret);
  if($ret != 0)
    throw new TaskmanException("Script execution error(return code $ret)");
}

function taskman_msg($msg)
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
    system("$cmd 2>&1", $ret);
  }
  else
    exec("$cmd 2>&1", $out, $ret);
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

  echo "\nUsage:\n php {$GLOBALS['TASKMAN_SCRIPT']} [OPTIONS] <task-name1>[,<task-name2>,..] [-D PROP1=value [-D PROP2]]\n\n";
  echo "Available options:\n";
  echo " -v    be verbose(default)\n";
  echo " -q    be quite\n";
  echo " -b    batch mode: be super quite, don't even output any system messages\n";
  echo "\n";
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

    echo "---------------------------------\n";
    echo " " . $task->getName() .  $props_string . "\n";
  }
  echo "\n";
}

/**
 * @desc Encodes tasks as json array
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

if(version_compare(PHP_VERSION, '5.2.99', '>') && file_exists(dirname(__FILE__) . '/taskman-ns.inc.php'))
  require_once(dirname(__FILE__) . '/taskman-ns.inc.php');

