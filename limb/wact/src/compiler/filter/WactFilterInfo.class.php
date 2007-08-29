<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactFilterInfo.
 *
 * @package wact
 * @version $Id: WactFilterInfo.class.php 6243 2007-08-29 11:53:10Z pachanga $
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

