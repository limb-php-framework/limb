<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: index.php 5525 2007-04-04 08:03:50Z pachanga $
 * @package    web_app
 */

require_once(dirname(__FILE__) . '/../setup.php');
require_once('src/LimbApplication.class.php');

$application = new LimbApplication();
$application->process();

?>
