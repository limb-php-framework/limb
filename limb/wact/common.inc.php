<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: common.inc.php 5203 2007-03-07 08:58:21Z serega $
 * @package    wact
 */
require_once(dirname(__FILE__) . '/error.inc.php');

if(!defined('WACT_DEFAULT_CONFIG_DIR'))
    define('WACT_DEFAULT_CONFIG_DIR', 'limb/wact/config/');

if(!defined('WACT_CACHE_DIR'))
    define('WACT_CACHE_DIR', dirname(__FILE__) . '/cache/');

if(!defined('WACT_STRICT_MODE'))
    define('WACT_STRICT_MODE', true);

set_magic_quotes_runtime(0);
?>