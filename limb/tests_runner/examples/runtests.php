<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: runtests.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

require_once(dirname(__FILE__) . '/../common.inc.php');
require_once(dirname(__FILE__) . '/../src/lmbTestRunner.class.php');

$ui = new lmbTestRunner(dirname(__FILE__) . '/cases/');
exit($ui->run() ? 0 : 1);

?>