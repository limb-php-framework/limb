<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';
require_once 'limb/wact/src/compiler/attribute/WactAttribute.class.php';

Mock::Generate('WactAttributeExpressionFragment', 'MockWactAttributeFragment');

class WactAttributeTest extends UnitTestCase
{
  protected $fragment1;
  protected $fragment2;
  protected $attribute;

  function setUp()
  {
    $this->fragment1 = new MockWactAttributeFragment();
    $this->fragment2 = new MockWactAttributeFragment();

    $this->attribute = new WactAttribute('test');

    $this->attribute->addFragment($this->fragment1);
    $this->attribute->addFragment($this->fragment2);
  }

  function testIsConstantAllTrue()
  {
    $this->fragment1->setReturnValue('IsConstant', TRUE);
    $this->fragment1->expectOnce('IsConstant');
    $this->fragment2->setReturnValue('IsConstant', TRUE);
    $this->fragment2->expectOnce('IsConstant');
    $this->assertTrue($this->attribute->isConstant());
  }

  function testIsConstantAllFalse()
  {
    $this->fragment1->setReturnValue('IsConstant', FALSE);
    $this->fragment1->expectOnce('IsConstant');
    $this->fragment2->setReturnValue('IsConstant', FALSE);
    $this->fragment2->expectNever('IsConstant');
    $this->assertFalse($this->attribute->isConstant());
  }

  function testIsConstantMixed()
  {
    $this->fragment1->setReturnValue('IsConstant', TRUE);
    $this->fragment1->expectOnce('IsConstant');
    $this->fragment2->setReturnValue('IsConstant', FALSE);
    $this->fragment2->expectOnce('IsConstant');
    $this->assertFalse($this->attribute->isConstant());
  }

  function testGetValue()
  {
    $this->fragment1->setReturnValue('getValue', 'Hur');
    $this->fragment1->expectOnce('getValue');
    $this->fragment2->setReturnValue('getValue', 'rah');
    $this->fragment2->expectOnce('getValue');
    $this->assertEqual($this->attribute->getValue(), 'Hurrah');
  }

  function testGenerate()
  {
    $code_writer = new WactCodeWriter();

    $this->fragment1->expectOnce('generateFragment', array($code_writer));
    $this->fragment2->expectOnce('generateFragment', array($code_writer));

    $this->attribute->generate($code_writer);

    $this->assertEqual($code_writer->renderCode(), ' test=""');
  }

  function testGenerateExpression()
  {
    $code_writer = new WactCodeWriter();

    $this->fragment1->expectOnce('generatePreStatement', array($code_writer));
    $this->fragment1->expectOnce('generateExpression', array($code_writer));
    $this->fragment1->expectOnce('generatePostStatement', array($code_writer));

    $this->fragment2->expectOnce('generatePreStatement', array($code_writer));
    $this->fragment2->expectOnce('generateExpression', array($code_writer));
    $this->fragment2->expectOnce('generatePostStatement', array($code_writer));

    $this->attribute->generatePreStatement($code_writer);
    $this->attribute->generateExpression($code_writer);
    $this->attribute->generatePostStatement($code_writer);

    $this->assertEqual($code_writer->renderCode(), '<?php (.) ?>');
  }

  function testPrepare()
  {
    $this->fragment1->expectOnce('prepare');
    $this->fragment2->expectOnce('prepare');

    $this->attribute->prepare();
  }
}


