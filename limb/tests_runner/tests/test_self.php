<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: test_self.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
require_once(dirname(__FILE__) . '/cases/.init.php');

$group = new GroupTest();
foreach(glob(dirname(__FILE__) . '/cases/*Test.class.php') as $file)
  $group->addTestFile($file);

$group->run(new TextReporter());

?>