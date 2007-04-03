<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCachedIni.class.php 5423 2007-03-29 13:09:55Z pachanga $
 * @package    config
 */
lmb_require('limb/config/src/lmbIni.class.php');
lmb_require('limb/util/src/system/lmbFs.class.php');

class lmbCachedIni extends lmbIni
{
  protected $cache_dir = '';

  function __construct($file, $cache_dir = null)
  {
    $this->file_path = $file;

    if(is_null($cache_dir))
      $this->cache_dir = LIMB_VAR_DIR . '/ini/';
    else
      $this->cache_dir = $cache_dir;

    if(!$this->_loadCache())
    {
      $this->group_values = $this->_createIni($file)->getAll();
      $this->_saveCache();
    }
  }

  protected function _createIni($file)
  {
    return new lmbIni($file);
  }

  function isCacheEnabled()
  {
    return (!defined('LIMB_INI_CACHE_ENABLED') || (defined('LIMB_INI_CACHE_ENABLED') &&
             constant('LIMB_INI_CACHE_ENABLED')));
  }

  function getCacheFile()
  {
    return $this->cache_dir . md5($this->file_path) . '.cache';
  }

  protected function _loadCache()
  {
    if(!$this->isCacheEnabled())
      return false;

    $cache_dir = $this->cache_dir;

    lmbFs :: mkdir($cache_dir);

    $cache_file = $this->getCacheFile();

    if(!$this->_isCacheValid($cache_file))
      return false;

    $group_values = array();

    $this->group_values = unserialize(file_get_contents($cache_file));

    return true;
  }

  protected function _isCacheValid($cache_file)
  {
    if(!file_exists($cache_file))
      return false;

    if(filemtime($cache_file) >= $this->_getIniLastModificationTime())
      return true;
    else
      return false;
  }

  protected function _getIniLastModificationTime()
  {
    if(!$override = $this->getOverrideFile())
      return filemtime($this->file_path);

    return filemtime($override);
  }

  protected function _saveCache()
  {
    if(!is_array($this->group_values))
      return;

    lmbFs :: safeWrite($this->getCacheFile(),
                       serialize($this->group_values));
  }

  function removeCache()
  {
    if(file_exists($cache_file = $this->getCacheFile()))
      unlink($cache_file);
  }
}

?>
