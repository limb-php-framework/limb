<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once(dirname(__FILE__) . '/../common.inc.php');
require_once(dirname(__FILE__) . '/../src/lmbTestRunner.class.php');
require_once(dirname(__FILE__) . '/../src/lmbTestTreeFilePathNode.class.php');

$ui = new lmbTestRunner();
$res = $ui->run(new lmbTestTreeFilePathNode(dirname(__FILE__) . '/cases/'));
exit($res ? 0 : 1);

?>