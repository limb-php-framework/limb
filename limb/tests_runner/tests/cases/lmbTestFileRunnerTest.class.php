<?php
/*
 * Limb PHP Framework
 *
 * @link http:/limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http:/bit-creative.com)
 * @license    LGPL http:/www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/../common.inc.php');
require_once(dirname(__FILE__) . '/../../src/lmbTestFileRunner.class.php');

class lmbTestFileRunnerTest extends lmbTestRunnerBase
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

    $runner = new lmbTestFileRunner();
    ob_start();
    $this->assertTrue($runner->runForFiles($this->cases . '/a/z/zoo_test.php'));
    ob_end_clean();
    $this->assertTrue($runner->testsFound());
  }

  function testRunOkForDir()
  {
    $foo = $this->_createTestCase($this->cases . '/foo_test.php');
    $bar = $this->_createTestCase($this->cases . '/a/bar_test.php');
    $zoo = $this->_createTestCase($this->cases . '/a/z/zoo_test.php');

    $runner = new lmbTestFileRunner();
    ob_start();
    $this->assertTrue($runner->runForFiles($this->cases . '/a'));
    ob_end_clean();
    $this->assertTrue($runner->testsFound());
  }

  function testNoRunnableFilesFound()
  {
    $runner = new lmbTestFileRunner();
    $this->assertTrue($runner->runForFiles($this->cases . mt_rand()));
    $this->assertFalse($runner->testsFound());
  }

  function testRunFailedForFile()
  {
    $foo = $this->_createTestCase($this->cases . '/foo_test.php');
    $bar = $this->_createTestCase($this->cases . '/a/bar_test.php');
    $zoo = $this->_createTestCaseFailing($this->cases . '/a/z/zoo_test.php');

    $runner = new lmbTestFileRunner();
    ob_start();
    $this->assertFalse($runner->runForFiles($this->cases . '/a/z/zoo_test.php'));
    ob_end_clean();
    $this->assertTrue($runner->testsFound());
  }

  function testRunFailedForDir()
  {
    $foo = $this->_createTestCase($this->cases . '/foo_test.php');
    $bar = $this->_createTestCase($this->cases . '/a/bar_test.php');
    $zoo = $this->_createTestCaseFailing($this->cases . '/a/z/zoo_test.php');

    $runner = new lmbTestFileRunner();
    ob_start();
    $this->assertFalse($runner->runForFiles($this->cases . '/a'));
    ob_end_clean();
    $this->assertTrue($runner->testsFound());
  }

  function testTestsInSkippedDirAreNotExecuted()
  {
    $foo = $this->_createTestCase($this->cases . '/foo_test.php');
    $bar = $this->_createTestCase($this->cases . '/a/bar_test.php');
    $zoo = $this->_createTestCase($this->cases . '/a/z/zoo_test.php');

    file_put_contents($this->cases . '/a/.skipif.php', '<?php return true; ?>');

    $runner = new lmbTestFileRunner();
    ob_start();
    $this->assertTrue($runner->runForFiles($this->cases . '/a/z/zoo_test.php'));
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertTrue($runner->testsFound());
    $this->assertNoPattern('~' . preg_quote($zoo->getOutput()) . '~', $str);
  }
}

?>
