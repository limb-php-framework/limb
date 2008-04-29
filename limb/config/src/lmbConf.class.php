<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbObject.class.php');
lmb_require('limb/core/src/exception/lmbNoSuchPropertyException.class.php');

/**
 * class lmbConf.
 *
 * @package config
 * @version $Id: lmbConf.class.php 6977 2008-04-29 14:07:16Z korchasa $
 */
class lmbConf extends lmbObject
{
  protected $_file;

  function __construct($file)
  {
    $conf = array();
    $this->_file = $file;

    if(!include($this->_file))
      throw new lmbException("Config file '$this->_file' not found");

    if($override_file = $this->_getOverrideFile($this->_file))
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

  function get($name, $default = LIMB_UNDEFINED)
  {
    try {
      return parent::get($name, $default);
    }
    catch (lmbNoSuchPropertyException $e)
    {
      throw new lmbNoSuchPropertyException('Option ' . $name . ' not found in ' . $this->_file);
    }
  }
}

