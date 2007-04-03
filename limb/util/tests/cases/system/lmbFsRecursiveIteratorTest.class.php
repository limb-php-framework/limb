<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFsRecursiveIteratorTest.class.php 5177 2007-03-02 16:01:30Z pachanga $
 * @package    util
 */
lmb_require('limb/util/src/system/lmbFsRecursiveIterator.class.php');
lmb_require('limb/util/src/system/lmbFs.class.php');

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
    catch(lmbIOException $e){}
  }

  function testSimpleIterate()
  {
    lmbFs :: rm($this->dir);
    lmbFs :: mkdir($this->dir);

    $it = new lmbFsRecursiveIterator($this->dir);

    $it->rewind();
    $this->_assertDotDir($it, $this->dir . '/.', __LINE__);

    $it->next();
    $this->_assertDotDir($it, $this->dir . '/..', __LINE__);

    $it->next();
    $this->assertFalse($it->valid());

    lmbFs :: rm($this->dir);
  }

  function testComplexIterate()
  {
    $this->_removeFileSystem();
    $this->_createFileSystem();

    $it = new lmbFsRecursiveIterator($this->dir);

    $it->rewind();
    $this->_assertDotDir($it, $this->dir . '/.', __LINE__);

    $it->next();
    $this->_assertDotDir($it, $this->dir . '/..', __LINE__);

    $it->next();
    $this->_assertFile($it, $this->dir . '/a', __LINE__);

    $it->next();
    $this->_assertDir($it, $this->dir . '/nested', __LINE__);

    $it->next();
    $this->_assertDotDir($it, $this->dir . '/nested/.', __LINE__);

    $it->next();
    $this->_assertDotDir($it, $this->dir . '/nested/..', __LINE__);

    if(lmbSys :: isWin32())
    {
      $it->next();
      $this->_assertDir($it, $this->dir . '/nested/.sub-nested', __LINE__);

      $it->next();
      $this->_assertDotDir($it, $this->dir . '/nested/.sub-nested/.', __LINE__);

      $it->next();
      $this->_assertDotDir($it, $this->dir . '/nested/.sub-nested/..', __LINE__);

      $it->next();
      $this->_assertFile($it, $this->dir . '/nested/.sub-nested/d', __LINE__);

      $it->next();
      $this->_assertDir($it, $this->dir . '/nested/b', __LINE__);

      $it->next();
      $this->_assertDotDir($it, $this->dir . '/nested/b/.', __LINE__);

      $it->next();
      $this->_assertDotDir($it, $this->dir . '/nested/b/..', __LINE__);

      $it->next();
      $this->_assertFile($it, $this->dir . '/nested/c', __LINE__);
    }
    else
    {
      $it->next();
      $this->_assertDir($it, $this->dir . '/nested/b', __LINE__);

      $it->next();
      $this->_assertDotDir($it, $this->dir . '/nested/b/.', __LINE__);

      $it->next();
      $this->_assertDotDir($it, $this->dir . '/nested/b/..', __LINE__);

      $it->next();
      $this->_assertFile($it, $this->dir . '/nested/c', __LINE__);

      $it->next();
      $this->_assertDir($it, $this->dir . '/nested/.sub-nested', __LINE__);

      $it->next();
      $this->_assertDotDir($it, $this->dir . '/nested/.sub-nested/.', __LINE__);

      $it->next();
      $this->_assertDotDir($it, $this->dir . '/nested/.sub-nested/..', __LINE__);

      $it->next();
      $this->_assertFile($it, $this->dir . '/nested/.sub-nested/d', __LINE__);
    }

    $it->next();
    $this->assertFalse($it->valid());

    $this->_removeFileSystem();
  }

  function testLoop()
  {
    $this->_removeFileSystem();
    $this->_createFileSystem();

    $it = new lmbFsRecursiveIterator($this->dir);
    $res = array();
    for($it->rewind(); $it->valid(); $it->next())
    {
      $res[] = $it->getPathName();
    }

    $res = array_map(array('lmbFs', 'normalizePath'), $res);

    if(lmbSys :: isWin32())
    {
      $this->assertEqual($res,
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
                         ));
    }
    else
    {
      $this->assertEqual($res,
                         array(lmbFs :: normalizePath($this->dir . '/.'),
                               lmbFs :: normalizePath($this->dir . '/..'),
                               lmbFs :: normalizePath($this->dir . '/a'),
                               lmbFs :: normalizePath($this->dir . '/nested'),
                               lmbFs :: normalizePath($this->dir . '/nested/.'),
                               lmbFs :: normalizePath($this->dir . '/nested/..'),
                               lmbFs :: normalizePath($this->dir . '/nested/b'),
                               lmbFs :: normalizePath($this->dir . '/nested/b/.'),
                               lmbFs :: normalizePath($this->dir . '/nested/b/..'),
                               lmbFs :: normalizePath($this->dir . '/nested/c'),
                               lmbFs :: normalizePath($this->dir . '/nested/.sub-nested'),
                               lmbFs :: normalizePath($this->dir . '/nested/.sub-nested/.'),
                               lmbFs :: normalizePath($this->dir . '/nested/.sub-nested/..'),
                               lmbFs :: normalizePath($this->dir . '/nested/.sub-nested/d'),
                         ));
    }

    $this->_removeFileSystem();
  }

  function _assertDir($it, $path, $line='')
  {
    $this->assertTrue($it->valid(), '%s ' . $line);
    $this->assertFalse($it->isDot(), '%s ' . $line);
    $this->assertTrue($it->isDir(), '%s ' . $line);
    $this->assertFalse($it->isFile(), '%s ' . $line);
    $this->assertEqual(lmbFs :: normalizePath($it->getPathName()),
                       lmbFs :: normalizePath($path), '%s ' . $line);
  }

  function _assertDotDir($it, $path, $line='')
  {
    $this->assertTrue($it->valid(), '%s ' . $line);
    $this->assertTrue($it->isDot(), '%s ' . $line);
    $this->assertTrue($it->isDir(), '%s ' . $line);
    $this->assertFalse($it->isFile(), '%s ' . $line);
    $this->assertEqual(lmbFs :: normalizePath($it->getPathName()),
                       lmbFs :: normalizePath($path), '%s ' . $line);
  }

  function _assertFile($it, $path, $line='')
  {
    $this->assertTrue($it->valid(), '%s ' . $line);
    $this->assertFalse($it->isDot(), '%s ' . $line);
    $this->assertFalse($it->isDir(), '%s ' . $line);
    $this->assertTrue($it->isFile(), '%s ' . $line);
    $this->assertEqual(lmbFs :: normalizePath($it->getPathName()),
                       lmbFs :: normalizePath($path), '%s ' . $line);
  }
}

?>