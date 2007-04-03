<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactTextNodeTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
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

?>

