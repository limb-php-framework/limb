<?php
lmb_require('limb/tests_runner/src/lmbTestRunner.class.php');
lmb_require('limb/tests_runner/src/lmbTestTreeFilePathNode.class.php');

class BambooTestRunner extends lmbTestRunner
{
  /* function run($cases) */
  /* { */
  /*   return parent :: run(new lmbTestTreeFilePathNode($cases)); */
  /* } */

  protected function _getReporter()
  {
    if(!$this->reporter)
    {
      require_once('limb/test_runner/src/lmbTestShellReporter.class.php');
      SimpleTest :: prefer(new lmbTestShellReporter());

      $this->reporter = clone(SimpleTest :: preferred(array('SimpleReporter', 'SimpleReporterDecorator')));
    }

    return $this->reporter;
  }
}