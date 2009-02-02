<?php

@define('LIMB_VAR_DIR', dirname(__FILE__) . '/../../var');

class TaskmanTest extends UnitTestCase 
{  
  function tearDown()
  {
    foreach(glob(LIMB_VAR_DIR . '/taskman-script.*.php') as $file)
      unlink($file);
  }

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
  
  function testProp()
  {
    list($code, $out) = $this->_run("    
    function task_foo() { echo taskman_prop('BAR'); }
    ",
    '-b foo -D BAR=42');

    $this->assertEqual(0, $code);
    $this->assertEqual("42", $out);
  }
  
  function testPropSet()
  {
    list($code, $out) = $this->_run("    
    function task_bar() { taskman_propset('BAZ', '42'); }
    /**
     * @deps bar
     */
    function task_foo() { echo taskman_prop('BAZ'); }
    ",
    '-b foo');

    $this->assertEqual(0, $code);
    $this->assertEqual("42", $out);
  }
  
  function testPropSetOr()
  {
    list($code, $out) = $this->_run("        
    function task_foo() {
      echo taskman_propor('BAR', 'error');
      echo taskman_propor('BAZ', 'success');
    }
    ",
    '-b foo -D BAR=42');

    $this->assertEqual(0, $code);
    $this->assertEqual("42success", $out);
  }  

  function testParallTasksFromCli()
  {
    @unlink(LIMB_VAR_DIR . '/shared');
    list($code, $out) = $this->_run("
    function write_shared(\$c) { \$fp = fopen('" . LIMB_VAR_DIR . "/shared', 'a');if(flock(\$fp, LOCK_EX)){ fwrite(\$fp, \$c); flock(\$fp, LOCK_UN); } fclose(\$fp);}
    function task_foo() { write_shared('foo');}
    function task_bar() { write_shared('bar'); }
    function task_wow() { write_shared('wow'); }
    ",
    "-b 'bar|foo|wow'");

    $this->assertEqual(0, $code);
    $this->assertEqual("", $out);
    $shared = file_get_contents(LIMB_VAR_DIR . '/shared');
    $this->assertTrue(strpos($shared, 'foo') !== false);
    $this->assertTrue(strpos($shared, 'bar') !== false);
    $this->assertTrue(strpos($shared, 'wow') !== false);
    $this->assertEqual(9, strlen($shared));
  }

  function testParallTasks()
  {
    @unlink(LIMB_VAR_DIR . '/shared');
    list($code, $out) = $this->_run("
    function write_shared(\$c) { \$fp = fopen('" . LIMB_VAR_DIR . "/shared', 'a');if(flock(\$fp, LOCK_EX)){ fwrite(\$fp, \$c); flock(\$fp, LOCK_UN); } fclose(\$fp);}
    function task_foo() { write_shared('foo'); }
    function task_bar() { write_shared('bar'); }
    /**
     * @deps bar|foo|wow
     */
    function task_zoo() { echo 'zoo'; }
    function task_wow() { write_shared('wow'); }
    ",
    '-b zoo');

    $this->assertEqual(0, $code);
    $this->assertEqual("zoo", $out);
    $shared = file_get_contents(LIMB_VAR_DIR . '/shared');
    $this->assertTrue(strpos($shared, 'foo') !== false);
    $this->assertTrue(strpos($shared, 'bar') !== false);
    $this->assertTrue(strpos($shared, 'wow') !== false);
    $this->assertEqual(9, strlen($shared));
  }

  protected function _run($contents, $cmd)
  {
    $file = LIMB_VAR_DIR . '/taskman-script.' . mt_rand() . '.php';
    file_put_contents($file, "<?php\nrequire_once('" . dirname(__FILE__) . "/../taskman.inc.php');\ntaskman_run();\n$contents");
    exec("php $file $cmd", $out, $res);
    return array($res, implode($out));
  }
}
