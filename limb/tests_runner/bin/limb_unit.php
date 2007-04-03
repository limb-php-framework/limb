<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: limb_unit.php 5144 2007-02-21 09:10:50Z pachanga $
 * @package    tests_runner
 */
require_once(dirname(__FILE__) . '/../src/lmbTestShellUI.class.php');

set_time_limit(0);
error_reporting(E_ALL);

$ui = new lmbTestShellUI();
$ui->run();

?>