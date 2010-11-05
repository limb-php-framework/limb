<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/drivers/lmbDbBaseRecord.class.php');

/**
 * class lmbMssqlRecord.
 *
 * @package dbal
 * @version $Id: lmbMssqlRecord.class.php,v 1.1.1.1 2009/06/08 11:57:21 mike Exp $
 */
class lmbMssqlRecord extends lmbDbBaseRecord
{
  protected $properties = array();

  function __construct($data = array())
  {
    $this->properties = $data;
  }

  function get($name, $default = LIMB_UNDEFINED)
  {
    if(isset($this->properties[$name]))
      return $this->properties[$name];

    if(LIMB_UNDEFINED !== $default)
      return $default;
  }

  function set($name, $value)
  {
    $this->properties[$name] = $value;
  }

  function export()
  {
    return $this->properties;
  }

  function import($values)
  {
    if (is_array($values) || is_object($values))
    {
      foreach ($values as $key => $value)
      {
        if (is_numeric($value))
        {
          $this->properties[$key] = $value;
        }
        elseif (is_null($value))
        {
          $this->properies[$key] = null;
        }
        else
        {
          $this->properties[$key] = mb_convert_encoding($value, 'UTF-8', 'Windows-1251');
        }
      }
    }
    else
    {
      $this->properties = $values;
    }
  }

  function remove($name)
  {
    if(isset($this->properties[$name]))
      unset($this->properties[$name]);
  }

  function has($name)
  {
    return isset($this->properties[$name]);
  }

  function reset()
  {
    $this->properties = array();
  }

  function getBit($name)
  {
    $value = $this->get($name);
    return is_null($value) ?  null : bindec($value);
  }

  function getInteger($name)
  {
    $value = $this->get($name);
    return is_null($value) ?  null : (int) $value;
  }

  function getFloat($name)
  {
    $value = $this->get($name);
    return is_null($value) ?  null : (float) $value;
  }

  function getString($name)
  {
    $value = $this->get($name);
    return is_null($value) ?  null : (string) $value;
  }

  function getBoolean($name)
  {
    $value = $this->get($name);
    return is_null($value) ?  null : (boolean) $value;
  }

  function getIntegerTimeStamp($name)
  {
    $value = $this->get($name);
    if(is_integer($value))
      return $value;
    else if(is_string($value))
    {
      $ts = strtotime($value);
      if($ts === -1)
      {
        if(preg_match('/([\d]{4})([\d]{2})([\d]{2})([\d]{2})([\d]{2})([\d]{2})/', $value, $matches))
          return mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
      }
      else
        return $ts;
    }
  }

  function _getDate($name, $format)
  {
    $value = $this->get($name);
    if(is_integer($value))
      return date($format, $value);
    elseif (is_null($value))
      return null;
    elseif ($ts = strtotime($value))
      return date($format, $ts);
    else    
      return date($format, strtotime(substr($value, -8)));
  }

  function getStringDate($name)
  {
    return $this->_getDate($name, 'Y-m-d');
  }
  
  function getDate($name)
  {
    return $this->getStringDate($name);
  }
  
  function getTime($name)
  {
    return $this->getStringTime($name);
  }

  function getStringTime($name)
  {
    return $this->_getDate($name, 'H:i:s');
  }

  function getStringTimeStamp($name)
  {
    return $this->_getDate($name, 'Y-m-d H:i:s');
  }

  function getStringFixed($name)
  {
    $value = $this->get($name);
    return is_null($value) ?  null : (string) $value;
  }

  function getBlob($name)
  {
    return $this->get($name);
  }

  function getClob($name)
  {
    return $this->get($name);
  }
  
  function cleanup()
  {
    foreach ($this->properties as $key => $value)
    {
      unset($this->properties[$key]);
      unset($key);
      unset($value);
    }
    unset($this->properties);
  }
}


