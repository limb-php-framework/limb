<?php

$GLOBALS['TASKMAN_TASKS'] = array();
$GLOBALS['TASKMAN_STACK'] = array();
$GLOBALS['TASKMAN_TASK_ALIASES'] = array();
$GLOBALS['TASKMAN_VERBOSE'] = true;
$GLOBALS['TASKMAN_BATCH'] = false;
$GLOBALS['TASKMAN_SCRIPT'] = 'taskman-script.php';
$GLOBALS['TASKMAN_CURRENT_TASK'] = null;
$GLOBALS['TASKMAN_CONFIG'] = array();

class TaskmanException extends Exception
{
  public function __toString()
  {
    global $TASKMAN_STACK;

    $stack_str = ''; 
    foreach($TASKMAN_STACK as $task)
    {
      $stack_str .= '->' . $task->getName();
    }
    return get_class($this) . 
            " '{$this->message}' in {$this->file}({$this->line})\n" . 
            "{$this->getTraceAsString()}\ntasks: $stack_str";
  }
}

class TaskmanTask
{
  private $func;
  private $name;
  private $props = array();
  private $is_running = false;
  private $has_run = array();
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

  function getFunc()
  {
    return $this->func;
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
    global $TASKMAN_STACK;

    $args_str = serialize($args);

    if((isset($this->has_run[$args_str]) && $this->has_run[$args_str]) || 
       $this->is_running)
      return;

    $this->is_running = true;
    $this->args = $args;

    try
    {

      if($task_handler = taskman_config('task_handler'))
        $task_handler($this);

      taskman_runtasks($this->_getBeforeDeps(), $this->args);

      taskman_runtasks($this->_getDeps(), $this->args);

      taskman_sysmsg("************************ Running task '" . $this->getName() . "' ************************\n");

      $bench = microtime(true);

      $TASKMAN_CURRENT_TASK = $this;
      $TASKMAN_STACK[] = $this;

      if($replacer = $this->_getReplacingTask())
        taskman_runtask($replacer, $this->args);
      else
        call_user_func_array($this->func, array($this->args));

      array_pop($TASKMAN_STACK);

      taskman_runtasks($this->_getAfterDeps(), $this->args);

      taskman_sysmsg("************************* '" . $this->getName() . "' done (" . 
                     round(microtime(true)-$bench,2) . " sec.)*************************\n");

      $this->has_run[$args_str] = true;
      $this->is_running = false;
    }
    catch(Exception $e)
    {
      if($error_handler = taskman_config('error_handler'))
        $error_handler($e);
      else
        throw $e;
    }
  }

  private function _getBeforeDeps()
  {
    return $this->_collectRelatedTasks("before");
  }

  private function _getAfterDeps()
  {
    return $this->_collectRelatedTasks("after");
  }

  private function _getReplacingTask()
  {
    $replaces = $this->_collectRelatedTasks("replace");
    if(sizeof($replaces) == 0)
      return null;
    else
      return end($replaces);
  }

  private function _collectRelatedTasks($prop_name)
  {
    $arr = array();
    foreach(taskman_gettasks() as $task_obj)
    {
      if($this->getName() == $task_obj->getName())
        continue;

      $value = $task_obj->getPropOr($prop_name, "");
      if($value == $this->getName() || in_array($value, $this->getAliases()))
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

  private function _runDeps($args = array())
  {
    taskman_runtasks($this->_getDeps(), $args);
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

function taskman_reset()
{
  $GLOBALS['TASKMAN_TASKS'] = array();
  $GLOBALS['TASKMAN_TASK_ALIASES'] = array();
  $GLOBALS['TASKMAN_CURRENT_TASK'] = null;
  $GLOBALS['TASKMAN_CONFIG'] = array();

  taskman_collecttasks();
}

function taskman_str($str)
{
  return preg_replace_callback(
          '~%([^%]+)%~',
          create_function(
            '$m',
            'return taskman_prop($m[1]);'
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

  $bench = microtime(true);

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

  taskman_sysmsg("************************ All done (".round(microtime(true)-$bench,2)." sec.)************************\n");
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
      $eq_pos = strpos($v, '=');
      if($eq_pos !== false)
      {
        $def_name = substr($v, 0, $eq_pos);
        $def_value = substr($v, $eq_pos+1);
      }
      else
      {
        $def_name = $v;
        $def_value = 1;
      }

      taskman_sysmsg("Setting prop $def_name=$def_value\n");
      taskman_propset($def_name, $def_value);
      
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

  if(!is_scalar($task))
  {
    var_dump($task);
    throw new TaskmanException("Bad task name");
  }

  if(isset($TASKMAN_TASKS[$task]))
    return $TASKMAN_TASKS[$task];

  if(isset($TASKMAN_TASK_ALIASES[$task]))
    return $TASKMAN_TASK_ALIASES[$task];

  throw new TaskmanException("Task with name/alias '{$task}' does not exist");
}

function taskman_parse_taskstr($str)
{
  $task_spec = array();
  $items = explode(',', $str);
  foreach($items as $item)
  {
    $args = null;
    $task = $item;
    if(strpos($item, ' ') !== false)
      @list($task, $args) = explode(' ', $item, 2);

    if($args)
      $task_spec[] = array($task, explode(' ', $args));
    else
      $task_spec[] = $task;
  }
  return $task_spec;
}

function taskman_runtask($task, $args = array())
{
  if($task instanceof TaskmanTask)
    $task_obj = $task;
  else
    $task_obj = taskman_gettask($task);

  $task_obj->run($args);
}

function taskman_runtasks($tasks, $args = array(), $isolate = false)
{
  global $TASKMAN_SCRIPT;

  if(!$isolate)
  {
    foreach($tasks as $task_spec)
    {
      if(is_array($task_spec))
        taskman_runtask($task_spec[0], $task_spec[1]);
      else
        taskman_runtask($task_spec, $args);
    }
  }
  else
  {
    $cmd = 'php ' .$TASKMAN_SCRIPT . ' ' . implode(',', $tasks) . ' ' . implode(' ', $args) . '';
    foreach(taskman_getprops() as $key => $value)
      $cmd .= ' -D '.$key .'='. escapeshellarg($value);

    taskman_shell_ensure($cmd);
  }
}

function taskman_current_task()
{
  global $TASKMAN_CURRENT_TASK;
  return $TASKMAN_CURRENT_TASK;
}

function taskman_shell_ensure($cmd, &$out=null)
{
  taskman_shell($cmd, $ret, $out);
  if($ret != 0)
    throw new TaskmanException("Shell execution error(exit code $ret)" .  (is_array($out) ? ":\n" . implode("\n", $out) : ''));
}

function taskman_shell($cmd, &$ret=null, &$out=null)
{
  taskman_msg(" shell: $cmd\n");
  taskman_msg(" ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^\n");

  _taskman_execute_proc_cmd($cmd, $ret, $out);
}

function _taskman_execute_proc_cmd($cmd, &$ret, &$out)
{
  //TODO: do we really need to redirect error stream?
  $proc = popen("$cmd 2>&1", 'r');

  $log = '';
  //TODO: how can this be?
  if(is_string($proc))
  {
    $log = $proc;
    _taskman_log($log);
  }
  else
  {
    while($logline = fgets($proc))
    {
      $log .= $logline;
      _taskman_log($logline);
    }
  }
  $out = explode("\n", $log);
  $ret = pclose($proc);
}

function taskman_msg($msg)
{
  global $TASKMAN_VERBOSE;

  if($TASKMAN_VERBOSE)
    _taskman_log($msg);
}

function taskman_sysmsg($msg)
{
  global $TASKMAN_BATCH;

  if($TASKMAN_BATCH == false)
    _taskman_log($msg);
}

function _taskman_log($msg)
{
  $logger = taskman_config('logger', '_taskman_default_logger');
  call_user_func_array($logger, array($msg));
}

function _taskman_default_logger($msg)
{
  echo $msg;
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

function taskman_propunset($name)
{
  unset($_ENV['TASKMAN_' . $name]);
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

function taskman_configset($name, $value)
{
  $GLOBALS['TASKMAN_CONFIG'][$name] = $value;
}

function taskman_config($name, $def=null)
{
  if(isset($GLOBALS['TASKMAN_CONFIG'][$name]))
    return $GLOBALS['TASKMAN_CONFIG'][$name];
  return $def;
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

  echo "\nUsage:\n php <taskman-script> [OPTIONS] <task-name1>[,<task-name2>,..] [-D PROP1=value [-D PROP2]]\n\n";
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

//}}}

if(version_compare(PHP_VERSION, '5.2.99', '>') && file_exists(dirname(__FILE__) . '/taskman-ns.inc.php'))
  require_once(dirname(__FILE__) . '/taskman-ns.inc.php');

