<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: setup.php 5525 2007-04-04 08:03:50Z pachanga $
 * @package    web_app
 */

set_include_path(dirname(__FILE__) . PATH_SEPARATOR .
                 dirname(__FILE__) . '/lib/' . PATH_SEPARATOR);

if(file_exists(dirname(__FILE__) . '/setup.override.php'))
  require_once(dirname(__FILE__) . '/setup.override.php');

@define('LIMB_VAR_DIR', dirname(__FILE__) . '/var/');

require_once('limb/core/common.inc.php');
require_once('limb/web_app/common.inc.php');
?>