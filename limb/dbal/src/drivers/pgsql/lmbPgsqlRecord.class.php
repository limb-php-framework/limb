<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPgsqlRecord.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */

lmb_require('limb/dbal/src/drivers/lmbDbRecord.interface.php');

class lmbPgsqlRecord implements lmbDbRecord
{
  protected $properties = array();

  function __construct($data = array())
  {
    $this->properties = $data;
  }

  function get($name)
  {
    if(isset($this->properties[$name]))
      return $this->properties[$name];
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
    $this->properties = $values;
  }

  function merge($values)
  {
    if(is_array($values) && sizeof($values))
      $this->properties = array_merge($this->properties, $values);
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
    if(is_null($value))
      return null;

    return $value == 't' ?  true : false;
  }

  function getIntegerTimeStamp($name)
  {
    $value = $this->get($name);
    if(is_integer($value))
    {
      return $value;
    }
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
    else
      return $value;
  }

  function getStringDate($name)
  {
    return $this->_getDate($name, 'Y-m-d');
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
    $value = $this->get($name);
    return is_null($value) ?  null : pg_unescape_bytea($value);
  }

  function getClob($name)
  {
    return $this->get($name);
  }

  //ArrayAccess interface
  function offsetExists($offset)
  {
    return !is_null($this->get($offset));
  }

  function offsetGet($offset)
  {
    return $this->get($offset);
  }

  function offsetSet($offset, $value){}
  function offsetUnset($offset){}

}

?>
