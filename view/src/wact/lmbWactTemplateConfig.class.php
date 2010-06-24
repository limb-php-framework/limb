<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
    return lmbToolkit :: instance()->getConf('wact')->get('tags_dirs');
  }

  function getSaxFilters()
  {
    return array();
  }
}

