<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: test_self.php 5050 2007-02-13 10:52:02Z pachanga $
 * @package    tests_runner
 */
require_once(dirname(__FILE__) . '/cases/.init.php');

$group = new GroupTest();
foreach(glob(dirname(__FILE__) . '/cases/*Test.class.php') as $file)
  $group->addTestFile($file);

$group->run(new TextReporter());

?>