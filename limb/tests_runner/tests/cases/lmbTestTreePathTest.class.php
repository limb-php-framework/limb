<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestTreePathTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
