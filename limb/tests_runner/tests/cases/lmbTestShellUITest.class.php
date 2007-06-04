<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestShellUITest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
    $this->_createRunScript();
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

  function testPerformMultipleArgs()
  {
    $foo = $this->_createTestCase($f1 = LIMB_VAR_DIR . '/cases/foo_test.php');
    $bar = $this->_createTestCase($f2 = LIMB_VAR_DIR . '/cases/a/bar_test.php');
    $zoo = $this->_createTestCase($f3 = LIMB_VAR_DIR . '/cases/a/z/zoo_test.php');

    $ret = $this->_execScript("$f2 $f1 $f3", $screen);
    if(!$this->assertEqual($ret, 0))
      echo $screen;

    $this->assertPattern('~1\s+of\s+1\s+done\(' . $foo->getClass() . '\)~', $screen);
    $this->assertPattern('~1\s+of\s+1\s+done\(' . $bar->getClass() . '\)~', $screen);
    $this->assertPattern('~1\s+of\s+1\s+done\(' . $zoo->getClass() . '\)~', $screen);
    $this->assertPattern('~(Test cases run:\s*1\/1.*){3}~si', $screen);
    $this->assertNoPattern('~Error~i', $screen);
  }

  function testAutoDefineConstants()
  {
    $c1 = "FOO_" . mt_rand();
    $c2 = "FOO_" . mt_rand();

    $this->_createTestCase($f = LIMB_VAR_DIR . '/cases/foo_test.php', "echo '$c1=' . $c1;echo '$c2=' . $c2;");
    $this->_execScript("$f $c1=hey $c2=wow", $screen);

    $this->assertPattern("~$c1=hey~", $screen);
    $this->assertPattern("~$c2=wow~", $screen);
  }

  function _createRunScript()
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
    file_put_contents($this->_runScriptName(), $script);
  }

  function _runScriptName()
  {
    return LIMB_VAR_DIR . '/runtests.php';
  }

  function _execScript($args, &$screen)
  {
    exec('php ' . $this->_runScriptName() . ' ' . $args, $out, $ret);
    $screen = implode("\n", $out);
    return $ret;
  }

  function _createTestCase($file, $extra = '')
  {
    $dir = dirname($file);
    if(!is_dir($dir))
      mkdir($dir, 0777, true);

    $generated = new GeneratedTestClass();
    file_put_contents($file, "<?php\n" . $generated->generate(false) . $extra . "\n?>");
    return $generated;
  }
}

?>
