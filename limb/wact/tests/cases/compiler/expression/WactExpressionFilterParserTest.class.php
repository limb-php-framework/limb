<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactExpressionFilterParserTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';

class WactExpressionFilterParserTest extends UnitTestCase {

  function testName()
  {
    $expression = 'filter';
    $parser = new WactExpressionFilterParser($expression);
    $this->assertEqual($parser->getFilterName(), 'filter');
  }

  function testEmptyName()
  {
    $expression = '';
    $parser = new WactExpressionFilterParser($expression);
    $this->assertEqual($parser->getFilterName(), NULL);
  }

  function testInvalidName()
  {
    $expression = '"filter"';
    $parser = new WactExpressionFilterParser($expression);
    $this->assertEqual($parser->getFilterName(), NULL);
  }

  function testNoArgs()
  {
    $expression = 'filter';
    $parser = new WactExpressionFilterParser($expression);
    $this->assertEqual($parser->getFilterName(), 'filter');
    $this->assertEqual($parser->getFilterArguments(), NULL);
  }

  function testNoArgsWithDelimiter()
  {
    $expression = 'filter:';
    $parser = new WactExpressionFilterParser($expression);
    $this->assertEqual($parser->getFilterName(), 'filter');
    $this->assertEqual($parser->getFilterArguments(), array());
  }

  function testNoArgsWithComma()
  {
    $expression = 'filter,';
    $parser = new WactExpressionFilterParser($expression);
    $this->assertEqual($parser->getFilterName(), 'filter');
    $this->assertEqual($parser->getFilterArguments(), NULL);
  }

  function testOneArg()
  {
    $expression = 'filter:arg';
    $parser = new WactExpressionFilterParser($expression);
    $this->assertEqual($parser->getFilterName(), 'filter');
    $this->assertEqual($parser->getFilterArguments(), array('arg'));
  }

  function testTwoArgs() {
    $expression = 'filter:argA,argB';
    $parser = new WactExpressionFilterParser($expression);
    $this->assertEqual($parser->getFilterName(), 'filter');
    $this->assertEqual($parser->getFilterArguments(), array('argA','argB'));
  }

  function testDoubleQuotesArgDelimiter()
  {
    $expression = 'filter:argA,"x:y"';
    $parser = new WactExpressionFilterParser($expression);
    $this->assertEqual($parser->getFilterName(), 'filter');
    $this->assertEqual($parser->getFilterArguments(), array('argA','"x:y"'));
  }

  function testSingleQuotesArgDelimiter()
  {
    $expression = 'filter:argA,\'x:y\'';
    $parser = new WactExpressionFilterParser($expression);
    $this->assertEqual($parser->getFilterName(), 'filter');
    $this->assertEqual($parser->getFilterArguments(), array('argA','\'x:y\''));
  }

  function testDoubleQuotesArgSeperator()
  {
    $expression = 'filter:argA,"x,y"';
    $parser = new WactExpressionFilterParser($expression);
    $this->assertEqual($parser->getFilterName(), 'filter');
    $this->assertEqual($parser->getFilterArguments(), array('argA','"x,y"'));
  }

  function testSingleQuotesArgSeperator()
  {
    $expression = 'filter:argA,\'x,y\'';
    $parser = new WactExpressionFilterParser($expression);
    $this->assertEqual($parser->getFilterName(), 'filter');
    $this->assertEqual($parser->getFilterArguments(), array('argA','\'x,y\''));
  }

  function testTwoDoubleQuotes()
  {
    $expression = 'filter:"x\'1","y\'2"';
    $parser = new WactExpressionFilterParser($expression);
    $this->assertEqual($parser->getFilterName(), 'filter');
    $this->assertEqual($parser->getFilterArguments(), array('"x\'1"','"y\'2"'));
  }

  function testTwoSingleQuotes()
  {
    $expression = 'filter:\'x"1\',\'y"2\'';
    $parser = new WactExpressionFilterParser($expression);
    $this->assertEqual($parser->getFilterName(), 'filter');
    $this->assertEqual($parser->getFilterArguments(), array('\'x"1\'','\'y"2\''));
  }

}
?>
