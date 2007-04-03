<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestTreePathTest.class.php 5006 2007-02-08 15:37:13Z pachanga $
 * @package    tests_runner
 */
require_once(dirname(__FILE__) . '/../../src/lmbTestTreePath.class.php');

class lmbTestTreePathTest extends UnitTestCase
{
  function testToArray()
  {
    $this->assertEqual(lmbTestTreePath :: toArray('/0/1'), array('0', '1'));
    $this->assertEqual(lmbTestTreePath :: toArray('/0/1/'), array('0', '1'));
    $this->assertEqual(lmbTestTreePath :: toArray('/0/1/../'), array('0'));
  }

  function testNormalize()
  {
    $this->assertEqual(lmbTestTreePath :: normalize('/0////'), '/0');
    $this->assertEqual(lmbTestTreePath :: normalize('/0/1/../'), '/0');
  }
}


?>
