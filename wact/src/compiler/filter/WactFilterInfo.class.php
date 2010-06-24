<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactFilterInfo.
 *
 * @package wact
 * @version $Id: WactFilterInfo.class.php 7686 2009-03-04 19:57:12Z korchasa $
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

