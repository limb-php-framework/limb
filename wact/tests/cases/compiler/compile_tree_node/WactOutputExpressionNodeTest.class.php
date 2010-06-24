<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/tests/cases/compiler/compile_tree_node/WactCompileTreeNodeTest.class.php');

Mock :: generate('WactExpression', 'MockExpression');

class WactOutputExpressionNodeTest extends WactCompileTreeNodeTest
{
  protected $expression;

  function _createNode()
  {
    $location = new WactSourceLocation('my_file', 10);
    $this->expression = new MockExpression();
    return new WactOutputExpressionNode($location, $this->expression);
  }

  function testLocationPassed()
  {
    $this->assertEqual($this->component->getTemplateFile(), 'my_file');
    $this->assertEqual($this->component->getTemplateLine(), 10);
  }

  // overwrite since generates not like usual compiler
  function testGenerate()
  {
  }

  function testGenerateWithDynamicExpression()
  {
    $code_writer = new WactCodeWriter();
    $this->expression->expectOnce('isConstant');
    $this->expression->setReturnValue('isConstant', false);
    $this->expression->expectOnce('generatePreStatement', array($code_writer));
    $this->expression->expectOnce('generateExpression', array($code_writer));
    $this->expression->expectOnce('generatePostStatement', array($code_writer));
    $this->component->generate($code_writer);
    $this->assertEqual($code_writer->renderCode(), '<?php echo ; ?>');
  }

  function testGenerateWithConstantExpression()
  {
    $code_writer = new WactCodeWriter();
    $this->expression->expectOnce('isConstant');
    $this->expression->setReturnValue('isConstant', true);
    $this->expression->setReturnValue('getValue', 'value');
    $this->component->generate($code_writer);
    $this->assertEqual($code_writer->renderCode(), 'value');
  }
}

