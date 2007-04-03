<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbWactTemplateConfig.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/wact/src/WactTemplateConfig.interface.php');

class lmbWactTemplateConfig implements WactTemplateConfig
{
  function getCacheDir()
  {
    return LIMB_VAR_DIR . '/compiled/';
  }

  function isForceScan()
  {
    return lmbToolkit :: instance()->getConf('wact')->get('forcescan');
  }

  function isForceCompile()
  {
    return lmbToolkit :: instance()->getConf('wact')->get('forcecompile');
  }

  function getScanDirectories()
  {
    if(!defined('LIMB_WACT_TAGS_INCLUDE_PATH'))
       throw new lmbException('LIMB_WACT_TAGS_INCLUDE_PATH constant is not defined!');

    $result = array();

    foreach(explode(';', LIMB_WACT_TAGS_INCLUDE_PATH) as $path)
      $result[] = $path;

    return $result;
  }

  function getSaxFilters()
  {
    return array();
  }
}
?>