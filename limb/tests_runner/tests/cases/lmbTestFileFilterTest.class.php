<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestFileFilterTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
require_once(dirname(__FILE__) . '/../../src/lmbTestFileFilter.class.php');

class lmbTestFileFilterTest extends UnitTestCase
{
  function testOneFilterMatch()
  {
    $filter = new lmbTestFileFilter(array('*Test'));
    $this->assertTrue($filter->match('/wow/hey/myTest'));
  }

  function testSeveralFiltersMatch()
  {
    $filter = new lmbTestFileFilter(array('*Test', '*yo'));
    $this->assertTrue($filter->match('/wow/hey/yo'));
  }

  function testNoMatch()
  {
    $filter = new lmbTestFileFilter(array('*Test'));
    $this->assertFalse($filter->match('/wow/hey/wow'));
  }

  function testMatchBasenameOnly()
  {
    $filter = new lmbTestFileFilter(array('*Test'));
    $this->assertFalse($filter->match('/wow/heyTest/wow'));
    $this->assertTrue($filter->match('/wow/foo/heyTest'));
  }
}


?>
