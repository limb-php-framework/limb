<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestShellUITest.class.php 5006 2007-02-08 15:37:13Z pachanga $
 * @package    tests_runner
 */
require_once(dirname(__FILE__) . '/../common.inc.php');
require_once(dirname(__FILE__) . '/../../src/lmbTestShellUI.class.php');

class lmbTestShellUITest extends lmbTestsUtilitiesBase
{
  function setUp()
  {
    $this->_rmdir(LIMB_VAR_DIR);
    mkdir(LIMB_VAR_DIR);
    mkdir(LIMB_VAR_DIR . '/cases');
    $this->_createRunScript(LIMB_VAR_DIR . '/cases');
  }

  function tearDown()
  {
    $this->_rmdir(LIMB_VAR_DIR);
  }

  function testPerformInDirWithAbsolutePath()
  {
    $foo = $this->_createTestCase(LIMB_VAR_DIR . '/cases/foo_test.php');
    $bar = $this->_createTestCase(LIMB_VAR_DIR . '/cases/a/bar_test.php');
    $zoo = $this->_createTestCase(LIMB_VAR_DIR . '/cases/a/z/zoo_test.php');

    $run_dir = LIMB_VAR_DIR . '/cases';
    $ret = $this->_execScript($run_dir, $screen);
    if(!$this->assertEqual($ret, 0))
      echo $screen;

    $this->assertPattern('~1\s+of\s+3\s+done\(' . $zoo->getClass() . '\)~', $screen);
    $this->assertPattern('~2\s+of\s+3\s+done\(' . $bar->getClass() . '\)~', $screen);
    $this->assertPattern('~3\s+of\s+3\s+done\(' . $foo->getClass() . '\)~', $screen);
    $this->assertPattern('~OK~i', $screen);
    $this->assertNoPattern('~Error~i', $screen);
  }

  function testPerformInDirWithRelativePath()
  {
    $foo = $this->_createTestCase(LIMB_VAR_DIR . '/cases/foo_test.php');
    $bar = $this->_createTestCase(LIMB_VAR_DIR . '/cases/a/bar_test.php');
    $zoo = $this->_createTestCase(LIMB_VAR_DIR . '/cases/a/z/zoo_test.php');

    $cwd = getcwd();
    chdir(LIMB_VAR_DIR);
    $ret = $this->_execScript('cases', $screen);

    chdir($cwd);

    if(!$this->assertEqual($ret, 0))
      echo $screen;

    $this->assertPattern('~1\s+of\s+3\s+done\(' . $zoo->getClass() . '\)~', $screen);
    $this->assertPattern('~2\s+of\s+3\s+done\(' . $bar->getClass() . '\)~', $screen);
    $this->assertPattern('~3\s+of\s+3\s+done\(' . $foo->getClass() . '\)~', $screen);
    $this->assertPattern('~OK~i', $screen);
    $this->assertNoPattern('~Error~i', $screen);
  }

  function testPerformTestsWithGlob()
  {
    $foo = $this->_createTestCase(LIMB_VAR_DIR . '/cases/foo_test.php');
    $bar = $this->_createTestCase(LIMB_VAR_DIR . '/cases/a/bar_test.php');
    $zoo = $this->_createTestCase(LIMB_VAR_DIR . '/cases/a/z/zoo_test.php');

    $run_dir = LIMB_VAR_DIR . '/cases/*.php';
    $ret = $this->_execScript($run_dir, $screen);
    if(!$this->assertEqual($ret, 0))
      echo $screen;

    $this->assertPattern('~1\s+of\s+1\s+done\(' . $foo->getClass() . '\)~', $screen);
    $this->assertPattern('~OK~i', $screen);
    $this->assertNoPattern('~Error~i', $screen);
  }

  function _createRunScript($tests_dir)
  {
    $dir = dirname(__FILE__);
    $simpletest = SIMPLE_TEST;

    $script = <<<EOD
<?php
define('SIMPLE_TEST', '$simpletest');
define('LIMB_VAR_DIR', dirname(__FILE__) . '/var');
require_once('$dir/../../common.inc.php');
require_once('$dir/../../src/lmbTestShellUI.class.php');

\$ui = new lmbTestShellUI();
\$ui->run();
?>
EOD;
    file_put_contents($this->_runScript(), $script);
  }

  function _runScript()
  {
    return LIMB_VAR_DIR . '/runtests.php ';
  }

  function _execScript($extra, &$screen)
  {
    exec('php ' . $this->_runScript() . ' ' . $extra, $out, $ret);
    $screen = implode("\n", $out);
    return $ret;
  }

  function _createTestCase($file)
  {
    $dir = dirname($file);
    if(!is_dir($dir))
      mkdir($dir, 0777, true);

    $generated = new GeneratedTestClass();
    file_put_contents($file, $generated->generate());
    return $generated;
  }
}

?>
