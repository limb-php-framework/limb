<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestShellReporter.class.php 5006 2007-02-08 15:37:13Z pachanga $
 * @package    tests_runner
 */

class lmbTestShellReporter extends TextReporter
{
  function paintCaseEnd($test_name)
  {
    parent :: paintCaseEnd($test_name);

    echo $this->getTestCaseProgress() . " of " . $this->getTestCaseCount() . " done({$test_name})\n";
  }
}
?>