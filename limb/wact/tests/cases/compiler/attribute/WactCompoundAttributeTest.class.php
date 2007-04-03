<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactCompoundAttributeTest.class.php 5071 2007-02-16 09:09:35Z serega $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';
require_once 'limb/wact/src/compiler/attribute/WactCompoundAttribute.class.php';

Mock::Generate('WactAttributeExpression', 'MockWactAttributeExpression');
Mock::Generate('WactAttributeNode', 'MockWactAttributeNode');

class WactCompoundAttributeTest extends UnitTestCase
{
  protected $literal;
  protected $expression;
  protected $attribute;

  function setUp()
  {
    $this->literal = new MockWactAttributeNode();
    $this->expression = new MockWactAttributeExpression();

    $this->attribute = new WactCompoundAttribute('test');

    $this->attribute->addAttributeFragment($this->literal);
    $this->attribute->addAttributeFragment($this->expression);
  }

  function testIsConstantAllTrue()
  {
    $this->literal->setReturnValue('IsConstant', TRUE);
    $this->literal->expectOnce('IsConstant');
    $this->expression->setReturnValue('IsConstant', TRUE);
    $this->expression->expectOnce('IsConstant');
    $this->assertTrue($this->attribute->isConstant());
  }

  function testIsConstantAllFalse()
  {
    $this->literal->setReturnValue('IsConstant', FALSE);
    $this->literal->expectOnce('IsConstant');
    $this->expression->setReturnValue('IsConstant', FALSE);
    $this->expression->expectNever('IsConstant');
    $this->assertFalse($this->attribute->isConstant());
  }

  function testIsConstantMixed()
  {
    $this->literal->setReturnValue('IsConstant', TRUE);
    $this->literal->expectOnce('IsConstant');
    $this->expression->setReturnValue('IsConstant', FALSE);
    $this->expression->expectOnce('IsConstant');
    $this->assertFalse($this->attribute->isConstant());
  }

  function testGetValue()
  {
    $this->literal->setReturnValue('getValue', 'Hur');
    $this->literal->expectOnce('getValue');
    $this->expression->setReturnValue('getValue', 'rah');
    $this->expression->expectOnce('getValue');
    $this->assertEqual($this->attribute->getValue(), 'Hurrah');
  }

  function testGenerate()
  {
    $code_writer = new WactCodeWriter();

    $this->literal->expectOnce('generateFragment', array($code_writer));
    $this->expression->expectOnce('generateFragment', array($code_writer));

    $this->attribute->generate($code_writer);

    $this->assertEqual($code_writer->renderCode(), ' test=""');
  }

  function testGenerateExpression()
  {
    $code_writer = new WactCodeWriter();

    $this->literal->expectOnce('generatePreStatement', array($code_writer));
    $this->literal->expectOnce('generateExpression', array($code_writer));
    $this->literal->expectOnce('generatePostStatement', array($code_writer));

    $this->expression->expectOnce('generatePreStatement', array($code_writer));
    $this->expression->expectOnce('generateExpression', array($code_writer));
    $this->expression->expectOnce('generatePostStatement', array($code_writer));

    $this->attribute->generatePreStatement($code_writer);
    $this->attribute->generateExpression($code_writer);
    $this->attribute->generatePostStatement($code_writer);

    $this->assertEqual($code_writer->renderCode(), '<?php (.) ?>');
  }

  function testPrepare()
  {
    $this->literal->expectOnce('prepare');
    $this->expression->expectOnce('prepare');

    $this->attribute->prepare();
  }
}

?>
