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
 * @version $Id$
 */
function lmb_env_has($name)
{
  if(array_key_exists($name, $_ENV))
  {
    if($_ENV[$name] === LIMB_UNDEFINED)
      return false;
    else
      return true;
  }

  if(defined($name))
    return true;

  return false;
}

function lmb_env_get($name, $def = null)
{
  if(array_key_exists($name, $_ENV))
  {
    if($_ENV[$name] === LIMB_UNDEFINED)
      return $def;
    else
      return $_ENV[$name];
  }

  if(defined($name))
    return constant($name);

  return $def;
}

function lmb_env_setor($name, $value)
{
  if(!array_key_exists($name, $_ENV))
  {
    if(defined($name))
    {
       $_ENV[$name] = constant($name);
    }
    else
    {
      $_ENV[$name] = $value;
      define($name, $value);
    }
  }

  if(lmb_env_trace_has($name))
    lmb_env_trace_show();
}

function lmb_env_set($name, $value)
{
  $_ENV[$name] = $value;

  if(!defined($name))
    define($name, $value);

  if(lmb_env_trace_has($name))
    lmb_env_trace_show();
}

function lmb_env_remove($name)
{
  $_ENV[$name] = LIMB_UNDEFINED;
}

function lmb_env_trace($name)
{
  lmb_env_setor('profile' . $name . LIMB_UNDEFINED, true);
}

function lmb_env_trace_has($name)
{
  return lmb_env_has('profile' . $name . LIMB_UNDEFINED);
}

function lmb_env_trace_show()
{
  $trace = debug_backtrace();
  $trace = $trace[1];

  $file_str = 'Called '.$trace['file'].'@'.$trace['line'];
  $call_str = $trace['function'].'('.$trace['args'][0].','.$trace['args'][1].')';
  echo $file_str.' '.$call_str.PHP_EOL;
}
