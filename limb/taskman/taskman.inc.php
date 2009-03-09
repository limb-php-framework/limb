<?php

$GLOBALS['TASKMAN_TASKS'] = array();
$GLOBALS['TASKMAN_TASK_ALIASES'] = array();
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

    taskman_runtasks($this->_getBeforeDeps(), $args);

    taskman_runtasks($this->_getDeps(), $args);

    taskman_sysmsg("************************ Running task '" . $this->getName() . "' ************************\n");

    $TASKMAN_CURRENT_TASK = $this;
    call_user_func_array($this->func, array($this->args));

    taskman_runtasks($this->_getAfterDeps(), $args);

    $this->has_run = true;
    $this->is_running = false;
  }

  private function _getBeforeDeps()
  {
    $arr = array();
    foreach(taskman_gettasks() as $task_obj)
    {
      if($this->getName() == $task_obj->getName())
        continue;

      $before = $task_obj->getPropOr("before", "");
      if($before == $this->getName())
        $arr[] = $task_obj;
    }
    return $arr;
  }

  private function _getAfterDeps()
  {
    $arr = array();
    foreach(taskman_gettasks() as $task_obj)
    {
      if($this->getName() == $task_obj->getName())
        continue;

      $before = $task_obj->getPropOr("after", "");
      if($before == $this->getName())
        $arr[] = $task_obj;
    }
    return $arr;
  }

  private function _getDeps()
  {
    $deps = $this->getPropOr('deps', "");
    if($deps)
      return taskman_parse_taskstr($deps);
    return array();
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

function taskman_str($str)
{
  return preg_replace_callback(
          '~%([^%]+)%~',
          create_function(
            '$matches',
            'return taskman_prop($matches[1]);'
          ),
          $str
        );
}

if(!function_exists('__'))
{
  function __($str) { return taskman_str($str); }
}
else
  taskman_sysmsg("Function __ is already defined somewhere else, use taskman_str() instead");

function taskman_run($argv = null, $help_func = 'task_help')
{
  if(is_null($argv))
    $argv = $GLOBALS['argv'];

  taskman_process_argv($argv);
  $GLOBALS['TASKMAN_SCRIPT'] = array_shift($argv);

  taskman_collecttasks();

  $always_task = null;
  $default_task = null;
  foreach(taskman_gettasks() as $task_obj)
  {
    if($task_obj->hasProp('always'))
      $always_task = $task_obj;
    if($task_obj->hasProp('default'))
      $default_task = $task_obj;
  }

  if(sizeof($argv) > 0)
  {
    $task_str = array_shift($argv);
    $tasks = taskman_parse_taskstr($task_str);

    if($always_task)
      $always_task->run($argv);

    taskman_runtasks($tasks, $argv);
  }
  else
  {
    if($default_task)
    {
      if($always_task)
        $always_task->run($argv);

      $default_task->run($argv);
    }
    else
    {
      $help_func();
      exit();
    }
  }

  taskman_sysmsg("************************ All done ************************\n");
}

function taskman_process_argv(&$argv)
{
  global $TASKMAN_VERBOSE;
  global $TASKMAN_BATCH;

  $filtered = array();
  $process_defs = false;
  for($i=0;$i<sizeof($argv);++$i)
  {
    $v = $argv[$i];

    if($v == '--')
    {
      for($j=$i+1;$j<sizeof($argv);++$j)
        $filtered[] = $argv[$j];
      break;
    }
    else if($v == '-D')
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
    else if($v == '-c')
    {
      if(!isset($argv[$i+1]))
        throw new TaskmanException("Configuration file(-c option) is missing");
      require_once($argv[$i+1]);
      ++$i;
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
  global $TASKMAN_TASK_ALIASES;
  $TASKMAN_TASKS = array();
  $TASKMAN_TASK_ALIASES = array();

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
      if(isset($TASKMAN_TASKS[$alias]) || isset($TASKMAN_TASK_ALIASES[$alias]))
        throw new TaskmanException("Double alias '$alias' definition of task with name '$name'");
      $TASKMAN_TASK_ALIASES[$alias] = $task_obj; 
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
  global $TASKMAN_TASK_ALIASES;

  if(isset($TASKMAN_TASKS[$task]))
    return $TASKMAN_TASKS[$task];

  if(isset($TASKMAN_TASK_ALIASES[$task]))
    return $TASKMAN_TASK_ALIASES[$task];

  throw new TaskmanException("Task with name/alias '{$task}' does not exist");
}

function taskman_parse_taskstr($str)
{
  $task_spec = array();
  $tmp_parall = array();
  $items = preg_split('~(,|\|)~', $str, -1, PREG_SPLIT_DELIM_CAPTURE);

  //TODO: it's kinda messy but working nonetheless :)
  for($i=0;$i<sizeof($items);++$i)
  {
    if(isset($items[$i+1]))
    {
      if($items[$i+1] == ',')
      {
        if($tmp_parall)
        {
          $tmp_parall[] = $items[$i];
          $task_spec[] = $tmp_parall;
          $tmp_parall = array();
        }
        else
          $task_spec[] = $items[$i];
      }
      else if($items[$i+1] == '|')
      {
        $tmp_parall[] = $items[$i];
      }
    }
    else
    {
      if($tmp_parall)
      {
        $tmp_parall[] = $items[$i];
        $task_spec[] = $tmp_parall;
      }
      else
        $task_spec[] = $items[$i];
    }
  }
  return $task_spec;
}

function taskman_runtask($task, $args = array())
{
  $task_obj = taskman_gettask($task);
  $task_obj->run($args);
}

function taskman_runtasks($tasks, $args = array())
{
  foreach($tasks as $task_spec)
  {
    if(is_scalar($task_spec))
      taskman_runtask($task_spec, $args);
    else if(is_array($task_spec))
      taskman_runtasks_parall($task_spec, $args);
    else if(is_object($task_spec))
      $task_spec->run($args);
    else
      throw new TaskmanException("Invalid task specification '$task_spec', should be either string or array or object"); 
  }
}

function taskman_runtasks_parall($tasks, $args = array())
{
  global $TASKMAN_SCRIPT;

  $cmds = array();
  foreach($tasks as $task)
  {
    $cmd = 'php ' .$TASKMAN_SCRIPT . ' ' . $task . ' ' . implode(' ', $args) . '';
    foreach (taskman_getprops() as $key => $value)
      $cmd .= ' -D '.$key .'='.$value;
      
    $cmds[] = $cmd;
  }
  
  taskman_sysmsg("************************ Running parallel tasks: " . implode(',', $tasks) . " ************************\n");

  $procs = array();

  $cnt = 0;
  foreach($cmds as $cmd)
  {
    $descriptorspec = array(
        0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
        1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
        2 => array("file", "/tmp/taskman.parall.err" . ($cnt++), "a") // stderr is a file to write to
      );

    $cwd = getcwd();
    $env = array();
    $proc = proc_open($cmd, $descriptorspec, $pipes, $cwd, $env);
    $procs[] = array($proc, $pipes);
  }

  $timeout = 0;
  while(sizeof($procs) > 0)
  {
    $start = microtime(true);

    //every 5 seconds...
    if($timeout > 5)
    {
      taskman_sysmsg("Still running tasks: " . sizeof($procs) . "\n");
      $timeout = 0;
    }

    $deads = array();
    foreach($procs as $id => $item)
    {
      if(is_resource($item[0])) 
      {
        $status = proc_get_status($item[0]);

        if($status['running'] == false)
        {
          $deads[] = $id;
          taskman_sysmsg("Task '{$status['command']}'($id) completed with status: " . $status['exitcode'] . "\n");
          if($status['exitcode'] != 0)
            throw new TaskmanException("Task '{$status['command']}' execution error");
        }
      }
    }

    foreach($deads as $id)
      unset($procs[$id]);

    usleep(1000);
    $timeout += (microtime(true)-$start);
  }
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
  $props = array();
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
function task_help($args = array())
{
  $filter = '';
  if(isset($args[0]))
    $filter = $args[0];

  $maxlen = -1;
  $tasks = array();
  foreach(taskman_gettasks() as $task)
  {
    if($filter && strpos($task->getName(), $filter) === false)
      continue;

    if(strlen($task->getName()) > $maxlen)
      $maxlen = strlen($task->getName());

    $tasks[] = $task;
  }

  echo "\nUsage:\n php {$GLOBALS['TASKMAN_SCRIPT']} [OPTIONS] <task-name1>[,<task-name2>,..] [-D PROP1=value [-D PROP2]]\n\n";
  echo "Available options:\n";
  echo " -c    specify PHP script to be included(handy for setting props,config options,etc)\n";
  echo " -v    be verbose(default)\n";
  echo " -q    be quite\n";
  echo " -b    batch mode: be super quite, don't even output any system messages\n";
  echo "\n";
  echo "Available tasks:\n";
  foreach($tasks as $task)
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

