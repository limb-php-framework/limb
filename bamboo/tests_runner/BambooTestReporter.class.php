<?php
lmb_require('limb/tests_runner/src/lmbTestShellReporter.class.php');

class BambooTestReporter extends lmbTestShellReporter
{
  protected $_results;
  protected $_skipped = 0;

  function paintSkip($message)
  {
    $this->_skipped++;
    parent :: paintSkip($message);
    $this->_generateElement($message, 'Skipped');
  }

  function getSkippedCount()
  {
    return $this->_skipped;
  }

  function _generateElement($message, $status)
  {
    $breadcrumb = $this->getTestList();
    array_shift($breadcrumb);

    $result = array(
      'test_class' => $breadcrumb[count($breadcrumb) - 2],
      'test_method' => $breadcrumb[count($breadcrumb) - 1],
      'time' =>  time(),
      'message' => $message,
      'status' =>  $status,
    );

    $this->_results[] = $result;
  }

  function paintPass($message)
  {
    parent::paintPass($message);
    $this->_generateElement($message, 'Passed');
  }

  function paintFail($message)
  {
    parent::paintFail($message);
    $this->_generateElement($message, 'Failed');
  }

  function paintError($message)
  {
    parent::paintError($message);
    $this->_generateElement($message, 'Error');
  }

  function paintException($message)
  {
    parent::paintException($message);
    $this->_generateElement($message, 'Exception');
  }

  function getResults()
  {
    return $this->_results;
  }
}