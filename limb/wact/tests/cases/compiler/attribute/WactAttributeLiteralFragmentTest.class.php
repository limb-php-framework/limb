<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';
require_once 'limb/wact/src/compiler/attribute/WactAttributeLiteralFragment.class.php';

class WactAttributeLiteralFragmentTest extends UnitTestCase
{
  function testIsConstant()
  {
    $attribute =  new WactAttributeLiteralFragment('value');
    $this->assertTrue($attribute->isConstant());
  }

  function testGetValue()
  {
    $attribute = new WactAttributeLiteralFragment('value');
    $this->assertEqual($attribute->getValue(), 'value');
  }

  function testGetValueNull()
  {
    $Attribute = new WactAttributeLiteralFragment(NULL);
    $this->assertIdentical($Attribute->getValue(), NULL);
  }

  function testGenerateFragment()
  {
    $code_writer = new WactCodeWriter();
    $attribute =  new WactAttributeLiteralFragment('value');
    $attribute->generateFragment($code_writer);
    $this->assertEqual($code_writer->renderCode(), 'value');
  }

  function testGenerateExpression()
  {
    $code_writer = new WactCodeWriter();
    $attribute =  new WactAttributeLiteralFragment('value');
    $attribute->generatePreStatement($code_writer);
    $attribute->generateExpression($code_writer);
    $attribute->generatePostStatement($code_writer);
    $this->assertEqual($code_writer->renderCode(), "<?php 'value' ?>");
  }
}

