<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFile2TestNodeMapperTest.class.php 5417 2007-03-29 11:39:33Z pachanga $
 * @package    tests_runner
 */
require_once(dirname(__FILE__) . '/../common.inc.php');
require_once(dirname(__FILE__) . '/../../src/lmbFile2TestNodeMapper.class.php');

class lmbFile2TestNodeMapperTest extends lmbTestsUtilitiesBase
{
  function setUp()
  {
    $this->_rmdir(LIMB_VAR_DIR);
    mkdir(LIMB_VAR_DIR);
  }

  function tearDown()
  {
    $this->_rmdir(LIMB_VAR_DIR);
  }

  function testSimpleMap()
  {
    mkdir(LIMB_VAR_DIR . '/a');
    touch(LIMB_VAR_DIR . '/a/foo_test.php');

    $mapper = new lmbFile2TestNodeMapper();
    $path = $mapper->map(LIMB_VAR_DIR, LIMB_VAR_DIR . '/a/foo_test.php');

    $this->assertEqual($path, '/0/0');
  }

  function testMoreComplexMap()
  {
    mkdir(LIMB_VAR_DIR . '/a');
    mkdir(LIMB_VAR_DIR . '/a/b');

    touch(LIMB_VAR_DIR . '/a/b/bar_test.php');
    touch(LIMB_VAR_DIR . '/a/b/foo_test.php');

    $mapper = new lmbFile2TestNodeMapper();
    $path = $mapper->map(LIMB_VAR_DIR, LIMB_VAR_DIR . '/a/b/foo_test.php');

    $this->assertEqual($path, '/0/0/1');
  }

  function testMapWithJunkFiles()
  {
    mkdir(LIMB_VAR_DIR . '/a');
    mkdir(LIMB_VAR_DIR . '/a/b');

    touch(LIMB_VAR_DIR . '/a/b/bar_test.php');
    touch(LIMB_VAR_DIR . '/a/b/foo_test.php');
    touch(LIMB_VAR_DIR . '/a/b/a.junk');
    touch(LIMB_VAR_DIR . '/a/b/b.junk');

    $mapper = new lmbFile2TestNodeMapper();
    $path = $mapper->map(LIMB_VAR_DIR, LIMB_VAR_DIR . '/a/b/foo_test.php');

    $this->assertEqual($path, '/0/0/1');
  }
}

?>
