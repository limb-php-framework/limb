<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbConfTools.class.php 5549 2007-04-06 07:59:52Z pachanga $
 * @package    config
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/config/src/lmbIni.class.php');
lmb_require('limb/config/src/lmbCachedIni.class.php');
lmb_require('limb/config/src/lmbConf.class.php');

@define('LIMB_CONF_INCLUDE_PATH', 'settings;limb/*/settings');

class lmbConfTools extends lmbAbstractTools
{
  protected $confs = array();

  function setConf($name, $conf)
  {
    $this->confs[$this->_normalizeConfName($name)] = $conf;
  }

  function getConf($name)
  {
    $name = $this->_normalizeConfName($name);

    if(isset($this->confs[$name]))
      return $this->confs[$name];

    $ext = substr($name, strpos($name, '.'));

    if($ext == '.ini')
    {
      $file = $this->_locateFile($name);
      if(defined('LIMB_VAR_DIR'))
        $this->confs[$name] = new lmbCachedIni($file, LIMB_VAR_DIR . '/ini/');
      else
        $this->confs[$name] = new lmbIni($file);
    }
    elseif($ext == '.conf.php')
    {
      $this->confs[$name] = new lmbConf($this->_locateFile($name));
    }
    else
      throw new lmbException("'$ext' type configuration is not supported!");

    return $this->confs[$name];
  }

  protected function _locateFile($name)
  {
    return $this->toolkit->findFileAlias($name, LIMB_CONF_INCLUDE_PATH, 'config');
  }

  protected function _normalizeConfName($name)
  {
    if(strpos($name, '.') !== false)
      return $name;
    return "$name.conf.php";
  }
}
?>
