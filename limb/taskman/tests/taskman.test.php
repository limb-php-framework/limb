<?php

@define('LIMB_VAR_DIR', dirname(__FILE__) . '/../../var');

class TaskmanTest extends UnitTestCase 
{  
  function testRunOneTask()
  {
    list($code, $out) = $this->_run("
    function task_foo() { echo 'foo'; }
    ",
    '-b foo');

    $this->assertEqual(0, $code);
    $this->assertEqual("foo", $out);
  }

  function testRunSeveralTasksFromCLI()
  {
    list($code, $out) = $this->_run("
    function task_foo() { echo 'foo'; }
    function task_bar() { echo 'bar'; }
    ",
    '-b foo,bar');

    $this->assertEqual(0, $code);
    $this->assertEqual("foobar", $out);
  }

  function testRunDependedentTask()
  {
    list($code, $out) = $this->_run("
    /**
     * @deps bar
     */
    function task_foo() { echo 'foo'; }
    function task_bar() { echo 'bar'; }
    ",
    '-b foo');

    $this->assertEqual(0, $code);
    $this->assertEqual("barfoo", $out);
  }

  function testRunInterDependedentTasks()
  {
    list($code, $out) = $this->_run("
    /**
     * @deps zoo
     */
    function task_foo() { echo 'foo'; }
    function task_bar() { echo 'bar'; }
    /**
     * @deps bar,foo,wow
     */
    function task_zoo() { echo 'zoo'; }
    function task_wow() { echo 'wow'; }
    ",
    '-b foo');

    $this->assertEqual(0, $code);
    $this->assertEqual("barwowzoofoo", $out);
  }

  function testParallTasks()
  {
    list($code, $out) = $this->_run("
    function task_foo() { sleep(1);echo 'foo'; }
    function task_bar() { sleep(2);echo 'bar'; }
    /**
     * @deps bar|foo|wow
     */
    function task_zoo() { echo 'zoo'; }
    function task_wow() { sleep(3);echo 'wow'; }
    ",
    '-b zoo');

    $this->assertEqual(0, $code);
    //$this->assertEqual("", $out);
  }

  protected function _run($contents, $cmd)
  {
    $file = LIMB_VAR_DIR . '/taskman-script.' . mt_rand() . '.php';
    file_put_contents($file, "<?php\nrequire_once('" . dirname(__FILE__) . "/../taskman.inc.php');\ntaskman_run();\n$contents");
    exec("php $file $cmd", $out, $res);
    return array($res, implode($out));
  }
}
