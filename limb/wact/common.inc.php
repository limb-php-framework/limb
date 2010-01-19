<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package wact
 * @version $Id: common.inc.php 8048 2010-01-19 22:12:02Z korchasa $
 */
require_once('limb/core/common.inc.php');

lmb_require('limb/wact/error.inc.php');

if(!defined('WACT_DEFAULT_CONFIG_DIR'))
  define('WACT_DEFAULT_CONFIG_DIR', 'limb/wact/config/');

if(!defined('WACT_CACHE_DIR'))
  define('WACT_CACHE_DIR', dirname(__FILE__) . '/cache/');

if(version_compare(PHP_VERSION,"5.3","<")) set_magic_quotes_runtime(0);

lmb_package_register('wact', dirname(__FILE__));
