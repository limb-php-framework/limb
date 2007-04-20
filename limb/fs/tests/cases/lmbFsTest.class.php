<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFsTest.class.php 5518 2007-04-03 11:32:03Z pachanga $
 * @package    fs
 */
lmb_require('limb/fs/src/lmbFs.class.php');

define('TEST_DIR_ABSOLUTE_PATH', LIMB_VAR_DIR);
define('TEST_DIR_RELATIVE_PATH', 'var');

class SpecialDirWalker
{
  var $walked = array();
  var $counter = 0;

  function walk($dir, $file, $path, $params, &$return_params)
  {
    $this->walked[] = lmbFs :: normalizePath($path);
    $return_params[] = $this->counter++;
  }
}

class lmbFsTest extends UnitTestCase
{
  function _createFileSystem()
  {
    lmbFs :: mkdir(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/');

    touch(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_1');
    touch(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_2');
    touch(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_3');

    touch(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_1');
    touch(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_2');
    touch(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_3');

    touch(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_1');
    touch(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_2');
    touch(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_3');
  }

  function _removeFileSystem()
  {
    lmbFs :: rm(TEST_DIR_ABSOLUTE_PATH . '/tmp/');
  }

  //make multiprocess test someday
  function testSafeWrite()
  {
    lmbFs :: safeWrite(LIMB_VAR_DIR . '/test', 'test');
    $this->assertEqual('test',
                       file_get_contents(LIMB_VAR_DIR . '/test'));
  }

  function testJoinPath()
  {
    $path = lmbFs :: joinPath(array('wow', 'hey', 'yo'), lmbFs :: UNIX);
    $this->assertEqual($path, 'wow/hey/yo');
  }

  function testRemoveNoSuchFile()
  {
    $this->assertFalse(lmbFs :: rm('blaaaaaaaaaaaaaaaah'));
  }

  function testRemoveFile()
  {
    lmbFs :: safeWrite($file = LIMB_VAR_DIR . '/test', 'test');
    $this->assertTrue(lmbFs :: rm($file));
    $this->assertFalse(file_exists($file));
  }

  function testRemoveDirectory()
  {
    $this->_createFileSystem();

    $this->assertTrue(lmbFs :: rm(TEST_DIR_ABSOLUTE_PATH . '/tmp/'));
    $this->assertFalse(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/'));
  }

  function testIsPathAbsolute()
  {
    $this->assertTrue(lmbFs :: isPathAbsolute('c:/var/wow', lmbFs :: DOS));
    $this->assertTrue(lmbFs :: isPathAbsolute('/var/wow', lmbFs :: UNIX));
    $this->assertTrue(lmbFs :: isPathAbsolute('/var/wow', lmbFs :: DOS));
    $this->assertFalse(lmbFs :: isPathAbsolute('c:/var/wow', lmbFs :: UNIX));

    $this->assertFalse(lmbFs :: isPathAbsolute('var/wow'));
  }

  function testNormalizeUglyPath()
  {
    $path = lmbFs :: normalizePath('/tmp\../tmp/wow////hey/');
    $this->assertEqual($path, lmbFs :: separator() . 'tmp' . lmbFs :: separator() . 'wow' . lmbFs :: separator() . 'hey');

    $path = lmbFs :: normalizePath('tmp\../tmp/wow////hey/');
    $this->assertEqual($path, 'tmp' . lmbFs :: separator() . 'wow' . lmbFs :: separator() . 'hey');
  }

  function testNormalizePathForWindows()
  {
    $path = lmbFs :: normalizePath('c:\\var\\dev\\demo\\design\\templates\\test.html');

    $this->assertEqual($path,
      'c:' . lmbFs :: separator() .
      'var' . lmbFs :: separator() .
      'dev' . lmbFs :: separator() .
      'demo' . lmbFs :: separator() .
      'design' . lmbFs :: separator() .
      'templates' . lmbFs :: separator() .
      'test.html');
  }

  function testNormalizePathTrimTrailingSlashes()
  {
    $path1 = lmbFs :: normalizePath('/tmp/wow////hey/\\');
    $path2 = lmbFs :: normalizePath('/tmp\\wow//../wow/hey');
    $this->assertEqual($path1, $path2);
    $this->assertEqual($path1, lmbFs :: separator() . 'tmp' . lmbFs :: separator() . 'wow' . lmbFs :: separator() . 'hey');
  }

  function testExplodeAbsolutePath()
  {
    $path = lmbFs :: explodePath('/tmp\../tmp/wow////hey/');

    $this->assertEqual(sizeof($path), 4);

    $this->assertEqual($path[0], '');
    $this->assertEqual($path[1], 'tmp');
    $this->assertEqual($path[2], 'wow');
    $this->assertEqual($path[3], 'hey');

    $path = lmbFs :: explodePath('/tmp\../tmp/wow////hey'); // no trailing slash

    $this->assertEqual(sizeof($path), 4);

    $this->assertEqual($path[0], '');
    $this->assertEqual($path[1], 'tmp');
    $this->assertEqual($path[2], 'wow');
    $this->assertEqual($path[3], 'hey');
  }

  function testExplodeRelativePath()
  {
    $path = lmbFs :: explodePath('tmp\../tmp/wow////hey/');

    $this->assertEqual(sizeof($path), 3);

    $this->assertEqual($path[0], 'tmp');
    $this->assertEqual($path[1], 'wow');
    $this->assertEqual($path[2], 'hey');

    $path = lmbFs :: explodePath('tmp\../tmp/wow////hey'); // no trailing slash

    $this->assertEqual(sizeof($path), 3);

    $this->assertEqual($path[0], 'tmp');
    $this->assertEqual($path[1], 'wow');
    $this->assertEqual($path[2], 'hey');
  }

  function testMkdirAbsolutePath()
  {
    lmbFs :: rm(TEST_DIR_ABSOLUTE_PATH . '/tmp/');

    $this->assertFalse(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/'));

    lmbFs :: mkdir(TEST_DIR_ABSOLUTE_PATH . '/./tmp\../tmp/wow////hey/');

    $this->assertTrue(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/'));
  }

  function _testMkdirRelativePath()
  {
    $cwd = getcwd();
    chdir(LIMB_VAR_DIR);

    lmbFs :: rm(TEST_DIR_RELATIVE_PATH . '/tmp/');
    $this->assertFalse(is_dir(TEST_DIR_RELATIVE_PATH . '/tmp/wow/hey/'));

    try
    {
      lmbFs :: mkdir(TEST_DIR_RELATIVE_PATH . '/./tmp\../tmp/wow////hey/');
      $this->assertTrue(is_dir(TEST_DIR_RELATIVE_PATH . '/tmp/wow/hey/'));
    }
    catch(lmbIOException $e)
    {
      $this->assertTrue(false);
    }
    chdir($cwd);
  }

  function testDirpath()
  {
    $this->assertEqual(lmbFs :: dirpath('/wow/test.txt'), lmbFs :: normalizePath('/wow'));
    $this->assertEqual(lmbFs :: dirpath('wow/hey/test.txt'), lmbFs :: normalizePath('wow/hey'));
    $this->assertEqual(lmbFs :: dirpath('test.txt'), 'test.txt');
    $this->assertEqual(lmbFs :: dirpath('/'), '');
  }

  function testLs()
  {
    $this->_createFileSystem();

    $a1 = array('test1_1', 'test1_2', 'test1_3', 'wow');
    sort($a1);
    $a2 =  lmbFs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/');
    sort($a2);

    $this->assertEqual($a1, $a2);
    $this->assertEqual(array('hey', 'test2_1', 'test2_2', 'test2_3'), lmbFs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'));

    $this->_removeFileSystem();
  }

  function testPath()
  {
    $this->assertEqual(lmbFs :: path(array('test')), 'test');
    $this->assertEqual(lmbFs :: path(array('test', 'wow')), 'test' . lmbFs :: separator() . 'wow');
    $this->assertEqual(lmbFs :: path(array('test', 'wow/')), 'test' . lmbFs :: separator() . 'wow');

    $this->assertEqual(lmbFs :: path(array('test'), true), 'test' . lmbFs :: separator());
    $this->assertEqual(lmbFs :: path(array('test', 'wow'), true), 'test' . lmbFs :: separator() . 'wow' . lmbFs :: separator());
  }

  function testChop()
  {
    $this->assertEqual(lmbFs :: chop('test'), 'test');
    $this->assertEqual(lmbFs :: chop('test/'), 'test');
    $this->assertEqual(lmbFs :: chop('test\\'), 'test');
  }

  function testWalkDir()
  {
    $this->_createFileSystem();

    $mock = new SpecialDirWalker();

    $this->assertEqual(
      lmbFs :: walkDir(TEST_DIR_ABSOLUTE_PATH . '/tmp/',
                    array(&$mock, 'walk'),
                    array('test')),
      array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10)
    );

    sort($mock->walked);

    $this->assertEqual(sizeof($mock->walked), 11);

    $this->assertEqual($mock->walked[0], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_1'));
    $this->assertEqual($mock->walked[1], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_2'));
    $this->assertEqual($mock->walked[2], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_3'));
    $this->assertEqual($mock->walked[3], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'));
    $this->assertEqual($mock->walked[4], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey'));
    $this->assertEqual($mock->walked[5], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_1'));
    $this->assertEqual($mock->walked[6], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_2'));
    $this->assertEqual($mock->walked[7], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_3'));
    $this->assertEqual($mock->walked[8], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_1'));
    $this->assertEqual($mock->walked[9], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_2'));
    $this->assertEqual($mock->walked[10], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_3'));

    $this->_removeFileSystem();
  }

  function testWalkDirIncludeFirst()
  {
    $this->_createFileSystem();

    $mock = new SpecialDirWalker();

    $this->assertEqual(
      $res = lmbFs :: walkDir(TEST_DIR_ABSOLUTE_PATH . '/tmp/',
                     array(&$mock, 'walk'),
                     array('test'),
                     true),
      array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11)
    );

    sort($mock->walked);

    $this->assertEqual(sizeof($mock->walked), 12);

    $this->assertEqual($mock->walked[0], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp'));
    $this->assertEqual($mock->walked[1], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_1'));
    $this->assertEqual($mock->walked[2], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_2'));
    $this->assertEqual($mock->walked[3], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_3'));
    $this->assertEqual($mock->walked[4], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'));
    $this->assertEqual($mock->walked[5], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey'));
    $this->assertEqual($mock->walked[6], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_1'));
    $this->assertEqual($mock->walked[7], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_2'));
    $this->assertEqual($mock->walked[8], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_3'));
    $this->assertEqual($mock->walked[9], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_1'));
    $this->assertEqual($mock->walked[10], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_2'));
    $this->assertEqual($mock->walked[11], lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_3'));

    $this->_removeFileSystem();
  }

  function testCpDirs()
  {
    $this->_createFileSystem();

    $res = lmbFs :: cp(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow',
                       TEST_DIR_ABSOLUTE_PATH . '/tmp/cp');
    sort($res);

    $this->assertEqual(
      $res,
      array(
      'hey',
      lmbFs :: normalizePath('hey/test3_1'),
      lmbFs :: normalizePath('hey/test3_2'),
      lmbFs :: normalizePath('hey/test3_3'),
      'test2_1',
      'test2_2',
      'test2_3',
      )
    );

    $this->assertEqual(
      lmbFs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp'),
      lmbFs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'));

    $this->assertEqual(
      lmbFs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/hey'),
      lmbFs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey'));

    $this->_removeFileSystem();
  }

  function testCpAsChild()
  {
    $this->_createFileSystem();

    lmbFs :: cp(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow',
                TEST_DIR_ABSOLUTE_PATH . '/tmp/cp', null, null, true);

    $this->assertEqual(
      lmbFs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/wow/'),
      lmbFs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow'));

    $this->assertEqual(
      lmbFs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/wow/hey'),
      lmbFs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey'));

    $this->_removeFileSystem();
  }

  function testCpWithExclude()
  {
    $this->_createFileSystem();

    $res = lmbFs :: cp(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow',
                       TEST_DIR_ABSOLUTE_PATH . '/tmp/cp', '/hey/');
    sort($res);

    $this->assertEqual(
      $res,
      array('test2_1', 'test2_2', 'test2_3')
    );

    $this->assertEqual(
      $res,
      lmbFs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/')
    );

    $this->assertFalse(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/hey'));

    $this->_removeFileSystem();
  }

  function testCpWithInclude()
  {
    $this->_createFileSystem();

    $res = lmbFs :: cp(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow',
                       TEST_DIR_ABSOLUTE_PATH . '/tmp/cp', null, '/test2/');

    $this->assertEqual(
      $res,
      array('test2_1', 'test2_2', 'test2_3')
    );

    $this->assertEqual(
      $res,
      lmbFs :: ls(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/')
    );

    $this->assertFalse(is_dir(TEST_DIR_ABSOLUTE_PATH . '/tmp/cp/hey'));

    $this->_removeFileSystem();
  }

  function testCpFileIntoNonExistingFile()
  {
    $this->_createFileSystem();

    $this->assertFalse(file_exists(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_1_1'));

    $res = lmbFs :: cp(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_1',
                        TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_1_1');

    $this->assertTrue(file_exists(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_1_1'));

    $this->_removeFileSystem();
  }

  function testCpFileIntoExistingDir()
  {
    $this->_createFileSystem();

    $this->assertFalse(file_exists(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test1_1'));

    $res = lmbFs :: cp(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_1',
                        TEST_DIR_ABSOLUTE_PATH . '/tmp/wow');

    $this->assertTrue(file_exists(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test1_1'));

    $this->_removeFileSystem();
  }

  function testCpFileIntoNonExistingDir()
  {
    $this->_createFileSystem();

    $this->assertFalse(file_exists(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow2/test1_1.copy'));

    $res = lmbFs :: cp(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_1',
                        TEST_DIR_ABSOLUTE_PATH . '/tmp/wow2/test1_1.copy');

    $this->assertTrue(file_exists(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow2/test1_1.copy'));

    $this->_removeFileSystem();
  }

  function testFind()
  {
    $this->_createFileSystem();

    $res = lmbFs :: find(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey');
    sort($res);

    $this->assertEqual(
      $res,
      array(
        lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_1'),
        lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_2'),
        lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_3')
      )
    );

    $res = lmbFs :: find(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/', 'f', null, '/^test2_1$/');
    sort($res);

    $this->assertEqual(
      $res,
      array(
        lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_2'),
        lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_3'),
      )
    );

    $this->_removeFileSystem();
  }

  function testFindRecursive()
  {
    $this->_createFileSystem();

    $res = lmbFs :: findRecursive(TEST_DIR_ABSOLUTE_PATH . '/tmp/', 'fd', '~test\d_1~');
    sort($res);

    $this->assertEqual(
      $res,
      array(
        lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/test1_1'),
        lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/hey/test3_1'),
        lmbFs :: normalizePath(TEST_DIR_ABSOLUTE_PATH . '/tmp/wow/test2_1'),
      )
    );

    $this->_removeFileSystem();
  }
}

?>