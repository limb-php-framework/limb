<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactPropertyInfo.
 *
 * @package wact
 * @version $Id: WactPropertyInfo.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class WactPropertyInfo
{
  var $Property;
  var $TagClass;
  var $PropertyClass;
  var $File;

  function __construct($property, $tag_class, $class)
  {
    $this->Property = $property;
    $this->TagClass = $tag_class;
    $this->PropertyClass = $class;
  }

  function load()
  {
    if (!class_exists($this->PropertyClass) && isset($this->File))
      require_once $this->File;
  }
}

