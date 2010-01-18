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
function lmb_assert_true($value)
{
  if(!$value)
    throw new lmbInvalidArgumentException('Value must be true', array('value' => $value), 0, 1);
}

function lmb_assert_type($value, $expected_type)
{
  $given_type = gettype($value);

  if($expected_type === $given_type)
    return;

  $aliases = array(
    'bool' => 'boolean',
    'numeric' => 'integer',
    'float' => 'double',
  );
  if(isset($aliases[$expected_type]) && $aliases[$expected_type] == $given_type)
    return;

  if('array' == $expected_type && 'object' == $given_type && $value instanceof ArrayAccess)
    return;

  if('object' == $given_type && $value instanceof $expected_type)
    return;

  throw new lmbInvalidArgumentException('Value must be a '.$expected_type.' type.', array('value' => $value), 0, 1);
}

function lmb_assert_array_with_key($array, $key)
{
  if(!is_array($array) && !(is_object($array) && $array instanceof ArrayAccess))
    throw new lmbInvalidArgumentException('Given value not a array', array('value' => $array), 0, 1);

  if(!isset($array[$key]))
    throw new lmbInvalidArgumentException('Array have no key '.$key, array(), 0, 1);
}

function lmb_assert_reg_exp($string, $pattern)
{
  if(!is_string($string) && !(is_object($string) && method_exists($string, '__toString')))
    throw new lmbInvalidArgumentException('Given value not a string', array('string' => $string), 0, 1);

  if('/' != $pattern{0} && '{' != $pattern{0} && '|' != $pattern{0})
  {
    if(false !== strpos($string, $pattern))
      return;
  }
  elseif(preg_match($pattern, $string))
    return;

  throw new lmbInvalidArgumentException("Pattern '{$pattern}' not found in '{$string}'", array(), 0, 1);
}