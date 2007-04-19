<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMaterializedPathTreeTest.class.php 5645 2007-04-12 07:13:10Z pachanga $
 * @package    tree
 */
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');

abstract class lmbTreeTestBase extends UnitTestCase
{
  var $db = null;
  var $driver = null;

  abstract function _createTreeImp();
  abstract function _cleanUp();

  function setUp()
  {
    $toolkit = lmbToolkit :: instance();
    $this->db = new lmbSimpleDb($toolkit->getDefaultDbConnection());

    $this->imp = $this->_createTreeImp();

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function testGetNodeFailed()
  {
    $this->assertNull($this->imp->getNode(10000));
  }

  function testIsNodeFailed()
  {
    $this->assertFalse($this->imp->isNode(10000));
  }

  function testInitTree()
  {
    $id = $this->imp->initTree();
    $this->assertTrue($this->imp->isNode($id));
    $node = $this->imp->getNode($id);
    $this->assertEqual($node['id'], $id);
  }

  function testCreateTopNode()
  {
    $root_id = $this->imp->initTree();

    $node = array('identifier' => 'node_1');
    $node_id = $this->imp->createNode($node, $root_id);

    $new_node = $this->imp->getNode($node_id);
    $this->assertEqual($node['identifier'], $new_node['identifier']);
    $this->assertEqual($new_node['id'], $node_id);
    $this->assertEqual($new_node['parent_id'], $root_id);
  }

  /*
  to be continued
  */

  function _checkResultNodesArray($nodes, $line='')
  {
    if(isset($nodes['id']))//check for array
      $this->assertEqual($this->imp->getNode($nodes['id'])->export(), $nodes->export());
    else
      foreach($nodes as $node)
        $this->assertEqual($this->imp->getNode($node['id'])->export(), $node->export());
  }
}
?>
