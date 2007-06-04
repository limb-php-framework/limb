<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactCompileTreeRootNodeTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

require_once('limb/wact/tests/cases/compiler/compile_tree_node/WactCompileTreeNodeTest.class.php');

class WactCompileTreeRootNodeTest extends WactCompileTreeNodeTest
{
  function _createNode()
  {
    return new WactCompileTreeRootNode();
  }

  function testGetComponentRefCode()
  {
    $this->assertEqual($this->component->getComponentRefCode(), '$root');
  }

  function testGetDataSource()
  {
    $this->assertIsA($this->component->getDataSource(), 'WactCompileTreeRootNode');
  }
}
?>