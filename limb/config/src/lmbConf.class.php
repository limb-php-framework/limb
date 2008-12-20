<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
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
 * @version $Id: lmbConf.class.php 7436 2008-12-20 09:38:09Z korchasa $
 */
class lmbConf extends lmbObject
{
  protected $_files;

  function __construct($files)
  {
    if(!is_array($files))
      $files = array($files);
          
    $this->_files = $files;    
    $files = array_reverse($files);
    
    $conf = array();
    foreach ($files as $file)
    {
      $conf = $this->_attachConfFile($conf, $file);
        
      if($override_file = $this->_getOverrideFile($file))
        $conf = $this->_attachConfFile($conf, $override_file);
    }
    
    parent :: __construct($conf);
  }
  
  protected function _attachConfFile($original_conf, $file)
  {    
    if(!file_exists($file))
      throw new lmbFileNotFoundException("Config file '$file' not found");
      
    include($file);
      
    if(!is_array($conf))
      throw new lmbException("Config must be a array", array('file' => $file, 'content' => $conf));
    return lmbArrayHelper::arrayMerge($original_conf, $conf);
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
      throw new lmbNoSuchPropertyException('Option ' . $name . ' not found', array('configs' => $this->_files));
    }
  }
}

