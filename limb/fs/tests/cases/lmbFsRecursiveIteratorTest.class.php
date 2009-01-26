<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/fs/src/lmbFsRecursiveIterator.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');

class lmbFsRecursiveIteratorTest extends UnitTestCase
{
  var $dir;

  function lmbFsRecursiveIteratorTest()
  {
    $this->dir = LIMB_VAR_DIR . '/tmp/';
    parent :: UnitTestCase();
  }

  function _createFileSystem()
  {
    lmbFs :: mkdir($this->dir);
    touch($this->dir . '/a');

    lmbFs :: mkdir($this->dir . '/nested/.sub-nested/');
    touch($this->dir . '/nested/.sub-nested/d');

    lmbFs :: mkdir($this->dir . '/nested/b');
    touch($this->dir . '/nested/c');
  }

  function _removeFileSystem()
  {
    lmbFs :: rm($this->dir);
  }

  function testExceptionIterate()
  {
    $it = new lmbFsRecursiveIterator('no-such-a-dir');

    try
    {
      $it->rewind();
      $this->assertTrue(false);
    }
    catch(lmbFsException $e){}
  }

  function testSimpleIterate()
  {
    lmbFs :: rm($this->dir);
    lmbFs :: mkdir($this->dir);

    $it = new lmbFsRecursiveIterator($this->dir);

    $it->rewind();

    $this->_assertDotDir($it, __LINE__);

    $it->next();
    $this->_assertDotDir($it, __LINE__);

    $it->next();
    $this->assertFalse($it->valid());

    lmbFs :: rm($this->dir);
  }

  function testComplexIterate()
  {
    $this->_removeFileSystem();
    $this->_createFileSystem();

    $it = new lmbFsRecursiveIterator($this->dir);
    $res = array();
    foreach($it as $path)
      $res[] = $path;

    $res = array_map(array('lmbFs', 'normalizePath'), $res);
    $expected =
      array(lmbFs :: normalizePath($this->dir . '/.'),
          lmbFs :: normalizePath($this->dir . '/..'),
          lmbFs :: normalizePath($this->dir . '/a'),
          lmbFs :: normalizePath($this->dir . '/nested'),
          lmbFs :: normalizePath($this->dir . '/nested/.'),
          lmbFs :: normalizePath($this->dir . '/nested/..'),
          lmbFs :: normalizePath($this->dir . '/nested/.sub-nested'),
          lmbFs :: normalizePath($this->dir . '/nested/.sub-nested/.'),
          lmbFs :: normalizePath($this->dir . '/nested/.sub-nested/..'),
          lmbFs :: normalizePath($this->dir . '/nested/.sub-nested/d'),
          lmbFs :: normalizePath($this->dir . '/nested/b'),
          lmbFs :: normalizePath($this->dir . '/nested/b/.'),
          lmbFs :: normalizePath($this->dir . '/nested/b/..'),
          lmbFs :: normalizePath($this->dir . '/nested/c'),
          );
    sort($res);
    sort($expected);

    //make this test more bullet proof
    $this->assertEqual($res, $expected);

    $this->_removeFileSystem();
  }

  function _assertDir($it, $path, $line='')
  {
    $this->assertTrue($it->valid(), '%s ' . $line);
    $this->assertFalse($it->isDot(), '%s ' . $line);
    $this->assertTrue($it->isDir(), '%s ' . $line);
    $this->assertFalse($it->isFile(), '%s ' . $line);
    $this->assertEqual(lmbFs :: normalizePath($it->getPath()),
                       lmbFs :: normalizePath($path), '%s ' . $line);
  }

  function _assertDotDir($it, $posible_paths, $line='')
  {
    $posible_paths = array(
        lmbFs :: normalizePath($this->dir.'/.'),
        lmbFs :: normalizePath($this->dir.'/..'),
    );

    $this->assertTrue($it->valid(), '%s ' . $line);
    $this->assertTrue($it->isDir(), '%s ' . $line);
    $this->assertFalse($it->isFile(), '%s ' . $line);
    $this->assertTrue(in_array(lmbFs :: normalizePath($it->getPath()),
                       $posible_paths), '%s ' . $line);
  }

  function _assertFile($it, $path, $line='')
  {
    $this->assertTrue($it->valid(), '%s ' . $line);
    $this->assertFalse($it->isDot(), '%s ' . $line);
    $this->assertFalse($it->isDir(), '%s ' . $line);
    $this->assertTrue($it->isFile(), '%s ' . $line);
    $this->assertEqual(lmbFs :: normalizePath($it->getPath()),
                       lmbFs :: normalizePath($path), '%s ' . $line);
  }
}


