<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMacroFilterInfo.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroFilterInfo
{
  protected $name = '';
  protected $class = '';
  protected $file;
  protected $aliases = array();

  function __construct($name, $class)
  {
    $this->name = $name;
    $this->class = $class;
  }

  static function createByAnnotations($file, $class, $annotations)
  {
    if(!isset($annotations['filter']))
      throw new lmbMacroException("@filter annotation is missing for class '$class'");

    $filter = $annotations['filter'];
    $info = new lmbMacroFilterInfo($filter, $class);

    if(isset($annotations['aliases']))
    {
      $filter_aliases = explode(',' , $annotations['aliases']);
      $filter_aliases = array_map('trim', $filter_aliases);
      $info->setAliases($filter_aliases);
    }
    
    $info->setFile($file);

    return $info;
  }

  function load()
  {
    if (!class_exists($this->class) && isset($this->file))
        require_once $this->file;
  }

  function getName()
  {
    return $this->name;
  }

  function getClass()
  {
    return $this->class;
  }

  function setFile($file)
  {
    $this->file = $file;
  }

  function getFile()
  {
    return $this->file;
  }
  
  function setAliases($aliases)
  {
    $this->aliases = $aliases;
  }
  
  function getAliases()
  {
    return $this->aliases;
  }
}

