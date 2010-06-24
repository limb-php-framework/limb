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
function lmb_assert_true($value, $custom_message = 'Value must be true')
{
  if(!$value)
    throw new lmbInvalidArgumentException($custom_message, array('value' => $value), 0, 1);
}

function lmb_assert_type($value, $expected_type, $custom_message = LIMB_UNDEFINED)
{
  if(LIMB_UNDEFINED === $custom_message)
    $custom_message = 'Value must be a '.$expected_type.' type.';

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

  throw new lmbInvalidArgumentException($custom_message, array('value' => $value), 0, 1);
}

function lmb_assert_array_with_key($array, $key_or_keys, $custom_message = LIMB_UNDEFINED)
{
  if(LIMB_UNDEFINED === $custom_message)
    $custom_message = 'Value is not an array or doesn\'t have a key "'.$key_or_keys.'"';

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

  $params = array(
    'value type' => gettype($array),
    'missed_keys' => $missed_keys,
  );
  throw new lmbInvalidArgumentException($custom_message, $params, 0, 1);
}

function lmb_assert_reg_exp($string, $pattern, $custom_message = LIMB_UNDEFINED)
{
  if(LIMB_UNDEFINED === $custom_message)
    $custom_message = 'Value is not an string or pattern  "'.$pattern.'" not found';

  if(!is_string($string) && !(is_object($string) && method_exists($string, '__toString')))
    throw new lmbInvalidArgumentException($custom_message, array('string' => $string), 0, 1);

  if('/' != $pattern[0] && '{' != $pattern[0] && '|' != $pattern[0])
  {
    if(false !== strpos($string, $pattern))
      return;
  }
  elseif(preg_match($pattern, $string))
    return;

  throw new lmbInvalidArgumentException($custom_message, array(), 0, 1);
}