<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbOciRecord.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/drivers/lmbDbRecord.interface.php');

class lmbOciRecord implements lmbDbRecord
{
  protected $properties = array();

  function __construct($data = array())
  {
    $this->import($data);
  }

  function get($name)
  {
    //Character encoding issue? Charset of identifiers?
    $upname = strtoupper($name);
    if(isset($this->properties[$upname]))
      return $this->properties[$upname];
    elseif(isset($this->properties[$name])) //a quick hack
      return $this->properties[$name];
  }

  function set($name, $value) //do we really need set here?
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
