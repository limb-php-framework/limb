<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class lmbIncludePathSupportTest extends UnitTestCase
{
  var $old_include_path;

  function setUp()
  {
    if(!is_dir(LIMB_VAR_DIR))
      mkdir(LIMB_VAR_DIR);

    if(!is_dir(LIMB_VAR_DIR . '/tmp'))
      mkdir(LIMB_VAR_DIR . '/tmp');

    $this->old_include_path = get_include_path();
    set_include_path(LIMB_VAR_DIR . '/tmp' . PATH_SEPARATOR . get_include_path());
  }

  function tearDown()
  {
    $this->rm_dir(LIMB_VAR_DIR . '/tmp');
    set_include_path($this->old_include_path);
  }

  function testResolveIncludePathFileFailed()
  {
    $_ = $this->_rnd();
    $this->assertNull(lmb_resolve_include_path("{$_}bar.inc.php"));
  }

  function testResolveIncludePathFile()
  {
    $_ = $this->_rnd();
    file_put_contents(LIMB_VAR_DIR . "/tmp/{$_}foo.inc.php", "foo");

    $resolved = lmb_resolve_include_path("{$_}foo.inc.php");

    $this->assertEqual(file_get_contents($resolved), "foo");
  }

  function testResolveIncludePathDir()
  {
    $_ = $this->_rnd();
    mkdir(LIMB_VAR_DIR . '/tmp/' . $_);

    $resolved = lmb_resolve_include_path($_);
    $this->assertEqual($resolved, LIMB_VAR_DIR . '/tmp/' . $_);

    rmdir(LIMB_VAR_DIR . '/tmp/' . $_);
  }

  function testGlobFailedForRelativePath()
  {
    $_ = $this->_rnd();
    $files = lmb_glob("{$_}*.inc.php");
    $this->assertEqual($files, array());
  }

  function testGlobFailedForAbsolutePath()
  {
    $_ = $this->_rnd();
    $files = lmb_glob(LIMB_VAR_DIR . "/tmp/{$_}*.inc.php");
    $this->assertEqual($files, array());
  }

  function testGlobForRelativePath()
  {
    $_ = $this->_rnd();

    file_put_contents(LIMB_VAR_DIR . "/tmp/{$_}bar.inc.php", "bar");
    file_put_contents(LIMB_VAR_DIR . "/tmp/{$_}foo.inc.php", "foo");
    file_put_contents(LIMB_VAR_DIR . "/tmp/{$_}zoo.inc.php", "zoo");

    $files = lmb_glob("{$_}*.inc.php");

    sort($files);

    $this->assertEqual(sizeof($files), 3);
    $this->assertEqual(file_get_contents($files[0]), "bar");
    $this->assertEqual(file_get_contents($files[1]), "foo");
    $this->assertEqual(file_get_contents($files[2]), "zoo");
  }

  function testGlobForAbsolutePath()
  {
    $_ = $this->_rnd();

    file_put_contents(LIMB_VAR_DIR . "/tmp/{$_}bar.inc.php", "bar");
    file_put_contents(LIMB_VAR_DIR . "/tmp/{$_}foo.inc.php", "foo");
    file_put_contents(LIMB_VAR_DIR . "/tmp/{$_}zoo.inc.php", "zoo");

    $files = lmb_glob(LIMB_VAR_DIR . "/tmp/{$_}*.inc.php");

    sort($files);

    $this->assertEqual(sizeof($files), 3);
    $this->assertEqual(file_get_contents($files[0]), "bar");
    $this->assertEqual(file_get_contents($files[1]), "foo");
    $this->assertEqual(file_get_contents($files[2]), "zoo");
  }

  function _rnd()
  {
    return mt_rand(1, 1000) . uniqid();
  }

  function rm_dir($path)
  {
    $dir = opendir($path);
    while($entry = readdir($dir))
    {
     if(is_file("$path/$entry"))
     {
       unlink("$path/$entry");
     }
     elseif(is_dir("$path/$entry") && $entry != '.' && $entry != '..')
     {
       $this->rm_dir("$path/$entry");
     }
    }

    closedir($dir);
    return rmdir($path);
  }
}

