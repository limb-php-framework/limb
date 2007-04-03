<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFileLocationsListTest.class.php 4996 2007-02-08 15:36:18Z pachanga $
 * @package    file_schema
 */
lmb_require('limb/file_schema/src/lmbFileLocations.interface.php');
lmb_require('limb/file_schema/src/lmbFileLocationsList.class.php');

Mock :: generate('lmbFileLocations', 'MockFileLocations');

class lmbFileLocationsListTest extends UnitTestCase
{
  function testGetLocations()
  {
    $mock = new MockFileLocations();
    $mock->expectOnce('getLocations');
    $mock->setReturnValue('getLocations', array('path1', 'path2'));

    $locations = new lmbFileLocationsList('path0', $mock, 'path3');

    $paths = $locations->getLocations();

    $this->assertEqual(sizeof($paths), 4);
    $this->assertPathsEqual($paths[0], 'path0');
    $this->assertPathsEqual($paths[1], 'path1');
    $this->assertPathsEqual($paths[2], 'path2');
    $this->assertPathsEqual($paths[3], 'path3');
  }

  function testGetLocationsUseArrayInConstructor()
  {
    $mock = new MockFileLocations();
    $mock->expectOnce('getLocations');
    $mock->setReturnValue('getLocations', array('path2', 'path3'));

    $locations = new lmbFileLocationsList(array('path0', 'path1', $mock));

    $paths = $locations->getLocations();

    $this->assertEqual(sizeof($paths), 4);
    $this->assertPathsEqual($paths[0], 'path0');
    $this->assertPathsEqual($paths[1], 'path1');
    $this->assertPathsEqual($paths[2], 'path2');
    $this->assertPathsEqual($paths[3], 'path3');
  }

  function testGetLocationsComplicatedTest()
  {
    $mock1 = new MockFileLocations();
    $mock1->expectOnce('getLocations');
    $mock1->setReturnValue('getLocations', array('path2', 'path3'));

    $mock2 = new MockFileLocations();
    $mock2->expectOnce('getLocations');
    $mock2->setReturnValue('getLocations', array('path4', 'path5'));

    $locations = new lmbFileLocationsList(array('path0', 'path1', $mock1), $mock2, 'path6');

    $paths = $locations->getLocations();

    $this->assertEqual(sizeof($paths), 7);
    $this->assertPathsEqual($paths[0], 'path0');
    $this->assertPathsEqual($paths[1], 'path1');
    $this->assertPathsEqual($paths[2], 'path2');
    $this->assertPathsEqual($paths[3], 'path3');
    $this->assertPathsEqual($paths[4], 'path4');
    $this->assertPathsEqual($paths[5], 'path5');
    $this->assertPathsEqual($paths[6], 'path6');
  }

  function assertPathsEqual($path1, $path2, $msg=false)
  {
    $this->assertEqual(rtrim(lmbFs :: normalizePath($path1), '/\\'),
                       rtrim(lmbFs :: normalizePath($path2), '/\\'),
                       $msg);
  }
}

?>