<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: runtests.php 5531 2007-04-05 09:55:30Z pachanga $
 * @package    tests_runner
 */

require_once(dirname(__FILE__) . '/../common.inc.php');
require_once(dirname(__FILE__) . '/../src/lmbTestShellUI.class.php');

$ui = new lmbTestShellUI(dirname(__FILE__) . '/cases/');
$ui->run();

?>