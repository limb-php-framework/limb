<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbConfTools.class.php 5423 2007-03-29 13:09:55Z pachanga $
 * @package    config
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/config/src/lmbCachedIni.class.php');
lmb_require('limb/config/src/lmbConf.class.php');

@define('LIMB_CONF_INCLUDE_PATH', 'settings;limb/*/settings');

class lmbConfTools extends lmbAbstractTools
{
  protected $confs = array();
  protected $map = array('.ini' => 'lmbCachedIni',
                         '.conf.php' => 'lmbConf');

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

    if(!isset($this->map[$ext]))
      throw new lmbException("'$ext' type configuration is not supported!");

    $file = $this->toolkit->findFileAlias($name, LIMB_CONF_INCLUDE_PATH, 'config');
    $this->confs[$name] = new $this->map[$ext]($file);

    return $this->confs[$name];
  }

  protected function _normalizeConfName($name)
  {
    if(strpos($name, '.') !== false)
      return $name;
    return "$name.conf.php";
  }
}
?>
