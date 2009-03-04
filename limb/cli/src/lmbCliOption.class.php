<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/cli/src/lmbCliException.class.php');

/**
 * class lmbCliOption.
 *
 * @package cli
 * @version $Id: lmbCliOption.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class lmbCliOption
{
  const VALUE_OPT = 0;
  const VALUE_REQ = 1;
  const VALUE_NO  = 2;

  protected $short_name;
  protected $long_name;
  protected $value_mode;
  protected $value;
  protected $is_present = false;

  function __construct($short_or_long_name, $long_name = null, $value_mode = self :: VALUE_NO)
  {
    if(!is_string($long_name) && strlen($short_or_long_name) > 1)
    {
      $this->long_name = $short_or_long_name;
      $this->value_mode = !is_null($long_name) ? $long_name : self :: VALUE_NO;
    }
    elseif(!is_string($long_name) && strlen($short_or_long_name) == 1)
    {
      $this->short_name = $short_or_long_name;
      $this->value_mode = !is_null($long_name) ? $long_name : self :: VALUE_NO;
    }
    else
    {
      if(strlen($short_or_long_name) > strlen($long_name))
        throw new lmbCliException("Option short name '$short_or_long_name' longer than long name '$long_name'");

      $this->short_name = $short_or_long_name;
      $this->long_name = $long_name;
      $this->value_mode = $value_mode;
    }
  }

  function reset()
  {
    $this->value = null;
    $this->is_present = false;
  }

  function match($name)
  {
    return ($name == $this->short_name || $name == $this->long_name);
  }

  function getShortName()
  {
    return $this->short_name;
  }

  function getLongName()
  {
    return $this->long_name;
  }

  function getValueMode()
  {
    return $this->value_mode;
  }

  function isValueRequired()
  {
    return $this->value_mode === self :: VALUE_REQ;
  }

  function isValueOptional()
  {
    return $this->value_mode === self :: VALUE_OPT;
  }

  function isValueForbidden()
  {
    return $this->value_mode === self :: VALUE_NO;
  }

  function touch()
  {
    $this->is_present = true;
  }

  function isPresent()
  {
    return $this->is_present;
  }

  function setValue($value)
  {
    $this->touch();
    $this->value = $value;
  }

  function getValue()
  {
    return $this->value;
  }

  function validate()
  {
    if(empty($this->value) && $this->isValueRequired())
      throw new lmbCliException("Option '" . $this->toString() . "' is required to have a value");

    if(!empty($this->value) && $this->isValueForbidden())
      throw new lmbCliException("Option '" . $this->toString() . "' is forbidden to have a value");
  }

  function toString()
  {
    $res = array();
    if($this->short_name)
      $res[] = '-' . $this->short_name;
    if($this->long_name)
      $res[] = '--' . $this->long_name;

    return implode('|', $res);
  }
}


