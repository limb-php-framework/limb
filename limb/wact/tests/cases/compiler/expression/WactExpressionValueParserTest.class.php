<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once 'limb/wact/src/compiler/expression/WactExpressionValueParser.class.php';

class WactExpressionValueParserTest extends UnitTestCase
{
  protected $parser;
  protected $root;
  protected $context;
  protected $code;

  function setUp()
  {
    $this->code = new WactCodeWriter();
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

  function _parseAndReturnGeneratedCode($expression)
  {
    $expr = $this->parser->parse($expression);
    $expr->generatePreStatement($this->code);
    $expr->generateExpression($this->code);
    $expr->generatePostStatement($this->code);
    return $this->code->getCode();
  }

  function testNull()
  {
    $code = $this->_parseAndReturnGeneratedCode('null');
    $this->assertEqual($code, '<?php NULL');
  }

  function testTrue()
  {
    $code = $this->_parseAndReturnGeneratedCode('true');
    $this->assertEqual($code, '<?php true');
  }

  function testFalse()
  {
    $code = $this->_parseAndReturnGeneratedCode('false');
    $this->assertEqual($code, '<?php false');
  }

  function testInteger()
  {
    $code = $this->_parseAndReturnGeneratedCode('1');
    $this->assertEqual($code, '<?php 1');
  }

  function testZero()
  {
    $code = $this->_parseAndReturnGeneratedCode('0');
    $this->assertEqual($code, '<?php 0');
  }

  function testFloat()
  {
    $code = $this->_parseAndReturnGeneratedCode('1.2');
    $this->assertEqual($code, '<?php 1.2');
  }

  function testStringDoubleQuotes()
  {
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
    $code = $this->_parseAndReturnGeneratedCode('1+2');
    $this->assertEqual($code, "<?php 1+2");
  }

  function testSubtraction()
  {
    $code = $this->_parseAndReturnGeneratedCode('1-2');
    $this->assertEqual($code, "<?php 1-2");
  }

  function testMultiplication()
  {
    $code = $this->_parseAndReturnGeneratedCode('2*3');
    $this->assertEqual($code, "<?php 2*3");
  }

  function testDivision()
  {
    $code = $this->_parseAndReturnGeneratedCode('8/2');
    $this->assertEqual($code, "<?php 8/2");
  }

  function testModulo()
  {
    $code = $this->_parseAndReturnGeneratedCode('5%2');
    $this->assertEqual($code, "<?php 5%2");
  }

  function testMinus()
  {
    $code = $this->_parseAndReturnGeneratedCode('-2');
    $this->assertEqual($code, "<?php -2");
  }

  function testConcatination()
  {
    $code = $this->_parseAndReturnGeneratedCode('"head" & "tail"');
    $this->assertEqual($code, "<?php 'head'&'tail'");
  }

  function testLogicalAnd()
  {
    $code = $this->_parseAndReturnGeneratedCode('true && false');
    $this->assertEqual($code, "<?php true&&false");
  }

  function testLogicalOr()
  {
    $code = $this->_parseAndReturnGeneratedCode('true || false');
    $this->assertEqual($code, "<?php true||false");
  }

  function testLogicalNot()
  {
    $code = $this->_parseAndReturnGeneratedCode('!false');
    $this->assertEqual($code, "<?php !false");
  }

  function testLogicalEqual()
  {
    $code = $this->_parseAndReturnGeneratedCode('1 == 1');
    $this->assertEqual($code, "<?php 1==1");
  }

  function testLogicalNotEqual()
  {
    $code = $this->_parseAndReturnGeneratedCode('1!=2');
    $this->assertEqual($code, "<?php 1!=2");
  }

  function testLogicalLessThan()
  {
    $code = $this->_parseAndReturnGeneratedCode('1 < 2');
    $this->assertEqual($code, "<?php 1<2");
  }

  function testLogicalLessThanOrEqual()
  {
    $code = $this->_parseAndReturnGeneratedCode('2 <= 2');
    $this->assertEqual($code, "<?php 2<=2");
  }

  function testLogicalGreaterThan()
  {
    $code = $this->_parseAndReturnGeneratedCode('3 > 2');
    $this->assertEqual($code, "<?php 3>2");
  }

  function testLogicalGreaterThanOrEqual()
  {
    $code = $this->_parseAndReturnGeneratedCode('3 >= 2');
    $this->assertEqual($code, "<?php 3>=2");
  }

  function testMyDearAuntSally()
  {
    $code = $this->_parseAndReturnGeneratedCode('1+2*3');
    $this->assertEqual($code, "<?php 1+2*3");
  }

  function testMyDearAuntSally2()
  {
    $code = $this->_parseAndReturnGeneratedCode('2*3+1');
    $this->assertEqual($code, "<?php 2*3+1");
  }

  function testMyDearAuntSally3()
  {
    $code = $this->_parseAndReturnGeneratedCode('2*3+4*5');
    $this->assertEqual($code, "<?php 2*3+4*5");
  }

  function testMyDearAuntSally4()
  {
    $code = $this->_parseAndReturnGeneratedCode('8-4-2');
    $this->assertEqual($code, "<?php 8-4-2");
  }

  function testMyDearAuntSally5()
  {
    $code = $this->_parseAndReturnGeneratedCode('24*6*2');
    $this->assertEqual($code, "<?php 24*6*2");
  }

  function testMyDearAuntSally6()
  {
    $code = $this->_parseAndReturnGeneratedCode('24/6/2');
    $this->assertEqual($code, "<?php 24/6/2");
  }

  function testParenthesis()
  {
    $code = $this->_parseAndReturnGeneratedCode('8-(4-2)');
    $this->assertEqual($code, "<?php 8-(4-2)");
  }

  function testConstantPropertyFromContext()
  {
    $property = new WactConstantProperty('hello');

    $this->context->registerProperty('Test', $property);

    $expr = $this->parser->parse('Test');
    $this->assertIdentical($expr->getValue(),'hello');
  }

  function testConstantPropertyWithNewSyntax()
  {
    $property = new WactConstantProperty('hello');

    $this->root->registerProperty('Test', $property);

    $expr = $this->parser->parse(':Test');
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

    $expr = $this->parser->parse('^Test');
    $this->assertIdentical($expr->getValue(),'hello');
  }

  function testConstantPropertyFromParentContextWithNewSyntax()
  {
    $property = new WactConstantProperty('hello');

    $this->root->registerProperty('Test', $property);

    $expr = $this->parser->parse('^:Test');
    $this->assertIdentical($expr->getValue(),'hello');
  }

  function testLocalVariableModifier()
  {
    $expr = $this->parser->parse('$Test');

    $code_writer = new WactCodeWriter();

    $root = new WactCompileTreeRootNode();
    $context = new WactCompileTreeNode();
    $context->parent = $root;

    $expr->generatePreStatement($code_writer);
    $expr->generateExpression($code_writer);
    $expr->generatePostStatement($code_writer);

    $this->assertEqual($code_writer->getCode(), '<?php $Test');
  }
}


