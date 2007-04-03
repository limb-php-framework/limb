<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactAttributeExpressionTest.class.php 5071 2007-02-16 09:09:35Z serega $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';
require_once 'limb/wact/src/compiler/attribute/WactAttributeExpression.class.php';

class WactAttributeExpressionTest extends UnitTestCase
{
  protected $filter_dictionary;

  function setUp()
  {
    $this->filter_dictionary = new WactFilterDictionary();
  }

  function testIsConstant()
  {
    $component = new WactCompilerTag(null, null, null);
    $attribute = new WactAttributeExpression('test', '"hello"', $component, $this->filter_dictionary);

    $this->assertTrue($attribute->isConstant());
  }

  function testGetValue()
  {
    $component = new WactCompilerTag(null, null, null);
    $attribute = new WactAttributeExpression('test', '"hello"', $component, $this->filter_dictionary);

    $this->assertEqual($attribute->getValue(), 'hello');
  }

  function testGenerateConstantFragment()
  {
    $component = new WactCompilerTag(null, null, null);
    $attribute = new WactAttributeExpression('test', '"hello"', $component, $this->filter_dictionary);
    $code_writer = new WactCodeWriter();

    $attribute->generateFragment($code_writer);

    $this->assertEqual($code_writer->renderCode(), 'hello');
  }

  function testGenerateFragment()
  {
    $component = new WactCompilerTag(null, null, null);
    $property = new WactCompilerProperty();
    $component->registerProperty('hello', $property);
    $attribute = new WactAttributeExpression('test', 'hello', $component, $this->filter_dictionary);

    $code_writer = new WactCodeWriter(null, null, null);
    $attribute->generateFragment($code_writer);
    $this->assertEqual($code_writer->renderCode(), '<?php echo htmlspecialchars(, ENT_QUOTES); ?>');
  }

  function testGenerateForConstantValue()
  {
    $component = new WactCompilerTag(null, null, null);
    $attribute = new WactAttributeExpression('test', '"<hello"', $component, $this->filter_dictionary);

    $code_writer = new WactCodeWriter();
    $attribute->generate($code_writer);
    $this->assertEqual($code_writer->renderCode(), ' test="&lt;hello"');
  }

  function testGenerateForNonConstantValue()
  {
    $component = new WactCompilerTag(null, null, null);
    $property = new WactCompilerProperty();
    $component->registerProperty('hello', $property);
    $attribute = new WactAttributeExpression('test', 'hello', $component, $this->filter_dictionary);

    $code_writer = new WactCodeWriter();
    $attribute->generate($code_writer);
    $this->assertEqual($code_writer->renderCode(), ' test="<?php echo htmlspecialchars(, ENT_QUOTES); ?>"');
  }

  function testGenerateExpression()
  {
    $code_writer = new WactCodeWriter();

    $component = new WactCompilerTag(null, null, null);
    $property = new WactCompilerProperty();
    $component->registerProperty('hello', $property);
    $attribute = new WactAttributeExpression('test', 'hello', $component, $this->filter_dictionary);

    $attribute->generatePreStatement($code_writer);
    $attribute->generateExpression($code_writer);
    $attribute->generatePostStatement($code_writer);

    $this->assertEqual($code_writer->renderCode(), '');
  }
}
?>
