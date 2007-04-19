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

  function testInitTree()
  {
    $id = $this->imp->initTree();

    $rs = $this->db->select($this->_node_table);
    $rs->rewind();
    $this->assertEqual($rs->count(), 1);

    $root_node = $rs->current();
    $this->assertEqual($id, $root_node['id']);
  }

  function testGetRootNodeFailed()
  {
    $this->assertNull($this->imp->getRootNode());
  }

  function testGetRootNode()
  {
    $id = $this->imp->initTree();

    $root_node = $this->imp->getRootNode();
    $this->assertEqual($id, $root_node['id']);
  }

  function testGetNodeFailed()
  {
    $this->assertNull($this->imp->getNode(10000));
  }

  function testGetNode()
  {
    $id = $this->imp->initTree();

    $node = $this->imp->getNode($id);
    $this->assertEqual($node['id'], $id);
  }

  function testGetNodeByNode()
  {
    $id = $this->imp->initTree();

    $node = $this->imp->getNode($id);
    $sec_node = $this->imp->getNode($node);

    $this->assertEqual($node->export(), $sec_node->export());
  }

  function testGetNodeByIdsReturnsOrderedNodes()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $arr = $this->imp->getNodesByIds(array($node_2, $node_1, $root_id, $node_1_1));
    $this->assertEqual($arr[0]['id'], $root_id);
    $this->assertEqual($arr[1]['id'], $node_1);
    $this->assertEqual($arr[2]['id'], $node_1_1);
    $this->assertEqual($arr[3]['id'], $node_2);
  }

  function testIsNodeFailed()
  {
    $this->assertFalse($this->imp->isNode(10000));
  }

  function testIsNodeForRootNode()
  {
    $id = $this->imp->initTree();
    $this->assertTrue($this->imp->isNode($id));
  }

  function testGetParentFailed()
  {
    $this->assertNull($this->imp->getParent(1000));
  }

  function testGetParentFailedForRootNode()
  {
    $id = $this->imp->initTree();
    $this->assertNull($this->imp->getParent($id));
  }

  function testGetRootNodeByPath()
  {
    $id = $this->imp->initTree();
    $this->assertTrue($this->imp->isNode($id));
    $node = $this->imp->getNodeByPath('/');
    $this->assertEqual($node['id'], $id);
    $this->assertEqual($node['identifier'], '');
  }

  function testCreateTopNode()
  {
    $root_id = $this->imp->initTree();
    $node_id = $this->imp->createNode($root_id, array('identifier' => 'node_1'));

    $new_node = $this->imp->getNode($node_id);
    $this->assertEqual('node_1', $new_node['identifier']);
    $this->assertEqual($new_node['id'], $node_id);

    $parent_node = $this->imp->getParent($new_node);
    $this->assertEqual($parent_node['id'], $root_id);
  }

  function testCreateNode()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $node = $this->imp->getNode($node_1_1);
    $parent_node = $this->imp->getParent($node);

    $this->assertEqual($node_1_1, $node['id']);
    $this->assertEqual(2, count($this->imp->getParents($node)));
    $this->assertEqual($node_1, $parent_node['id']);
  }

  function testGetNodeByNodeReturnsSameNode()
  {
    $root_id = $this->imp->initTree();

    $node = array('identifier' => 'node_1');
    $new_node = $this->imp->getNode($node);
    $this->assertEqual($new_node, $node);
  }

  function testGetNodeByStringCallsGetNodeByPath()
  {
    $root_id = $this->imp->initTree();
    $node_id = $this->imp->createNode($root_id, array('identifier' => 'node_1'));
    $this->assertEqual($this->imp->getNode('/node_1')->export(),
                       $this->imp->getNode($node_id)->export());
  }

  function testGetPathToNodeFailed()
  {
    $this->assertNull($this->imp->getPathToNode(1000));
  }

  function testGetPathToRootNode()
  {
    $root_id = $this->imp->initTree();
    $this->assertEqual($this->imp->getPathToNode($root_id), '/');
  }

  function testGetPathToNode()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $this->assertEqual($this->imp->getPathToNode($node_1), '/node_1');
    $this->assertEqual($this->imp->getPathToNode($node_2), '/node_2');
    $this->assertEqual($this->imp->getPathToNode($node_1_1), '/node_1/node_1_1');
  }

  function testGetParent()
  {
    $root_id = $this->imp->initTree();
    $parent_node_id = $this->imp->createNode($root_id, array('identifier' => 'node_1'));
    $node_id = $this->imp->createNode($parent_node_id, array('identifier' => 'node_1_1'));

    $parent_node = $this->imp->getParent($node_id);
    $this->assertEqual($parent_node['id'], $parent_node_id);
    $this->assertEqual($parent_node['identifier'], 'node_1');
  }

  function testGetParentsFailed()
  {
    $this->assertNull($this->imp->getParents(1000));
  }

  function testGetRootParents()
  {
    $root_id = $this->imp->initTree();
    $this->assertNull($this->imp->getParents($root_id));
  }

  function testGetParents()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $arr = $this->imp->getParents($node_1);
    $this->assertEqual(sizeof($arr), 1);
    $this->assertEqual($arr[0]['id'], $root_id);

    $arr = $this->imp->getParents($node_1_1);
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]['id'], $root_id);
    $this->assertEqual($arr[1]['id'], $node_1);

    $arr = $this->imp->getParents($node_2);
    $this->assertEqual(sizeof($arr), 1);
    $this->assertEqual($arr[0]['id'], $root_id);
  }

  function testCountRootChildren()
  {
    $root_id = $this->imp->initTree();
    $this->assertEqual($this->imp->countChildren($root_id), 0);
  }

  function testCountChildren()
  {
    $root_id = $this->imp->initTree();
    $parent_node_id = $this->imp->createNode($root_id, array('identifier' => 'node_1'));
    $node_id = $this->imp->createNode($parent_node_id, array('identifier' => 'node_1_1'));

    $this->assertEqual($this->imp->countChildren($root_id), 1);
    $this->assertEqual($this->imp->countChildren($parent_node_id), 1);
  }

  function testCountAllRootChildren()
  {
    $root_id = $this->imp->initTree();
    $this->assertEqual($this->imp->countChildrenAll($root_id), 0);
  }

  function testCountAllChildren()
  {
    $root_id = $this->imp->initTree();
    $parent_node_id = $this->imp->createNode($root_id, array('identifier' => 'node_1'));
    $node_id = $this->imp->createNode($parent_node_id, array('identifier' => 'node_1_1'));

    $this->assertEqual($this->imp->countChildrenAll($root_id), 2);
    $this->assertEqual($this->imp->countChildrenAll($parent_node_id), 1);
  }

  function testDeleteNodeFailed()
  {
    $this->assertFalse($this->imp->deleteNode(100000));
  }

  function testDeleteNode()
  {
    $root_id = $this->imp->initTree();
    $parent_node_id = $this->imp->createNode($root_id, array('identifier' => 'parent'));
    $sub_node_id1 = $this->imp->createNode($parent_node_id, array('identifier' => 'test1'));
    $this->imp->createNode($sub_node_id1, array('identifier' => 'test1_1'));
    $sub_node_id2 = $this->imp->createNode($parent_node_id, array('identifier' => 'test2'));

    $this->imp->deleteNode($sub_node_id1);

    $this->assertEqual($this->imp->countChildrenAll('/'), 2);
  }

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
