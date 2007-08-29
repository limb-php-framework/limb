<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbSet.class.php');

/**
 * class lmbConf.
 *
 * @package config
 * @version $Id: lmbConf.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbConf extends lmbSet
{
  function __construct($file)
  {
    $conf = array();

    if(!@include($file))
      throw new lmbException("Config file '$file' not found");

    if($override_file = $this->_getOverrideFile($file))
    {
      $original = $conf;
      include($override_file);
      $conf = array_merge($original, $conf);
    }
    parent :: __construct($conf);
  }

  protected function _getOverrideFile($file_path)
  {
    $file_name = substr($file_path, 0, strpos($file_path, '.php'));
    $override_file_name = $file_name . '.override.php';

    if(file_exists($override_file_name))
      return $override_file_name;
    else
      return false;
  }
}

