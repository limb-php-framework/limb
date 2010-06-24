<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';

class WactExpressionTestingFilter extends WactCompilerFilter
{
  public static $calls = array();

  function generatePreStatement($code_writer)
  {
    if(!is_object($code_writer))
      throw new Exception('Code writer was not passed!');

    self :: $calls['generatePreStatement']++;

    parent :: generatePreStatement($code_writer);
  }

  function generateExpression($code_writer)
  {
    if(!is_object($code_writer))
      throw new Exception('Code writer was not passed!');

    self :: $calls['generateExpression']++;

    parent :: generateExpression($code_writer);
  }

  function generatePostStatement($code_writer)
  {
    if(!is_object($code_writer))
      throw new Exception('Code writer was not passed!');

    self :: $calls['generatePostStatement']++;

    parent :: generatePostStatement($code_writer);
  }

  function prepare()
  {
    self :: $calls['prepare']++;

    parent :: prepare();
  }

  function getValue()
  {
    return $this->base->getValue();
  }

  static function reset()
  {
    self :: $calls = array('generatePreStatement' => 0,
                           'generateExpression' => 0,
                           'generatePostStatement' => 0,
                            'prepare' => 0);
  }
}

class WactExpressionTest extends UnitTestCase
{
  protected $filter_dictionary;

  function setUp()
  {
    WactExpressionTestingFilter :: reset();
    $this->filter_dictionary = new WactFilterDictionary();
    $filter_info = new WactFilterInfo('expression_testing_filter', 'WactExpressionTestingFilter', 0, 3);
    $this->filter_dictionary->registerFilterInfo($filter_info, __FILE__);
  }

  protected function _createExpression($expression_text, $default_filter = 'raw')
  {
    $location = new WactSourceLocation('my_testing_file', 10);
    $context_node = new WactCompileTreeNode($location);
    return new WactExpression($expression_text, $context_node, $this->filter_dictionary, $default_filter);
  }

  function _parseAndReturnGeneratedCode($expression_text)
  {
    $code = new WactCodeWriter();
    $expression = $this->_createExpression($expression_text);
    $expression->generatePreStatement($code);
    $expression->generateExpression($code);
    $expression->generatePostStatement($code);
    return $code->getCode();
  }

  function testCreateValueInteger()
  {
    $expression = $this->_createExpression('29');
    $this->assertTrue($expression->isConstant());
    $this->assertEqual($expression->getValue(), 29);
  }

  function testCreateValueFloat()
  {
    $expression = $this->_createExpression('1.5');
    $this->assertTrue($expression->isConstant());
    $this->assertEqual($expression->getValue(), 1.5);
  }

  function testCreateValueNegInteger()
  {
    $code = $this->_parseAndReturnGeneratedCode('-29');
    $this->assertEqual($code, '<?php -29');
  }

  function testCreateValueNegFloat()
  {
    $code = $this->_parseAndReturnGeneratedCode('-1.5');
    $this->assertEqual($code, '<?php -1.5');
  }

  function testCreateValueSingleQuoteString()
  {
    $expression = $this->_createExpression("'hello'");
    $this->assertTrue($expression->isConstant());
    $this->assertEqual($expression->getValue(), 'hello');
  }

  function testCreateValueDoubleQuoteString()
  {
    $expression = $this->_createExpression('"hello"');
    $this->assertTrue($expression->isConstant());
    $this->assertEqual($expression->getValue(), 'hello');
  }

  function testMissingFilter()
  {
    try
    {
      $expression = $this->_createExpression('Test|no_such_filter');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Unknown filter/', $e->getMessage());
      $this->assertEqual($e->getParam('filter'), 'no_such_filter');
    }
  }

  function testInvalidFilter()
  {
    try
    {
      $expression = $this->_createExpression('Test|99');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Filter name expected/', $e->getMessage());
    }
  }

  function testIsConstant()
  {
    $expression = $this->_createExpression('"Test"');
    $this->assertTrue($expression->isConstant());
  }

  function testGetValueForConstant()
  {
    $expression = $this->_createExpression('"Test"');
    $this->assertEqual($expression->getValue(), 'Test');
  }

  function testGetValueForDBE()
  {
    $context = new WactCompileTreeNode();
    $property = new WactConstantProperty('hello');
    $context->registerProperty('Test', $property);

    $expression = new WactExpression('Test', $context, $this->filter_dictionary);
    $this->assertEqual($expression->getValue(), 'hello');
  }

  function testGenerateMethods()
  {
    $code_writer = new WactCodeWriter();

    $expression = $this->_createExpression('"Test"|expression_testing_filter');

    $expression->generatePreStatement($code_writer);
    $expression->generateExpression($code_writer);
    $expression->generatePostStatement($code_writer);
    $expression->prepare();

    $this->assertEqual(WactExpressionTestingFilter :: $calls['generatePreStatement'], 1);
    $this->assertEqual(WactExpressionTestingFilter :: $calls['generateExpression'], 1);
    $this->assertEqual(WactExpressionTestingFilter :: $calls['generatePostStatement'], 1);
    $this->assertEqual(WactExpressionTestingFilter :: $calls['prepare'], 1);
  }

  function testApplyDefaultFilter()
  {
    $expression = $this->_createExpression('"Test"|expression_testing_filter', 'expression_testing_filter');
    $expression->prepare();
    $this->assertEqual(WactExpressionTestingFilter :: $calls['prepare'], 1);
  }

  function testFilterWithParams()
  {
    $expression = $this->_createExpression('"Test"|expression_testing_filter:"..."');
    $expression->prepare();
    $this->assertEqual(WactExpressionTestingFilter :: $calls['prepare'], 1);
  }

  function testDontApplyDefaultFilter()
  {
    $expression = $this->_createExpression('"Test"|raw', 'expression_testing_filter');
    $expression->prepare();
    $this->assertEqual(WactExpressionTestingFilter :: $calls['prepare'], 0);
  }

  function testDontApplyDefaultFilter2()
  {
    $expression = $this->_createExpression('"Test"|expression_testing_filter|raw', 'expression_testing_filter');
    $expression->prepare();
    $this->assertEqual(WactExpressionTestingFilter :: $calls['prepare'], 1);
  }

  function testDontApplyDefaultFilterIsTheSameFilterIsTheLastAmongExpressionFilters()
  {
    $expression = $this->_createExpression('"Test"|expression_testing_filter', 'expression_testing_filter');
    $expression->prepare();
    $this->assertEqual(WactExpressionTestingFilter :: $calls['prepare'], 1);
  }
}

