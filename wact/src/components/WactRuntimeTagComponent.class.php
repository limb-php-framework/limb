<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Base class for runtime components that output XML tags
 * @package wact
 * @version $Id: WactRuntimeTagComponent.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactRuntimeTagComponent extends WactRuntimeComponent
{
  protected $attributes = array();

  function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }

  function setAttribute($attrib, $value)
  {
    $attrib = $this->_getCanonicalAttributeName($attrib);
    $this->attributes[$attrib] = $value;
  }

  function getAttribute($attrib)
  {
    $attrib = $this->_getCanonicalAttributeName($attrib);
    if (isset($this->attributes[$attrib]))
      return $this->attributes[$attrib];
  }

  function getBoolAttribute($attrib, $default = FALSE)
  {
    if (!isset($this->attributes[strtolower($attrib)]))
      return $default;

    return self :: getBooleanValue($this->attributes[strtolower($attrib)]);
  }

  static function getBooleanValue($value)
  {
    if(is_bool($value))
      return $value;

    if(is_string($value))
    {

      switch(strtoupper($value))
      {
        case false:
        case 'FALSE':
        case 'N':
        case 'NO':
        case 'NONE':
        case 'NA':
        case '0':
          return false;
        default:
          return true;
      }
    }
  }

  function removeAttribute($attrib)
  {
    $attrib = $this->_getCanonicalAttributeName($attrib);
    unset($this->attributes[$attrib]);
  }

  function hasAttribute($attrib)
  {
    $attrib = $this->_getCanonicalAttributeName($attrib);
    return array_key_exists($attrib, $this->attributes);
  }

  /**
  * Writes the contents of the attributes to the screen, using
  * htmlspecialchars to convert entities in values. Called by
  * a compiled template
  */
  function renderAttributes()
  {
    foreach ($this->attributes as $name => $value)
    {
      echo ' ';
      echo $name;
      if (!is_null($value))
      {
        echo '="';
        echo htmlspecialchars($value, ENT_QUOTES);
        echo '"';
      }
    }
  }

  protected function _getCanonicalAttributeName($attrib)
  {
    // quick check if they happen to use the same case.
    if (array_key_exists($attrib, $this->attributes))
      return $attrib;

    // slow check
    foreach(array_keys($this->attributes) as $key)
    {
      if (strcasecmp($attrib, $key) == 0)
        return $key;
    }

    return $attrib;
  }
}

