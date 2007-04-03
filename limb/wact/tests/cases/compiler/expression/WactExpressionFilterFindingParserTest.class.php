<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactExpressionFilterFindingParserTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';

class WactExpressionFilterFindingParserTest extends UnitTestCase
{
  function testSingleValue()
  {
    $expression = 'value';
    $parser = new WactExpressionFilterFindingParser($expression);
    $this->assertEqual($parser->getFilterExpressions(), array('value'));
  }

  function testTwoValues()
  {
    $expression = 'valueA|valueB';
    $parser = new WactExpressionFilterFindingParser($expression);
    $this->assertEqual($parser->getFilterExpressions(), array('valueA','valueB'));
  }

  function testDoubleQuoteValueDelimiter()
  {
    $expression = 'valueA|"A|B"';
    $parser = new WactExpressionFilterFindingParser($expression);
    $this->assertEqual($parser->getFilterExpressions(), array('valueA','"A|B"'));
  }

  function testSingleQuoteValueDelimiter()
  {
    $expression = 'valueA|\'A|B\'';
    $parser = new WactExpressionFilterFindingParser($expression);
    $this->assertEqual($parser->getFilterExpressions(), array('valueA','\'A|B\''));
  }

  function testNoValue()
  {
    $expression = '';
    $parser = new WactExpressionFilterFindingParser($expression);
    $this->assertEqual($parser->getFilterExpressions(), array());
  }

  function testNoValueWithDelimiter()
  {
    $expression = '|';
    $parser = new WactExpressionFilterFindingParser($expression);
    $this->assertEqual($parser->getFilterExpressions(), array());
  }

  function testNoValueWithTwoDelimiters()
  {
    $expression = '||';
    $parser = new WactExpressionFilterFindingParser($expression);
    $this->assertEqual($parser->getFilterExpressions(), array());
  }

  function testValueWithNoValue()
  {
    $expression = 'valueA|valueB|';
    $parser = new WactExpressionFilterFindingParser($expression);
    $this->assertEqual($parser->getFilterExpressions(), array('valueA','valueB'));
  }

  function testValueWithNoValueStartingDelimiter()
  {
    $expression = '|valueA|valueB|';
    $parser = new WactExpressionFilterFindingParser($expression);
    $this->assertEqual($parser->getFilterExpressions(), array('valueA','valueB'));
  }

  function testTwoDoubleQuotes()
  {
    $expression = 'filterA:"x\'1"|filterB:"y\'2"';
    $parser = new WactExpressionFilterFindingParser($expression);
    $this->assertEqual($parser->getFilterExpressions(), array('filterA:"x\'1"','filterB:"y\'2"'));
  }

  function testTwoSingleQuotes()
  {
    $expression = 'filterA:\'x"1\'|filterB:\'y"2\'';
    $parser = new WactExpressionFilterFindingParser($expression);
    $this->assertEqual($parser->getFilterExpressions(), array('filterA:\'x"1\'','filterB:\'y"2\''));
  }
}
?>
