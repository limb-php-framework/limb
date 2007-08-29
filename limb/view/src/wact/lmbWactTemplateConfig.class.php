<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/wact/src/WactTemplateConfig.interface.php');

/**
 * class lmbWactTemplateConfig.
 *
 * @package view
 * @version $Id$
 */
class lmbWactTemplateConfig implements WactTemplateConfig
{
  function __construct($cache_dir)
  {
    $this->cache_dir = $cache_dir;
  }

  function getCacheDir()
  {
    return $this->cache_dir;
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

