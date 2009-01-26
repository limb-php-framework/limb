<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';
require_once 'limb/wact/src/compiler/expression/WactExpressionFilterParser.class.php';

class WactExpressionFilterParserTest extends UnitTestCase
{
  protected $parser;

  function setUp()
  {
    $location = new WactSourceLocation('my_testing_file', 10);
    $context_node = new WactCompileTreeNode($location);

    $this->parser = new WactExpressionFilterParser($context_node);
  }

  function testName()
  {
    $filters = $this->parser->parse($expression = 'filter');
    $this->assertEqual($filters, array('filter' => array('name' => 'filter',
                                                         'expression' => 'filter',
                                                         'params' => array())));
  }

  function testEmptyName()
  {
    $filters = $this->parser->parse($expression = '');
    $this->assertEqual($filters, array());
  }

  function testInvalidName()
  {
    try
    {
      $filters = $this->parser->parse($expression = '"filter"');
      $this->assertTrue(false);
    }
    catch(Exception $e)
    {
      $this->assertWantedPattern('/Filter name expected/', $e->getMessage());
    }
  }

  function testNoArgsWithDelimiter()
  {
    try
    {
      $filters = $this->parser->parse($expression = 'filter:');
      $this->assertTrue(false);
    }
    catch(Exception $e)
    {
      $this->assertWantedPattern('/Filter params expected after ":" symbol/', $e->getMessage());
    }
  }

  function testNoArgsWithComma()
  {
    try
    {
      $filters = $this->parser->parse($expression = 'filter,');
      $this->assertTrue(false);
    }
    catch(Exception $e)
    {
      $this->assertWantedPattern('/Unexpected symbol after filter name/', $e->getMessage());
    }
  }

  function testOneParam()
  {
    $filters = $this->parser->parse($expression = 'filter:arg');
    $this->assertEqual($filters, array('filter' => array('name' => 'filter',
                                                         'expression' => 'filter:arg',
                                                         'params' => array('arg'))));
  }

  function testTwoParams()
  {
    $filters = $this->parser->parse($expression = 'filter: arg1 , arg2');
    $this->assertEqual($filters, array('filter' => array('name' => 'filter',
                                                         'expression' => 'filter: arg1 , arg2',
                                                         'params' => array('arg1','arg2'))));
  }

  function testDoubleQuotesParamsDelimiter()
  {
    $filters = $this->parser->parse($expression = 'filter:argA,"x:y"');
    $this->assertEqual($filters, array('filter' => array('name' => 'filter',
                                                         'expression' => 'filter:argA,"x:y"',
                                                         'params' => array('argA','"x:y"'))));

  }

  function testSingleQuotesArgDelimiter()
  {
    $filters = $this->parser->parse($expression = 'filter:argA,\'x:y\'');
    $this->assertEqual($filters, array('filter' => array('name' => 'filter',
                                                         'expression' => 'filter:argA,\'x:y\'',
                                                         'params' => array('argA','\'x:y\''))));

  }

  function testDoubleQuotesArgSeperator()
  {
    $filters = $this->parser->parse($expression = 'filter:argA,"x,y"');
    $this->assertEqual($filters, array('filter' => array('name' => 'filter',
                                                         'expression' => 'filter:argA,"x,y"',
                                                         'params' => array('argA','"x,y"'))));

  }

  function testSingleQuotesArgSeperator()
  {
    $filters = $this->parser->parse($expression = 'filter:argA,\'x,y\'');
    $this->assertEqual($filters, array('filter' => array('name' => 'filter',
                                                         'expression' => 'filter:argA,\'x,y\'',
                                                         'params' => array('argA','\'x,y\''))));

  }

  function testTwoDoubleQuotes()
  {
    $filters = $this->parser->parse($expression = 'filter:"x\'1","y\'2"');
    $this->assertEqual($filters, array('filter' => array('name' => 'filter',
                                                         'expression' => 'filter:"x\'1","y\'2"',
                                                         'params' => array('"x\'1"','"y\'2"'))));

  }

  function testTwoSingleQuotes()
  {
    $filters = $this->parser->parse($expression = 'filter:\'x"1\',\'y"2\'');
    $this->assertEqual($filters, array('filter' => array('name' => 'filter',
                                                         'expression' => 'filter:\'x"1\',\'y"2\'',
                                                         'params' => array('\'x"1\'','\'y"2\''))));
  }

  function testParamsStarterInParams()
  {
    $filters = $this->parser->parse($expression = 'filter: ", test: 99"');
    $this->assertEqual($filters, array('filter' => array('name' => 'filter',
                                                         'expression' => 'filter: ", test: 99"',
                                                         'params' => array('", test: 99"'))));
  }

  function testSpaceInParams()
  {
    $filters = $this->parser->parse($expression = 'filter:" "');
    $this->assertEqual($filters, array('filter' => array('name' => 'filter',
                                                         'expression' => 'filter:" "',
                                                         'params' => array('" "'))));
  }

  function testParamDelimeterInParams()
  {
    $filters = $this->parser->parse($expression = 'filter:3, ",", "."');
    $this->assertEqual($filters, array('filter' => array('name' => 'filter',
                                                         'expression' => 'filter:3, ",", "."',
                                                         'params' => array(3,'","','"."'))));
  }

  function testParamDelimeterInParams2()
  {
    $filters = $this->parser->parse($expression = 'filter:"a","b" ,"c"');
    $this->assertEqual($filters, array('filter' => array('name' => 'filter',
                                                         'expression' => 'filter:"a","b" ,"c"',
                                                         'params' => array('"a"', '"b"', '"c"'))));
  }

  function testTwoFiltersNoParams()
  {
    $filters = $this->parser->parse($expression = 'filter1|filter2');
    $this->assertEqual($filters, array('filter1' => array('name' => 'filter1',
                                                         'expression' => 'filter1',
                                                         'params' => array()),
                                       'filter2' => array('name' => 'filter2',
                                                         'expression' => 'filter2',
                                                         'params' => array())));
  }

  function testTwoFiltersWithParams()
  {
    $filters = $this->parser->parse($expression = 'filter1: arg1, arg2 |filter2: arg3');
    $this->assertEqual($filters, array('filter1' => array('name' => 'filter1',
                                                         'expression' => 'filter1: arg1, arg2 ',
                                                         'params' => array('arg1','arg2')),
                                       'filter2' => array('name' => 'filter2',
                                                         'expression' => 'filter2: arg3',
                                                         'params' => array('arg3'))));
  }

  function testTwoFiltersWithSeparatorInParams()
  {
    $filters = $this->parser->parse($expression = 'filter1: "x|y", arg2 |filter2: arg3');
    $this->assertEqual($filters, array('filter1' => array('name' => 'filter1',
                                                         'expression' => 'filter1: "x|y", arg2 ',
                                                         'params' => array('"x|y"','arg2')),
                                       'filter2' => array('name' => 'filter2',
                                                         'expression' => 'filter2: arg3',
                                                         'params' => array('arg3'))));
  }
}

