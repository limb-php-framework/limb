<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/config/src/lmbIni.class.php');
lmb_require('limb/config/src/lmbCachedIni.class.php');
lmb_require('limb/config/src/lmbConf.class.php');

lmb_env_setor('LIMB_CONF_INCLUDE_PATH', 'settings;limb/*/settings');

/**
 * class lmbConfTools.
 *
 * @package config
 * @version $Id: lmbConfTools.class.php 7676 2009-03-03 22:37:28Z korchasa $
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
      $this->conf_include_path = lmb_env_get('LIMB_CONF_INCLUDE_PATH');
    return $this->conf_include_path;
  }
  
  protected function _locateFiles($name, $find_all = false)
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
      $file = $this->_locateFiles($name);
      if(lmb_env_has('LIMB_VAR_DIR'))
        $this->confs[$name] = new lmbCachedIni($file, lmb_env_get('LIMB_VAR_DIR') . '/ini/');
      else
        $this->confs[$name] = new lmbIni($file);
    }
    elseif($ext == '.conf.php')
    {
      $file = $this->_locateFiles($name);
      if(!count($file))
        throw new lmbFileNotFoundException($name);
        
      $this->confs[$name] = new lmbConf(lmbFs::normalizePath($file));
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

