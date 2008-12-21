<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/config/src/lmbIni.class.php');
lmb_require('limb/config/src/lmbCachedIni.class.php');
lmb_require('limb/config/src/lmbConf.class.php');

@define('LIMB_CONF_INCLUDE_PATH', 'settings;limb/*/settings');

/**
 * class lmbConfTools.
 *
 * @package config
 * @version $Id: lmbConfTools.class.php 7441 2008-12-21 17:37:22Z korchasa $
 */
class lmbConfTools extends lmbAbstractTools
{
  protected $confs = array();
  protected $conf_include_path;

  function setConf($name, $conf)
  {
    $this->confs[$this->_normalizeConfName($name)] = $conf;
  }

  function hasConf($name)
  {
    try {
      $this->toolkit->getConf($name);
      return true;
    }
    catch (lmbFileNotFoundException $e)
    {
      return false;
    }
  }  
  
  function setConfIncludePath($path)
  {
    $this->conf_include_path = $path;
  }
  
  function getConfIncludePath()
  {
    if(!$this->conf_include_path)
      $this->conf_include_path = LIMB_CONF_INCLUDE_PATH;
      
    return $this->conf_include_path;
  }
  
  protected function _locateFiles($name, $find_all = true)
  {
    return $this->toolkit->findFileByAlias($name, $this->toolkit->getConfIncludePath(), 'config', $find_all);
  }
  
  function getConf($name)
  {
    $name = $this->_normalizeConfName($name);

    if(isset($this->confs[$name]))
      return $this->confs[$name];

    $ext = substr($name, strpos($name, '.'));

    if($ext == '.ini')
    {
      $file = $this->_locateFile($name, $find_all = false);
      if(defined('LIMB_VAR_DIR'))
        $this->confs[$name] = new lmbCachedIni($file, LIMB_VAR_DIR . '/ini/');
      else
        $this->confs[$name] = new lmbIni($file);
    }
    elseif($ext == '.conf.php')
    {
      $files = $this->_locateFiles($name);      
      if(!count($files))
        throw new lmbFileNotFoundException($name);
        
      $this->confs[$name] = new lmbConf($files);
    }
    else
      throw new lmbException("'$ext' type configuration is not supported!");

    return $this->confs[$name];
  }  

  protected function _normalizeConfName($name)
  {
    if(strpos($name, '.') !== false)
      return $name;
    return "$name.conf.php";
  }
}

