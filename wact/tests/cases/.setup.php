<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/core/common.inc.php');
require_once('limb/core/tests/cases/init.inc.php');
lmb_tests_init_var_dir(dirname(__FILE__) . '/../../../var/wact');
define('WACT_CACHE_DIR', lmb_var_dir().'/cache');

set_include_path(dirname(__FILE__) . '/../../../' . PATH_SEPARATOR . get_include_path());
require_once(dirname(__FILE__) . '/../../common.inc.php');
require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');
