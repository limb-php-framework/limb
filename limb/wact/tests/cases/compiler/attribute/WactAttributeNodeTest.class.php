<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactAttributeNodeTest.class.php 5071 2007-02-16 09:09:35Z serega $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';
require_once 'limb/wact/src/compiler/attribute/WactAttributeNode.class.php';

class WactAttributeNodeTest extends UnitTestCase
{
  function testIsConstant()
  {
    $attribute =  new WactAttributeNode('test', 'value');
    $this->assertTrue($attribute->isConstant());
  }

  function testGetValue()
  {
    $attribute = new WactAttributeNode('test', 'value');
    $this->assertEqual($attribute->getValue(), 'value');
  }

  function testGetValueNull()
  {
    $Attribute = new WactAttributeNode('test', NULL);
    $this->assertIdentical($Attribute->getValue(), NULL);
  }

  function testGenerateFragment()
  {
    $code_writer = new WactCodeWriter();
    $attribute =  new WactAttributeNode('test', 'value');
    $attribute->generateFragment($code_writer);
    $this->assertEqual($code_writer->renderCode(), 'value');
  }

  function testGenerate()
  {
    $code_writer = new WactCodeWriter();
    $attribute =  new WactAttributeNode('test', 'value');
    $attribute->generate($code_writer);
    $this->assertEqual($code_writer->renderCode(), ' test="value"');
  }

  function testGenerateExpression()
  {
    $code_writer = new WactCodeWriter();
    $attribute =  new WactAttributeNode('test', 'value');
    $attribute->generatePreStatement($code_writer);
    $attribute->generateExpression($code_writer);
    $attribute->generatePostStatement($code_writer);
    $this->assertEqual($code_writer->renderCode(), "<?php 'value' ?>");
  }
}

?>
