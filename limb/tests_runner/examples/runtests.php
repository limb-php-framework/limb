<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: runtests.php 5049 2007-02-13 08:44:23Z pachanga $
 * @package    tests_runner
 */

//temporary files folder
define('LIMB_VAR_DIR', dirname(__FILE__) . '/var');

require_once(dirname(__FILE__) . '/../common.inc.php');
require_once(dirname(__FILE__) . '/../src/lmbTestShellUI.class.php');
require_once(dirname(__FILE__) . '/../src/lmbTestWebUI.class.php');
require_once(dirname(__FILE__) . '/../src/lmbTestTreeDirNode.class.php');

$node = new lmbTestTreeDirNode(dirname(__FILE__) . '/cases/');

if(PHP_SAPI == 'cli')
  $ui = new lmbTestShellUI($node);
else
  $ui = new lmbTestWebUI($node);

$ui->run();

?>