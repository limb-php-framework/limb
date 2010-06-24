<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbObject.class.php');
lmb_require('limb/core/src/lmbArrayHelper.class.php');
lmb_require('limb/core/src/exception/lmbNoSuchPropertyException.class.php');
lmb_require('limb/fs/src/exception/lmbFileNotFoundException.class.php');

/**
 * class lmbConf.
 *
 * @package config
 * @version $Id: lmbConf.class.php 8038 2010-01-19 20:19:00Z korchasa $
 */
class lmbConf extends lmbObject
{
  protected $_file;

  function __construct($file)
  {
    $this->_file = $file;

    $conf = $this->_attachConfFile($file);

    if($override_file = $this->_getOverrideFile($file))
       $conf = $this->_attachConfFile($override_file, $conf);

    parent :: __construct($conf);
  }

  protected function _attachConfFile($file, $existed_conf = array())
  {
    $conf = $existed_conf;
    if(!file_exists($file))
      throw new lmbFileNotFoundException("Config file '$file' not found");

    include($file);

    if(!is_array($conf))
      throw new lmbException("Config must be a array", array('file' => $file, 'content' => $conf));
    return $conf;
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
    if(!$name)
      throw new lmbInvalidArgumentException('Option name not given');
    try {
      return parent::get($name, $default);
    }
    catch (lmbNoSuchPropertyException $e)
    {
      throw new lmbNoSuchPropertyException('Option ' . $name . ' not found', array('config' => $this->_file));
    }
  }
}