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
function lmb_assert_true($value, $message = 'Value must be positive', $e_class = 'lmbInvalidArgumentException')
{
  if ($value)
    return;

  if ($e_class instanceof lmbException)
    throw new $e_class($message, array('value' => $value), 0, 1);
  else
    throw new $e_class($message);
}

function lmb_assert_type(
  $value,
  $expected_type,
  $message = 'Value must be a %expected% type, but %given% given',
  $exception_class = 'lmbInvalidArgumentException'
)
{
  $given_type = gettype($value);

  if($expected_type === $given_type)
    return;

  $aliases = array(
    'bool' => 'boolean',
    'numeric' => 'integer',
    'int' => 'integer',
    'float' => 'double',
    'real' => 'double',
  );
  if(isset($aliases[$expected_type]) && $aliases[$expected_type] == $given_type)
    return;

  if('array' == $expected_type && 'object' == $given_type && $value instanceof ArrayAccess)
    return;

  if('object' == $given_type && $value instanceof $expected_type)
    return;

  $message = str_replace('%expected%', $expected_type, $message);
  $message = str_replace('%given%', $given_type, $message);

  if ($exception_class instanceof lmbException)
    throw new $exception_class($message, array('value' => $value), 0, 1);
  else
    throw new $exception_class($message);
}

function lmb_assert_array_with_key(
  $array,
  $key_or_keys,
  $message = 'Value is not an array or doesn\'t have a key(s) "%keys%"',
  $exception_class = 'lmbInvalidArgumentException'
)
{
  if(!is_array($key_or_keys))
    $key_or_keys = array($key_or_keys);

  if(is_array($array) || (is_object($array) && $array instanceof ArrayAccess))
  {
    $value_keys = array_keys((array) $array);
    $missed_keys = array_diff($key_or_keys, $value_keys);
    if(0 === count($missed_keys))
      return;
  }
  else
  {
    $missed_keys = array();
  }

  $message = str_replace('%keys%', implode(', ', $missed_keys), $message);
  if ($exception_class instanceof lmbException)
  {
  	$params = array(
      'value type' => gettype($array),
      'missed keys' => $missed_keys,
    );
    throw new $exception_class($message, $params, 0, 1);
  }
  else
    throw new $exception_class($message);
}

function lmb_assert_reg_exp(
  $string,
  $pattern,
  $message = 'Value is not an string or pattern "%pattern%" not found',
  $exception_class = 'lmbInvalidArgumentException'
)
{
  if(is_string($string) || (is_object($string) && method_exists($string, '__toString')))
  {
	  if('/' != $pattern[0] && '{' != $pattern[0] && '|' != $pattern[0])
	  {
	    if(false !== strpos($string, $pattern))
	      return;
	  }
	  elseif(preg_match($pattern, $string))
	    return;
  }

  $message = str_replace('%pattern%', $pattern, $message);
  if ($exception_class instanceof lmbException)
  {
  	$params = array(
      'value type' => gettype($array),
      'pattern' => $pattern,
  	  'string' => $string,
    );
    throw new $exception_class($message, $params, 0, 1);
  }
  else
    throw new $exception_class($message);
}