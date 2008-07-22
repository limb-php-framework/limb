<?php

require_once(dirname(__FILE__) . '/../../src/lmbBundler.class.php');

class lmbBundlerTest extends UnitTestCase
{
  var $fixture_dir;

  function __construct()
  {
    $this->fixture_dir = realpath(dirname(__FILE__).'/../fixture') . DIRECTORY_SEPARATOR;    
  }

  function testGetDependenciesFromFile()
  {
    $file = $this->fixture_dir . 'first-level.php';

    $deps = lmbBundler::getDependenciesFromFile($file);
    $this->assertEqual(3, count($deps));
    $this->assertTrue('./require.php', $deps[0]);
    $this->assertTrue('require_once.php', $deps[1]);
    $this->assertTrue('lmb_require.php', $deps[2]);
  }

  function testGetDependenciesFromFile_WithoutComments()
  {
    $file = $this->fixture_dir . 'with_comments.php';

    $deps = lmbBundler::getDependenciesFromFile($file);
    $this->assertEqual(0, count($deps));
  }

  function testGetNewDependencies_emptyIncludesList()
  {
    $file = $this->fixture_dir . 'second-level.php';

    $bundler = new lmbBundler($include_path = $this->fixture_dir . PATH_SEPARATOR);
    $bundler->add($file);
    $includes = $bundler->getIncludes();

    $this->assertEqual(5, count($includes));
    $this->assertTrue(strpos($includes[0], 'require.php'));
    $this->assertTrue(false !== strpos($includes[0], $this->fixture_dir));
    $this->assertTrue(strpos($includes[1], 'require_once.php'));
    $this->assertTrue(false !== strpos($includes[1], $this->fixture_dir));
    $this->assertTrue(strpos($includes[2], 'lmb_require.php'));
    $this->assertTrue(false !== strpos($includes[2], $this->fixture_dir));
    $this->assertTrue(strpos($includes[3], 'first-level.php'));
    $this->assertTrue(false !== strpos($includes[3], $this->fixture_dir));
    $this->assertTrue(strpos($includes[4], 'second-level.php'));
    $this->assertTrue(false !== strpos($includes[4], $this->fixture_dir));
  }

  function estGetNewDependencies_filledIncludesList()
  {
    $file = $this->fixture_dir . 'second-level.php';

    $bundler = new lmbBundler($include_path = $this->fixture_dir . PATH_SEPARATOR);

    $bundler->add($file);
    $includes1 = $bundler->getIncludes();

    $bundler->add($file);
    $includes2 = $bundler->getIncludes();

    $this->assertIdentical($includes1, $includes2);
  }

  function testGetNewDependencies_properlyResolveIncludePaths_findInFirstIncludePaths()
  {
    $fixture_sub_folder = $this->fixture_dir . 'subfolder/';

    $bundler = new lmbBundler($include_path = $this->fixture_dir . PATH_SEPARATOR . $fixture_sub_folder);

    $file = $this->fixture_dir . 'first-level.php';

    $bundler->add($file);
    $includes = $bundler->getIncludes();

    $this->assertEqual(4, count($includes));
    $this->assertEqual($includes[0], $this->fixture_dir . 'require.php');
    $this->assertTrue(false === strpos($includes[0], $fixture_sub_folder));
  }

  function testGetNewDependencies_properlyResolveIncludePaths_findNotInFirstIncludePath()
  {
    $fixture_sub_folder = realpath($this->fixture_dir . '/subfolder') . DIRECTORY_SEPARATOR;

    $bundler = new lmbBundler($include_path = $this->fixture_dir . PATH_SEPARATOR . $fixture_sub_folder);

    $file = $this->fixture_dir . 'sub_folder_depended.php';

    $bundler->add($file);
    $includes = $bundler->getIncludes();

    $this->assertEqual(2, count($includes));
    $this->assertEqual($includes[0], $fixture_sub_folder . 'sub_folder.php');
    $this->assertTrue(false !== strpos($includes[0], $fixture_sub_folder));
  }

  function testCleanUpFile()
  {
    $cleaned = lmbBundler::cleanUpFile($this->fixture_dir . 'first-level.php');
    $this->assertIdentical('bundler!', trim($cleaned));
  }

  function testMakeBundle()
  {
    $bundler = new lmbBundler($include_path = $this->fixture_dir . PATH_SEPARATOR);
    $bundler->add($this->fixture_dir . 'first-level.php');

    ob_start();
    $bundler->makeBundle($without_tags = true);
    $content = ob_get_contents();
    ob_end_clean();
    $this->assertIdentical(trim($content), 'hello world from bundler!');
  }
}

