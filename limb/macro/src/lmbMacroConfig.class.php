<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMacroConfig.
 *
 * @package macro
 * @version $Id$
 */

lmb_require('limb/core/src/lmbObject.class.php');

class lmbMacroConfig extends lmbObject
{
  public $cache_dir;
  public $is_force_compile;
  public $is_force_scan;
  public $tpl_scan_dirs;
  public $tags_scan_dirs;
  public $filters_scan_dirs;
  function __construct($cache_dir = null, $is_force_compile = true, $is_force_scan = true,
                       $tpl_scan_dirs = array(), $tags_scan_dirs = array(), $filters_scan_dirs = array())
  {    
    $params['cache_dir'] = $cache_dir;
    $params['is_force_compile'] = $is_force_compile;
    $params['is_force_scan'] = $is_force_scan;
    $params['tpl_scan_dirs'] = $tpl_scan_dirs;
    $params['tags_scan_dirs'] = $tags_scan_dirs;
    $params['filters_scan_dirs'] = $filters_scan_dirs;
    parent::__construct($params);
  }
}

