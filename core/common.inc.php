<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package core
 * @version $Id: common.inc.php 8119 2010-02-01 13:11:44Z korchasa $
 */
if(!isset($_ENV['LIMB_LAZY_CLASS_PATHS']))
  $_ENV['LIMB_LAZY_CLASS_PATHS'] = array();
if(!isset($_ENV['LIMB_LAZY_TRIED']))
  $_ENV['LIMB_LAZY_TRIED'] = array();
define('LIMB_UNDEFINED', 'undefined' . microtime());
define('LIMB_PACKAGES_DIR', dirname(__FILE__) . '/../');
define('LIMB_DUMP_MAX_DEPTH', 7);
define('LIMB_APP_DEVELOPMENT', 'devel');
define('LIMB_APP_PRODUCTION', 'production');

lmb_require('limb/core/src/assert.inc.php');
lmb_require('limb/core/src/env.inc.php');
lmb_require('limb/core/src/package.inc.php');
lmb_require('limb/core/src/string.inc.php');
lmb_require('limb/core/src/exception/lmbException.class.php');
lmb_require('limb/core/src/exception/lmbPHPFileNotFoundException.class.php');
lmb_require('limb/core/src/exception/lmbInvalidArgumentException.class.php');
lmb_require('limb/core/src/lmbBacktrace.class.php');
lmb_require('limb/core/src/lmbErrorGuard.class.php');

function lmb_resolve_include_path($path)
{
  if(function_exists('stream_resolve_include_path'))
    return stream_resolve_include_path($path);

  foreach(lmb_get_include_path_items() as $dir)
  {
    $full_path = "$dir/$path";
    if(is_file($full_path) || is_dir($full_path))
      return $full_path;
  }

  return false;
}

function lmb_glob($path)
{
  if(lmb_is_path_absolute($path))
    return glob($path);

  $result = array();
  foreach(lmb_get_include_path_items() as $dir)
  {
    if($res = glob("$dir/$path"))
    {
      foreach($res as $item)
         $result[] = $item;
    }
  }
  return $result;
}

function lmb_get_include_path_items()
{
  return explode(PATH_SEPARATOR, get_include_path());
}

function lmb_is_path_absolute($path)
{
  if(!$path)
    return false;

  //very trivial check, is more comprehensive one required?
  return (($path{0} == '/' || $path{0} == '\\') ||
          (strlen($path) > 2 && $path{1} == ':'));
}

function lmb_require($file_path, $class = '')
{
  if(isset($_ENV['LIMB_LAZY_TRIED'][$file_path . $class]))
    return;
  else
    $_ENV['LIMB_LAZY_TRIED'][$file_path . $class] = true;

  if(strpos($file_path, '*') !== false)
  {
    $file_paths = lmb_glob($file_path);
    if(is_array($file_paths))
      foreach($file_paths as $path)
        lmb_require($path);

    return;
  }

  if(!$class)
  {
    //autoguessing class or interface name by file
    $file = basename($file_path);
    $items = explode('.', $file);

    if(isset($items[1]))
    {
      if($items[1] == 'class' || $items[1] == 'interface')
        $class = $items[0];
    }
  }

  if($class)
  {
    $_ENV['LIMB_LAZY_CLASS_PATHS'][$class] = $file_path;
    return;
  }

  try {
    $file_found = include_once $file_path;
  } catch (lmbException $e) {
    if (strpos($e->getMessage(), "include_once($file_path)") !== false)
      $file_found = false;
    else
      throw $e;
  }

  if(!$file_found)
    throw new lmbPHPFileNotFoundException("Could not include source file '$file_path'");
}

function lmb_require_class($file_path, $class = '')
{
  if(!$class)
  {
    //autoguessing class or interface name by file
    $file = basename($file_path);
    $items = explode('.', $file);
    $class = $items[0];
  }

  $_ENV['LIMB_LAZY_CLASS_PATHS'][$class] = $file_path;
}

function lmb_require_glob($file_path)
{
  if(strpos($file_path, '*') !== false)
  {
    foreach(lmb_glob($file_path) as $path)
      lmb_require($path);
  }
  else
    lmb_require($path);
}

function lmb_require_optional($file_path)
{
  if(!file_exists($file_path))
    return;

  lmb_require($file_path);
}

function lmb_autoload($name)
{
  if(isset($_ENV['LIMB_LAZY_CLASS_PATHS'][$name]))
  {
    $file_path = $_ENV['LIMB_LAZY_CLASS_PATHS'][$name];
    //is it safe to use include here instead of include_once?
    if(!include_once($file_path))
    {
      $message = "Could not include source file '$file_path'";
      if(class_exists('lmbException') && class_exists('lmbBacktrace'))
      {
        $trace = new lmbBacktrace(10, 1);
        throw new lmbException($message, array('trace' => $trace->toString()));
      }
      else
      {
        throw new Exception($message);
      }
    }
  }
}

function lmb_var_dump($arg, $echo = false, $level = 1)
{
	if($echo)
	  echo lmb_var_export($arg, $level);
	else
	  return lmb_var_export($arg, $level);
}

function lmb_var_export($arg, $level = 1)
{
	$prefix = str_repeat('  ', ($level > 0) ? ($level - 1) : 0);
  switch(gettype($arg))
  {
  	case 'NULL':
  		return 'NULL';

  	case 'boolean':
  		return $arg ? 'TRUE' : 'FALSE';

    case 'integer':
      return 'INT('.$arg.')';

    case 'double':
      return 'FLOAT('.$arg.')';

  	case 'resource':
  		if(is_resource($arg))
  		{
        $resource_id = strstr((string) $arg, '#');
        $resource_type = get_resource_type($arg);
        return "RESOURCE($resource_id) of type (" . get_resource_type($arg) . ")";
  		}
  		else
  		  return lmb_var_export((string) $arg);

    case 'object':

    	if (LIMB_DUMP_MAX_DEPTH == $level)
    	  return 'OBJECT(' . get_class($arg) . ')';
    	$_ENV['LIMB_VAR_EXPORT_SHOWED_OBJECTS'][spl_object_hash($arg)] = true;

      if ($level == LIMB_DUMP_MAX_DEPTH)
      	return 'OBJECT(' . get_class($arg) . ")";
      else
      {
	      $dump =  'OBJECT(' . get_class($arg) . ") {";
	      if (get_object_vars($arg))
	      {
	      	$dump .= PHP_EOL;
		      foreach(get_object_vars($arg) as $name => $value)
		      {
		      	$dump .= $prefix . "  [\"$name\"]=> "
		      	  . lmb_var_export($value, $level + 1)
		      	  . PHP_EOL;
		      }
		      $dump .= $prefix;
	      }
	      $dump .=  "}";
	      return $dump;
      }

  	case 'array':

  		if($level == LIMB_DUMP_MAX_DEPTH)
  		   return 'ARRAY(' . sizeof($arg) . ')';
  		else
      {
        $dump = "ARRAY(".sizeof($arg).') [';
        if(sizeof($arg))
        {
          $dump .= PHP_EOL;
	        foreach($arg as $arr_key => $arr_value)
	          $dump .= $prefix . "  [$arr_key] => ".lmb_var_export($arr_value, $level + 1).PHP_EOL;
	        $dump .= $prefix;
        }
	      $dump .= "]";
	      return $dump;
	    }

  	case 'string':
      $dump = 'STRING('.strlen($arg).') "';
      $dump .= lmb_escape_string((string) $arg, 100);

      if(strlen($arg) > 100)
        $dump .= '...';

      $dump .= '"';
      return $dump;

  	default:
  		return var_export($arg, true);
  }
}

/**
 * Function for quice print variable data without special constructions
 * @example $obj->callSomeMethod(lmb_var_debug($some_argument))
 * @param mixed $arg
 * @return original arg
 */
function lmb_var_debug($arg)
{
  echo lmb_var_export($arg);
  return $arg;
}

/**
 * Escaping string for log.
 * If we work in cli mode we don't need htmlspecialchars
 * @param $string
 */
function lmb_escape_string($string, $length_limit = 100)
{
  if ('cli' == php_sapi_name())
    return substr($string, 0, $length_limit);
  else
    return htmlspecialchars(substr($string, 0, $length_limit));
}

function lmb_var_dir($new_value = null)
{
  if($new_value)
    lmb_env_set('LIMB_VAR_DIR', $new_value);
  else
    return lmb_env_get('LIMB_VAR_DIR');
}

function lmb_app_mode($new_value = null)
{
  if($new_value)
    lmb_env_set('LIMB_APP_MODE', $new_value);
  else
    return lmb_env_get('LIMB_APP_MODE', LIMB_APP_PRODUCTION);
}

spl_autoload_register('lmb_autoload');
new lmbException('ugly hack');
new lmbPHPFileNotFoundException('ugly hack');

lmbErrorGuard::registerErrorHandler('lmbErrorGuard', 'convertErrorsToExceptions');