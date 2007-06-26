<?php
/*
 * Limb PHP Framework
 *
 * @link http:/limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http:/bit-creative.com)
 * @license    LGPL http:/www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/../common.inc.php');
require_once(dirname(__FILE__) . '/../../src/lmbTestRunner.class.php');

class lmbTestRunnerTest extends lmbTestRunnerBase
{
  protected $cases;

  function setUp()
  {
    $this->_rmdir(LIMB_VAR_DIR);
    mkdir(LIMB_VAR_DIR);
    //we need unique temporary dir since test modules are included once
    mkdir($this->cases = LIMB_VAR_DIR . '/' . mt_rand());
  }

  function tearDown()
  {
    $this->_rmdir(LIMB_VAR_DIR);
  }

  function testRunOkForFile()
  {
    $foo = $this->_createTestCase($this->cases . '/foo_test.php');
    $bar = $this->_createTestCase($this->cases . '/a/bar_test.php');
    $zoo = $this->_createTestCase($this->cases . '/a/z/zoo_test.php');

    $runner = new lmbTestRunner($this->cases . '/a/z/zoo_test.php');
    ob_start();
    $this->assertTrue($runner->run($found));
    ob_end_clean();
    $this->assertTrue($found);
  }

  function testRunOkForDir()
  {
    $foo = $this->_createTestCase($this->cases . '/foo_test.php');
    $bar = $this->_createTestCase($this->cases . '/a/bar_test.php');
    $zoo = $this->_createTestCase($this->cases . '/a/z/zoo_test.php');

    $runner = new lmbTestRunner($this->cases . '/a');
    ob_start();
    $this->assertTrue($runner->run($found));
    ob_end_clean();
    $this->assertTrue($found);
  }

  function testNoRunnableFilesFound()
  {
    $runner = new lmbTestRunner($this->cases . mt_rand());
    $this->assertTrue($runner->run($found));
    $this->assertFalse($found);
  }

  function testRunFailedForFile()
  {
    $foo = $this->_createTestCase($this->cases . '/foo_test.php');
    $bar = $this->_createTestCase($this->cases . '/a/bar_test.php');
    $zoo = $this->_createTestCaseFailing($this->cases . '/a/z/zoo_test.php');

    $runner = new lmbTestRunner($this->cases . '/a/z/zoo_test.php');
    ob_start();
    $this->assertFalse($runner->run($found));
    ob_end_clean();
    $this->assertTrue($found);
  }

  function testRunFailedForDir()
  {
    $foo = $this->_createTestCase($this->cases . '/foo_test.php');
    $bar = $this->_createTestCase($this->cases . '/a/bar_test.php');
    $zoo = $this->_createTestCaseFailing($this->cases . '/a/z/zoo_test.php');

    $runner = new lmbTestRunner($this->cases . '/a');
    ob_start();
    $this->assertFalse($runner->run($found));
    ob_end_clean();
    $this->assertTrue($found);
  }
}

?>
