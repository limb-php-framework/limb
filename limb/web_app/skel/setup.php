<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: setup.php 5163 2007-02-28 08:38:30Z pachanga $
 * @package    web_app
 */
if(file_exists(dirname(__FILE__) . '/setup.override.php'))
  require_once(dirname(__FILE__) . '/setup.override.php');

set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path());

@define('LIMB_VAR_DIR', dirname(__FILE__) . '/var/');

require_once('limb/core/common.inc.php');
require_once('limb/web_app/common.inc.php');
?>