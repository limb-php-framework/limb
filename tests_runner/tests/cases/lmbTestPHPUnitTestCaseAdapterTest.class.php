<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/../common.inc.php');
require_once(dirname(__FILE__) . '/../../src/lmbPHPUnitTestCase.class.php');

class lmbTestPHPUnitTestCaseAdapterTest extends lmbTestRunnerBase
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

  function testAssertEquals()
  {
  	$this->_mustBePassed('$this->assertEquals(1, 1);');
    $this->_mustBeFailed('$this->assertEquals(1, 0);');
    $this->_mustBeFailed('$this->assertEquals(1, 0, "custom message");', 'custom message');
  }

  protected function _mustBePassed($code)
  {
    $this->_testCodeInAdapter($code, true);
  }

  protected function _mustBeFailed($code, $fail_message)
  {
    $this->_testCodeInAdapter($code, false, $fail_message);
  }

  protected function _testCodeInAdapter($code, $pass, $fail_message = null)
  {
  	$test = new GeneratedTestClass();
    $test->setParentClass('lmbPHPUnitTestCase');
    $test_file = LIMB_VAR_DIR . '/' . uniqid() . '.php';

    file_put_contents($test_file, $test->generate($code));

    $group = new lmbTestGroup();
    $group->addFile($test_file);

    ob_start();
    $group->run($reporter = new TextReporter());
    $out = ob_get_clean();

    if ($pass)
    {
      $this->assertEqual(1, $reporter->getPassCount());
      $this->assertEqual(0, $reporter->getFailCount());
    }
    else
    {
    	$this->assertEqual(0, $reporter->getPassCount());
      $this->assertEqual(1, $reporter->getFailCount());
      if ($fail_message)
        $this->assertPattern('/' . $fail_message . '/', $out, 'Wrong error message');
    }
  }
}