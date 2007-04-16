<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: runtests.php 5665 2007-04-16 13:12:00Z pachanga $
 * @package    tests_runner
 */

require_once(dirname(__FILE__) . '/../common.inc.php');
require_once(dirname(__FILE__) . '/../src/lmbTestRunner.class.php');

$ui = new lmbTestRunner(dirname(__FILE__) . '/cases/');
exit($ui->run() ? 0 : 1);

?>