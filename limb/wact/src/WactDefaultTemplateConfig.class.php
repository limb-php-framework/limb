<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactDefaultTemplateConfig.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/wact/src/WactTemplateConfig.interface.php');

class WactDefaultTemplateConfig implements WactTemplateConfig
{
  protected $is_force_scan = false;
  protected $is_force_compile = false;
  protected $cache_dir = WACT_CACHE_DIR;
  protected $scan_directories = array('limb/wact/src/tags/');
  protected $sax_filters = array();
  protected $templates_dir = 'templates/';

  function __construct($ini_file_path = '')
  {
    if(!$ini_file_path)
      $ini_file_path = WACT_DEFAULT_CONFIG_DIR . '/config.ini';

    $this->_readSettingsFromConfig($ini_file_path);
  }

  function _readSettingsFromConfig($ini_file_path)
  {
    $settings = parse_ini_file($ini_file_path);
    if(isset($settings['force_scan']))
      $this->is_force_scan = (boolean)$settings['force_scan'];

    if(isset($settings['force_compile']))
      $this->is_force_compile = (boolean)$settings['force_compile'];

    if(isset($settings['cache_dir']))
      $this->cache_dir = $settings['cache_dir'];

    if(isset($settings['scan_directories']))
      $this->scan_directories = $settings['scan_directories'];

    if(isset($settings['sax_filters']))
      $this->sax_filters = $settings['sax_filters'];

    if(isset($settings['templates_dir']))
      $this->templates_dir = $settings['templates_dir'];
  }

  function isForceScan()
  {
    return $this->is_force_scan;
  }

  function isForceCompile()
  {
    return $this->is_force_compile;
  }

  function getCacheDir()
  {
    return $this->cache_dir;
  }

  function getScanDirectories()
  {
    return $this->scan_directories;
  }

  function getSaxFilters()
  {
    return $this->sax_filters;
  }

  function getTemplatesDir()
  {
    return $this->templates_dir;
  }
}

?>