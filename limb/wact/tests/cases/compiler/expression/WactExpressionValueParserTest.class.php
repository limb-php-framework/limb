<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactExpressionValueParserTest.class.php 5168 2007-02-28 16:05:08Z serega $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/expression/WactExpressionValueParser.class.php';

class WactExpressionValueParserTest extends UnitTestCase
{
  function testIntegerZero()
  {
    $expression = '0';
    $parser = new WactExpressionValueParser($expression);
    $this->assertEqual($parser->getExpressionType(), WactExpressionValueParser :: INT);
    $this->assertIdentical($parser->getValue(), 0);
    $this->assertTrue($parser->isConstantValue());
  }

  function testInteger()
  {
    $expression = '23';
    $parser = new WactExpressionValueParser($expression);
    $this->assertEqual($parser->getExpressionType(), WactExpressionValueParser :: INT);
    $this->assertIdentical($parser->getValue(), 23);
  }

  function testFloat()
  {
    $expression = '23.35';
    $parser = new WactExpressionValueParser($expression);
    $this->assertEqual($parser->getExpressionType(), WactExpressionValueParser :: FLOAT);
    $this->assertIdentical($parser->getValue(), 23.35);
  }

  function testStringDoubleQuote()
  {
    $expression = '"Test"';
    $parser = new WactExpressionValueParser($expression);
    $this->assertEqual($parser->getExpressionType(), WactExpressionValueParser :: STRING);
    $this->assertIdentical($parser->getValue(), 'Test');
  }

  function testStringSingleQuote()
  {
    $expression = "'Test'";
    $parser = new WactExpressionValueParser($expression);
    $this->assertEqual($parser->getExpressionType(), WactExpressionValueParser :: STRING);
    $this->assertIdentical($parser->getValue(), 'Test');
  }

  function testDataBinding()
  {
    $expression = 'Variable';
    $parser = new WactExpressionValueParser($expression);
    $this->assertEqual($parser->getExpressionType(), WactExpressionValueParser :: DATABINDING);
    $this->assertIdentical($parser->getValue(), 'Variable');
  }
}
?>
