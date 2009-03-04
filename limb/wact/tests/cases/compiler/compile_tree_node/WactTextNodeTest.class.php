<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once('limb/wact/tests/cases/compiler/compile_tree_node/WactCompileTreeNodeTest.class.php');

class WactTextNodeTest extends WactCompileTreeNodeTest
{
  function _createNode()
  {
    $location = new WactSourceLocation('my_file', 10);
    return new WactTextNode($location, 'test');
  }

  function testLocationPassed()
  {
    $this->assertEqual($this->component->getTemplateFile(), 'my_file');
    $this->assertEqual($this->component->getTemplateLine(), 10);
  }

  function testGenerate()
  {
    $component = new WactTextNode(null, 'test');

    $code_writer = new WactCodeWriter();
    $component->generate($code_writer);

    $this->assertEqual($code_writer->renderCode(), 'test');
  }

  function testGenerateWithChild()
  {
    $component = new WactTextNode(null, 'test');
    $child_component = new WactTextNode(null, 'test2');
    $component->addChild($child_component);

    $code_writer = new WactCodeWriter();
    $component->generate($code_writer);

    $this->assertEqual($code_writer->renderCode(), 'testtest2');
  }
}

