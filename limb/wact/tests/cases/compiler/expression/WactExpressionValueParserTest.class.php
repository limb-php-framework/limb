<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactExpressionValueParserTest.class.php 5691 2007-04-19 13:27:02Z serega $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/expression/WactExpressionValueParser.class.php';

class WactExpressionValueParserTest extends UnitTestCase
{
  protected $parser;
  protected $root;
  protected $context;

  function setUp()
  {
    // preparing tree nodes with runtime
    $this->root = new WactCompileTreeRootNode();

    $this->context = new WactCompileTreeNode();
    $this->context->setServerId('child');
    $this->context->parent = $this->root;

    $this->parser = new WactExpressionValueParser($this->context);
  }

  function tearDown()
  {
    unset($this->parser);
  }

  function testNull() {
    $expr = $this->parser->parse('null');
    $this->assertIdentical($expr->getValue(), null);
  }

  function testTrue()
  {
    $expr = $this->parser->parse('true');
    $this->assertIdentical($expr->getValue(), true);
  }

  function testFalse()
  {
    $expr = $this->parser->parse('false');
    $this->assertIdentical($expr->getValue(), false);
  }

  function testInteger()
  {
    $expr = $this->parser->parse('1');
    $this->assertIdentical($expr->getValue(), 1);
  }

  function testZero()
  {
    $expr = $this->parser->parse('0');
    $this->assertIdentical($expr->getValue(), 0);
  }

  function testFloat()
  {
    $expr = $this->parser->parse('1.2');
    $this->assertIdentical($expr->getValue(), 1.2);
  }

  function testStringDoubleQuotes() {
    $expr = $this->parser->parse('"hello"');
    $this->assertIdentical($expr->getValue(), "hello");
  }

  function testZeroDoubleQuotes()
  {
    $expr = $this->parser->parse('"0"');
    $this->assertIdentical($expr->getValue(), "0");
  }

  function testStringSingleQuotes()
  {
    $expr = $this->parser->parse("'hello'");
    $this->assertIdentical($expr->getValue(), "hello");
  }

  function testSeveralDots()
  {
    $expr = $this->parser->parse('"..."');
    $this->assertIdentical($expr->getValue(), '...');
  }

  function testAddition()
  {
    $expr = $this->parser->parse('1+2');
    $this->assertIdentical($expr->getValue(), 1+2);
  }

  function testSubtraction()
  {
    $expr = $this->parser->parse('1-2');
    $this->assertIdentical($expr->getValue(), 1-2);
  }

  function testMultiplication()
  {
    $expr = $this->parser->parse('2*3');
    $this->assertIdentical($expr->getValue(), 2*3);
  }

  function testDivision()
  {
    $expr = $this->parser->parse('8/2');
    $this->assertIdentical($expr->getValue(), 8/2);
  }

  function testModulo()
  {
    $expr = $this->parser->parse('5%2');
    $this->assertIdentical($expr->getValue(), 5%2);
  }

  function testMinus()
  {
    $expr = $this->parser->parse('-2');
    $this->assertIdentical($expr->getValue(), -2);
  }

  function testConcatination()
  {
    $expr = $this->parser->parse('"head" & "tail"');
    $this->assertIdentical($expr->getValue(), 'headtail');
  }

  function testLogicalAnd()
  {
    $expr = $this->parser->parse('true and false');
    $this->assertIdentical($expr->getValue(), false);
  }

  function testLogicalOr()
  {
    $expr = $this->parser->parse('true or false');
    $this->assertIdentical($expr->getValue(), true);
  }

  function testLogicalNot()
  {
    $expr = $this->parser->parse('not false');
    $this->assertIdentical($expr->getValue(), true);
  }

  function testLogicalEqual()
  {
    $expr = $this->parser->parse('1 == 1');
    $this->assertIdentical($expr->getValue(), true);
  }

  function testLogicalNotEqual()
  {
    $expr = $this->parser->parse('1 != 2');
    $this->assertIdentical($expr->getValue(), true);
  }

  function testLogicalLessThan()
  {
    $expr = $this->parser->parse('1 < 2');
    $this->assertIdentical($expr->getValue(), true);
  }

  function testLogicalLessThanOrEqual()
  {
    $expr = $this->parser->parse('2 <= 2');
    $this->assertIdentical($expr->getValue(), true);
  }

  function testLogicalGreaterThan()
  {
    $expr = $this->parser->parse('3 > 2');
    $this->assertIdentical($expr->getValue(), true);
  }

  function testLogicalGreaterThanOrEqual()
  {
    $expr = $this->parser->parse('3 >= 2');
    $this->assertIdentical($expr->getValue(), true);
  }

  function testMyDearAuntSally()
  {
    $expr = $this->parser->parse('1+2*3');
    $this->assertIdentical($expr->getValue(), 1+2*3);
  }

  function testMyDearAuntSally2()
  {
    $expr = $this->parser->parse('2*3+1');
    $this->assertIdentical($expr->getValue(), 2*3+1);
  }

  function testMyDearAuntSally3()
  {
    $expr = $this->parser->parse('2*3+4*5');
    $this->assertIdentical($expr->getValue(), 2*3+4*5);
  }

  function testMyDearAuntSally4()
  {
    $expr = $this->parser->parse('8-4-2');
    $this->assertIdentical($expr->getValue(), 8-4-2);
  }

  function testMyDearAuntSally5()
  {
    $expr = $this->parser->parse('24*6*2');
    $this->assertIdentical($expr->getValue(), 24*6*2);
  }

  function testMyDearAuntSally6()
  {
    $expr = $this->parser->parse('24/6/2');
    $this->assertIdentical($expr->getValue(), 24/6/2);
  }

  function testParenthesis()
  {
    $expr = $this->parser->parse('8-(4-2)');
    $this->assertIdentical($expr->getValue(), 8-(4-2));
  }

  function testConstantPropertyFromContext()
  {
    $property = new WactConstantProperty('hello');

    $this->context->registerProperty('Test', $property);

    $expr = $this->parser->parse('Test');
    $this->assertIdentical($expr->getValue(),'hello');
  }

  function testConstantPropertyFromRootContext()
  {
    $property = new WactConstantProperty('hello');

    $this->root->registerProperty('Test', $property);

    $expr = $this->parser->parse('#Test');
    $this->assertIdentical($expr->getValue(),'hello');
  }

  function testConstantPropertyFromParentContext()
  {
    $property = new WactConstantProperty('hello');

    $this->root->registerProperty('Test', $property);

    //debugBreak();
    $expr = $this->parser->parse('^Test');
    $this->assertIdentical($expr->getValue(),'hello');
  }
}

?>
