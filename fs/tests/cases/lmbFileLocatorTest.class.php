<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/fs/src/lmbFileLocations.interface.php');
lmb_require('limb/fs/src/lmbFileLocator.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');

Mock :: generate('lmbFileLocations', 'MockFileLocaions');

class lmbFileLocatorTest extends UnitTestCase
{
  function testLocateException()
  {
    $locator = new lmbFileLocator($mock = new MockFileLocations());

    $params = array('whatever');
    $mock->expectOnce('getLocations', array($params));
    $mock->setReturnValue('getLocations', array());

    try
    {
      $locator->locate('whatever', $params);
      $this->assertTrue(false);
    }
    catch(lmbFileNotFoundException $e){}
  }

  function testLocateUsingLocations()
  {
    $locator = new lmbFileLocator($mock = new MockFileLocations());

    $mock->expectOnce('getLocations');
    $mock->setReturnValue('getLocations',
                          array(dirname(__FILE__) . '/design/_en/',
                                     dirname(__FILE__) . '/design/'));

    $this->assertEqual(lmbFs :: normalizePath($locator->locate('test1.html')),
                       lmbFs :: normalizePath(dirname(__FILE__) . '/design/_en/test1.html'));
  }

  function testSkipAbsoluteAliases()
  {
    $locator = new lmbFileLocator($mock = new MockFileLocations());

    $mock->expectNever('getLocations');

    $this->assertEqual(lmbFs :: normalizePath($locator->locate(dirname(__FILE__) . '/design/_en/test1.html')),
                       lmbFs :: normalizePath(dirname(__FILE__) . '/design/_en/test1.html'));
  }

  function testLocateAll()
  {
    $locator = new lmbFileLocator($mock = new MockFileLocations());

    $mock->expectOnce('getLocations');
    $mock->setReturnValue('getLocations',
                          array(dirname(__FILE__) . '/design/',
                                dirname(__FILE__) . '/design/_en/'));


    $all_files = $locator->locateAll('*.html');
    sort($all_files);
    $this->assertEqual(lmbFs :: normalizePath($all_files[0]),
                       lmbFs :: normalizePath(dirname(__FILE__) . '/design/test1.html'));

    $this->assertEqual(lmbFs :: normalizePath($all_files[1]),
                       lmbFs :: normalizePath(dirname(__FILE__) . '/design/_en/test1.html'));
  }
}


