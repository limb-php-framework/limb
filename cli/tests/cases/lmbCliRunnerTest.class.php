<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/fs/src/lmbFs.class.php');
lmb_require('limb/cli/src/lmbCliResponse.class.php');
lmb_require('limb/cli/src/lmbCliInput.class.php');
lmb_require('limb/cli/src/lmbCliRunner.class.php');

class lmbCliRunnerTest extends UnitTestCase
{
  var $tmp_dir;

  function setUp()
  {
    $this->tmp_dir = lmb_var_dir() . '/tmp_cmd/';
    lmbFs :: mkdir($this->tmp_dir);
  }

  function tearDown()
  {
    lmbFs :: rm($this->tmp_dir);
  }

  function testExecuteFailureNoCommand()
  {
    $input = new lmbCliInput();
    $output = new lmbCliResponse();
    $output->setVerbose(false);

    $runner = new lmbCliRunner($input, $output);
    $runner->returnOnExit();

    try
    {
      $runner->execute('/Foo.class.php');
      $this->fail();
    }
    catch(lmbException $e)
    {
      $this->pass();
    }
  }

  function testCantMapToCmdObject()
  {
    $input = new lmbCliInput();
    $output = new lmbCliResponse();
    $output->setVerbose(false);

    $input->read(array('foo.php', 'foo'));

    $runner = new lmbCliRunner($input, $output);
    $runner->returnOnExit();

    try
    {
      $runner->execute('/Bar.class.php');
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testCommandToClass()
  {
    $this->assertEqual(lmbCliRunner :: commandFileToClass('Foo.class.php'), 'Foo');
    $this->assertEqual(lmbCliRunner :: commandFileToClass('/BarBaz.class.php'), 'BarBaz');
  }

  function testPassArgvToExecute()
  {
    $input = new lmbCliInput();
    $output = new lmbCliResponse();

    $input->read(array('foo.php', '--dry-run', '-c', 'bar'));

    $runner = new lmbCliRunner($input, $output);
    $runner->returnOnExit();

    $file = $this->_createCommandClass(
      'TestPassArgvToExecute',
      'function execute() {
          echo $this->input->hasOption("dry-run") ? "Y" : "N";
          echo $this->input->getOptionValue("c");
      }'
    );

    ob_start();
    $runner->execute($file);
    $str = ob_get_contents();
    ob_end_clean();

    $this->assertEqual('Ybar', $str);
  }

  function testProcessCommandValidateResult_Positive()
  {
    $input = new lmbCliInput();
    $output = new lmbCliResponse();

    $input->read(array('foo.php', '--required_param'));

    $runner = new lmbCliRunner($input, $output);
    $runner->returnOnExit();

    $file = $this->_createCommandClass(
      'TestProcessCommandValidateResult_Positive',
      'function execute() { echo "execute"; }',
      'function validate() { echo "valid"; return true; }'
    );

    ob_start();
    $runner->execute($file);
    $str = ob_get_contents();
    ob_end_clean();

    $this->assertEqual('validexecute', $str);
  }

  function testProcessCommandValidateResult_Negative()
  {
    $input = new lmbCliInput();
    $output = new lmbCliResponse();

    $input->read(array('foo.php', '--required_param'));

    $runner = new lmbCliRunner($input, $output);
    $runner->returnOnExit();

    $file = $this->_createCommandClass(
      'TestProcessCommandValidateResult_Negative',
      'function execute() { echo "execute"; }',
      'function validate() { echo "invalid"; return false; }',
      'function help() { return "help"; }'
    );

    ob_start();
    $runner->execute($file);
    $str = ob_get_contents();
    ob_end_clean();

    $this->assertEqual('invalidhelp', $str);
  }

  function _createCommandClass(
    $class,
    $execute_body = null,
    $validate_body = null,
    $help_body = null,
    $constructor_body = null)
  {
    if(!$constructor_body)
      $constructor_body = '';

    if(!$execute_body)
      $execute_body = 'function execute() {}';

    if(!$validate_body)
      $validate_body = 'function validate() { return true; }';

    if(!$help_body)
      $help_body = 'function help() {}';

    $php = <<<EOD
<?php
class $class extends lmbCliBaseCmd
{
  $constructor_body
  $execute_body
  $validate_body
  $help_body
}
?>
EOD;
    $file = lmb_var_dir() . '/tmp_cmd/' . $class . '.class.php';
    file_put_contents($file, $php);
    return $file;
  }

  function _randomName()
  {
    return 'foo' . mt_rand();
  }
}


