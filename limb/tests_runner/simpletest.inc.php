<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: common.inc.php 5049 2007-02-13 08:44:23Z pachanga $
 * @package    tests_runner
 */
@define('SIMPLE_TEST', dirname(__FILE__) . '/lib/simpletest/');

if(!file_exists(SIMPLE_TEST . '/unit_tester.php'))
{
  echo('SIMPLE_TEST constant doesn\'t point to SimpleTest installation directory(' . SIMPLE_TEST . ')');
  exit(1);
}

require_once(SIMPLE_TEST . '/unit_tester.php');
require_once(SIMPLE_TEST . '/mock_objects.php');
require_once(SIMPLE_TEST . '/reporter.php');
?>