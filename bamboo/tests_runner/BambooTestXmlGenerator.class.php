<?php

class BambooTestXmlGenerator
{
  protected $_recorder;
  protected $_dom;
  protected $_root;                             // root dom element

  protected $_result_dataset;

  function __construct($recorder)
  {
    $this->_recorder = $recorder;
    $this->_dom = new DOMDocument('1.0', 'utf-8');
    $this->_dom->formatOutput = true;
    $this->_dom->preserveWhiteSpace = false;

    $this->_prepareResultDataset();
  }

  protected function _prepareResultDataset()
  {
    $this->_result_dataset = array();

    if(!$this->_recorder->getResults())
      return;

    foreach($this->_recorder->getResults() as $result)
    {
      if(!isset($this->_result_dataset[$result['test_class']][$result['test_method']]))
        $this->_result_dataset[$result['test_class']][$result['test_method']] = array();

      $this->_result_dataset[$result['test_class']][$result['test_method']][] = $result;
    }
  }

  function generate()
  {
    $this->_generateRootElement();
    $this->_generateTestSuites();

    return $this->_dom->saveXML();
  }

  protected function _generateRootElement()
  {
    $root = $this->_dom->createElement('testsuites');
    $this->_dom->appendChild($root);

    $this->_createAttribute('name', 'ATS Run tests results', $root);

    $this->_root = $root;
  }

  protected function _generateTestSuites()
  {
    foreach($this->_result_dataset as $testsuite_name => $dataset)
      $this->_createTestSuite($testsuite_name, $dataset);
  }

  protected function _createTestSuite($testsuite_name, $dataset)
  {
    $testsuite = $this->_dom->createElement('testsuite');
    $this->_createAttribute('name', $testsuite_name, $testsuite);

    $this->_root->appendChild($testsuite);

    $this->_generateTestCasesAndCountResults($testsuite, $testsuite_name, $dataset);
  }

  protected function _generateTestCasesAndCountResults($testsuite, $testsuite_name, $dataset)
  {
    $testsuite_assertions = 0;
    $testsuite_failures = 0;
    $testsuite_errors = 0;
    $testsuite_skipped = 0;

    foreach($dataset as $testcase_name => $testcase_dataset)
    {
      $results = $this->_createTestCase($testsuite, $testsuite_name, $testcase_name, $testcase_dataset);

      $testsuite_assertions += $results['assertions'];
      $testsuite_failures += $results['failures'];
      $testsuite_errors += $results['errors'];
      $testsuite_skipped += $results['skipped'];
    }

    $this->_createAttribute('assertions', $testsuite_assertions, $testsuite);
    $this->_createAttribute('failures', $testsuite_failures, $testsuite);
    $this->_createAttribute('errors', $testsuite_errors, $testsuite);
    $this->_createAttribute('skipped', $testsuite_skipped, $testsuite);
  }

  protected function _createTestCase($testsuite, $testsuite_name, $testcase_name, $testcase_dataset)
  {
    $testcase = $this->_dom->createElement('testcase');

    $this->_createAttribute('name', $testcase_name, $testcase);
    $this->_createAttribute('class', $testsuite_name, $testcase);

    $testsuite->appendChild($testcase);

    $testcase_assertions = 0;
    $testcase_failures = 0;
    $testcase_errors = 0;
    $testcase_exceptions = 0;
    $testcase_skipped = 0;

    foreach($testcase_dataset as $result)
    {
      if($result['status'] == 'Passed')
        $testcase_assertions++;
      elseif($result['status'] == 'Failed')
      {
        $testcase_failures++;
        $this->_createFailureOrErrorElement($testcase, $result);
      }
      elseif($result['status'] == 'Error')
      {
        $testcase_errors++;
        $this->_createFailureOrErrorElement($testcase, $result);
      }
      elseif($result['status'] == 'Exception')
      {
        $testcase_exceptions++;
        $this->_createFailureOrExceptionElement($testcase, $result);
      }
      elseif($result['status'] == 'Skipped')
      {
        $testcase_assertions++; /// - for bamboo while it can't handle skipped tests
        $testcase_skipped++;
        //        $this->_createSkippedElement($testcase, $result); -- bamboo can't parse <message> tag
        $this->_createAttribute('name', 'SKIPPED TESTS!'. $testcase_name, $testcase);
      }
    }

    $this->_createAttribute('assertions', $testcase_assertions, $testcase);
    $this->_createAttribute('errors', $testcase_errors, $testcase);
    $this->_createAttribute('failures', $testcase_failures, $testcase);
    $this->_createAttribute('exceptions', $testcase_exceptions, $testcase);
    $this->_createAttribute('skipped', $testcase_skipped, $testcase);

    return array(
      'assertions' => $testcase_assertions,
      'failures' => $testcase_failures,
      'errors' => $testcase_errors,
      'skipped' => $testcase_skipped,
    );
  }

  protected function _createAttribute($name, $value, $append_to)
  {
    $attribute = $this->_dom->createAttribute($name);
    $attribute->value = $value;

    $append_to->appendChild($attribute);
  }

  protected function _createFailureOrErrorElement($testcase, $result)
  {
    $tag_name = ($result['status'] == 'Failed' ? 'failure' : 'error');
    $element = $this->_dom->createElement($tag_name);
    $message = $this->_dom->createTextNode($result['message']);

    $element->appendChild($message);
    $testcase->appendChild($element);
  }

  protected function _createFailureOrExceptionElement($testcase, $result)
  {
    $element = $this->_dom->createElement('error');
    $message = $this->_dom->createTextNode($result['message']);

    $element->appendChild($message);
    $testcase->appendChild($element);
  }

  protected function _createSkippedElement($testcase, $result)
  {
    $element = $this->_dom->createElement('message');
    $message = $this->_dom->createTextNode($result['message']);

    $element->appendChild($message);
    $testcase->appendChild($element);
  }
}
