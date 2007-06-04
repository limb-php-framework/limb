<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactPHPNodeTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

require_once('limb/wact/tests/cases/compiler/compile_tree_node/WactCompileTreeNodeTest.class.php');

class WactPHPNodeTest extends WactCompileTreeNodeTest
{
  function _createNode()
  {
    $location = new WactSourceLocation('my_file', 10);
    return new WactPHPNode($location, 'test');
  }

  function testLocationPassed()
  {
    $this->assertEqual($this->component->getTemplateFile(), 'my_file');
    $this->assertEqual($this->component->getTemplateLine(), 10);
  }

  function testGenerate()
  {
    $code_writer = new WactCodeWriter();
    $this->component->generate($code_writer);
    $this->assertEqual($code_writer->renderCode(), '<?php test ?>');
  }
}
?>
