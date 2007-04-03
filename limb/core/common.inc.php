<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: common.inc.php 5406 2007-03-29 08:27:59Z pachanga $
 * @package    core
 */
$GLOBALS['LIMB_LAZY_CLASS_PATHS'] = array();

function lmb_resolve_include_path($path)
{
  //this will be replaced with stream_resolve_include_path() in the future versions of PHP
  foreach(lmb_get_include_path_items() as $dir)
  {
    $full_path = "$dir/$path";
    if(is_file($full_path) || is_dir($full_path))
      return $full_path;
  }
}

function lmb_is_readable($file)
{
  $fh = @fopen($file, 'r', true);
  if(!is_resource($fh))
    return false;

  fclose($fh);
  return true;
}

function lmb_glob($path)
{
  if(lmb_is_path_absolute($path))
    return glob($path);

  foreach(lmb_get_include_path_items() as $dir)
  {
    if($res = glob("$dir/$path"))
      return $res;
  }
  return array();
}

function lmb_get_include_path_items()
{
  return explode(PATH_SEPARATOR, get_include_path());
}

function lmb_is_path_absolute($path)
{
  if(!$path)
    return false;

  //very trivial check, more comprehensive required?
  return (($path{0} == '/' || $path{0} == '\\') ||
          (strlen($path) > 2 && $path{1} == ':'));
}

function lmb_require($file_path)
{
  static $tried = array();

  if(isset($tried[$file_path]))
    return;
  else
    $tried[$file_path] = true;

  if(strpos($file_path, '*') !== false)
  {
    foreach(lmb_glob($file_path) as $path)
      lmb_require($path);
    return;
  }

  $file = basename($file_path);
  $items = explode('.', $file);

  if(isset($items[1]))
  {
    if($items[1] == 'class' || $items[1] == 'interface')
    {
      $GLOBALS['LIMB_LAZY_CLASS_PATHS'][$items[0]] = $file_path;
      return;
    }
  }
  else
  {
    if($items[1] == 'class' && class_exists($items[0], false))
      return;
    if($items[1] == 'interface' && interface_exists($items[0], false))
      return;
  }

  if(!include_once($file_path))
    throw new lmbException("Could not include source file '$file_path'");
}

function lmb_autoload($name)
{
  if(isset($GLOBALS['LIMB_LAZY_CLASS_PATHS'][$name]))
  {
    $file_path = $GLOBALS['LIMB_LAZY_CLASS_PATHS'][$name];
    if(!include($file_path))
      throw new lmbException("Could not include source file '$file_path'");
  }
}

function lmb_var_dump($obj)
{
  ob_start();
  var_dump($obj);
  $dump = ob_get_contents();
  ob_end_clean();
  return $dump;
}

/**
 * @see lmb_camel_case
 * @deprecated
 */
function toStudlyCaps($str, $ucfirst = true)
{
  return lmb_camel_case($str, $ucfirst);
}

/**
 * @see lmb_uder_scores
 * @deprecated
 */
function to_under_scores($str)
{
  return lmb_under_scores($str);
}

/**
 * @see lmb_humanize
 * @deprecated
 */
function makeStudlyCapsReadable($str)
{
  return lmb_humanize($str);
}

function lmb_camel_case($str, $ucfirst = true)
{
  $res = preg_replace('~([a-zA-Z])?_([a-zA-Z])~e',
                      "'\\1'.strtoupper('\\2')",
                      $str);
  return ($ucfirst) ? ucfirst($res) : $res;
}

function lmb_under_scores($str)
{
  return ltrim(preg_replace('~([a-z])?([A-Z])([a-z])~e', "'\\1_'.strtolower('\\2').'\\3'", $str),
               '_');
}

function lmb_humanize($str)
{
  return str_replace('_', ' ', lmb_uder_scores($str));
}

lmb_require(dirname(__FILE__) . '/exception/lmbException.class.php');

spl_autoload_register('lmb_autoload');

?>