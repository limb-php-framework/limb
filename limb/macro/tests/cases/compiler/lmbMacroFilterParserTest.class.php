<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroFilterParserTest extends UnitTestCase
{
  protected $parser;

  function setUp()
  {
    $location = new lmbMacroSourceLocation('my_testing_file', 10);
    $context_node = new lmbMacroNode($location);

    $this->parser = new lmbMacroFilterParser($context_node);
  }

  function testName()
  {
    $filters = $this->parser->parse($expression = 'filter');
    $this->assertEqual($filters, array(array('name' => 'filter',
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
    $filters = $this->parser->parse($expression = 'filter:$arg');
    $this->assertEqual($filters, array(array('name' => 'filter',
                                             'expression' => 'filter:$arg',
                                             'params' => array('$arg'))));
  }

  function testTwoParams()
  {
    $filters = $this->parser->parse($expression = 'filter:$arg1,"arg2"');
    $this->assertEqual($filters, array(array('name' => 'filter',
                                             'expression' => 'filter:$arg1,"arg2"',
                                             'params' => array('$arg1','"arg2"'))));
  }

  function testSpaceInParams()
  {
    $filters = $this->parser->parse($expression = 'filter:" "');
    $this->assertEqual($filters, array(array('name' => 'filter',
                                             'expression' => 'filter:" "',
                                             'params' => array('" "'))));
  }

  function testTwoFiltersNoParams()
  {
    $filters = $this->parser->parse($expression = 'filter1|filter2');
    $this->assertEqual($filters, array(array('name' => 'filter1',
                                             'expression' => 'filter1',
                                             'params' => array()),
                                       array('name' => 'filter2',
                                             'expression' => 'filter2',
                                             'params' => array())));
  }

  function testTwoFiltersWithParams()
  {
    $filters = $this->parser->parse($expression = 'filter1: $arg1, arg2 |filter2: arg3');
    $this->assertEqual($filters, array(array('name' => 'filter1',
                                             'expression' => 'filter1: $arg1, arg2 ',
                                             'params' => array(' $arg1',' arg2 ')),
                                       array('name' => 'filter2',
                                             'expression' => 'filter2: arg3',
                                             'params' => array(' arg3'))));
  }

  function testTwoFiltersWithSeparatorInParams()
  {
    $filters = $this->parser->parse($expression = 'filter1: "x|y", arg2 |filter2: arg3');
    $this->assertEqual($filters, array(array('name' => 'filter1',
                                             'expression' => 'filter1: "x|y", arg2 ',
                                             'params' => array(' "x|y"',' arg2 ')),
                                       array('name' => 'filter2',
                                             'expression' => 'filter2: arg3',
                                             'params' => array(' arg3'))));
  }
}

