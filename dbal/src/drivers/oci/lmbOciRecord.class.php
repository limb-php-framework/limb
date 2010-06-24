<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/drivers/lmbDbBaseRecord.class.php');

/**
 * class lmbOciRecord.
 *
 * @package dbal
 * @version $Id: lmbOciRecord.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbOciRecord extends lmbDbBaseRecord
{
  function __construct($data = array())
  {
    $this->import($data);
  }

  function get($name, $default = LIMB_UNDEFINED)
  {
    //Character encoding issue? Charset of identifiers?
    $upname = strtoupper($name);
    if(isset($this->properties[$upname]))
      return $this->properties[$upname];
    elseif(isset($this->properties[$name])) //a quick hack
      return $this->properties[$name];
    
    if(LIMB_UNDEFINED !== $default)
      return $default;
  }

  function remove($name)
  {
    $upname = strtoupper($name);
    if(isset($this->properties[$upname]))
      unset($this->properties[$upname]);
    elseif(isset($this->properties[$name]))
      unset($this->properties[$name]);
  }

  function has($name)
  {
    $upname = strtoupper($name);
    return isset($this->properties[$upname]) ||
           isset($this->properties[$name]);
  }

  function reset()
  {
    $this->properties = array();
  }

  function set($name, $value)
  {
    $this->properties[$name] = $value;
  }

  function export()
  {
    return array_change_key_case($this->properties, CASE_LOWER);
  }

  function import($values)
  {
    $this->properties = array();

    if(!is_array($values))
      return;

    foreach($values as $key => $value)
    {
      if(is_a($value, 'OCI-Lob')) //should we delay it until getter is called?
        $this->properties[$key] = $value->load();
      else
        $this->properties[$key] = $value;
    }
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
    return is_null($value) ? null : (boolean) $value;
  }

  function getBlob($name)
  {
    return $this->get($name);
  }

  function getClob($name)
  {
    return $this->get($name);
  }

  function getIntegerTimeStamp($name)
  {
  }

  function getStringDate($name)
  {
  }

  function getStringTime($name)
  {
  }

  function getStringTimeStamp($name)
  {
  }

  function getStringFixed($name)
  {
    $value = $this->get($name);
    return is_null($value) ?  null : (string) $value;
  }
}


