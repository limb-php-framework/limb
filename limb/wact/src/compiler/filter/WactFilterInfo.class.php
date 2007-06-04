<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactFilterInfo.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

class WactFilterInfo
{
  var $Name = 'capitalize';
  var $FilterClass = 'CapitalizeFilter';
  var $MinParameterCount = 0;
  var $MaxParameterCount = 0;
  var $File;

  function __construct($Name, $FilterClass, $MinParameterCount = 0, $MaxParameterCount = 0)
  {
    $this->Name = $Name;
    $this->FilterClass = $FilterClass;
    $this->MinParameterCount = $MinParameterCount;
    $this->MaxParameterCount = $MaxParameterCount;
  }

  function load()
  {
    if (!class_exists($this->FilterClass) && isset($this->File))
        require_once $this->File;
  }
}
?>