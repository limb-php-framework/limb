<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
require_once(dirname(__FILE__) . '/cases/.init.php');

$group = new GroupTest();
foreach(glob(dirname(__FILE__) . '/cases/*Test.class.php') as $file)
  $group->addTestFile($file);

$group->run(new TextReporter());

?>