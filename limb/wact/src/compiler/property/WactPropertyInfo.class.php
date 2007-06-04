<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactPropertyInfo.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
?>