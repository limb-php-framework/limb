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
lmb_require('limb/tree/src/tree/lmbNestedSetsTree.class.php');

class NestedSetsTreeTestVersion extends lmbNestedSetsTree
{
  function __construct()
  {
    parent :: __construct('test_nested_sets_tree');
  }
}

class lmbNestedSetsTreeTest extends UnitTestCase
{
  var $db = null;
  var $driver = null;

  function _createTreeImp()
  {
    return new NestedSetsTreeTestVersion();
  }

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

  function _cleanUp()
  {
    $this->db->delete('test_nested_sets_tree');
  }

  function testGetNodeFailed()
  {
    $this->assertNull($this->imp->getNode(10000));
  }
  
  function testGetNode()
  {
    $node = array(
      'identifier' => 'node_1',
      'id' => 2,
      'c_left' => 2,
      'c_right' => 3,
      'c_level' => 1,
    );
    $this->db->insert('test_nested_sets_tree', $node);

    $this->assertEqual($node, $this->imp->getNode(2)->export());
  }
  
  function testIsNodeFailed()
  {
    $this->assertFalse($this->imp->isNode(10000)); 
  }
  
  function testIsNode()
  {
    $node = array(
      'identifier' => 'node_1',
      'id' => 2,
      'c_left' => 2,
      'c_right' => 3,
      'c_level' => 1,
    );
    $this->db->insert('test_nested_sets_tree', $node);

    $this->assertEqual(true, $this->imp->isNode(2));
  }
  
  function testInitTree()
  {
    $root_node = array(
      'identifier' => 'root',
      'id' => 1,
      'c_left' => 1,
      'c_right' => 2,
      'c_level' => 0,
      );
    $id = $this->imp->initTree($root_node);
    $this->assertEqual($root_node['id'], $id);
    $this->assertEqual($root_node, $this->imp->getNode(1)->export());
  }
  
  function testInitTreeAutoGenerateId()
  {
    $root_node = array('identifier' => 'root');
    $id = $this->imp->initTree($root_node);
    
    $this->assertNotIdentical($id, false);
    
    $root_node['id']=$id;
    $root_node['c_left']=1;
    $root_node['c_right']=2;
    $root_node['c_level']=0;
    
    $this->assertEqual($root_node, $this->imp->getNode($id)->export());  
  }
  
  function testCreateTopNode()
  {
    $id = $this->imp->initTree(array('identifier' => 'root'));
    
    $node = array(
      'identifier' => 'node_1',
      'id' => 2,
      'c_left' => 2,
      'c_right' => 3,
      'c_level' => 1,
    );
    $node_id = $this->imp->createNode($node);
    
    $this->assertEqual($node, $this->imp->getNode($node_id)->export());
  }
  
  function testCreateNodes()
  {
    $this->imp->initTree(array('identifier' => 'root'));
    $id = $this->imp->createNode(array('identifier' => 'node_1'));
    $this->imp->createNode(array('identifier' => 'node_2'));
    
    $node = array(
      'identifier' => 'node_1_1',
      'id' => 4,
      'c_left' => 3,
      'c_right' => 4,
      'c_level' => 2,
    );
    $node_id = $this->imp->createNode($node, $id);
    
    $this->assertEqual($node, $this->imp->getNode($node_id)->export());
  }
  
  function testUpdateNode()
  {
    $this->imp->initTree(array('identifier' => 'root'));
    $id = $this->imp->createNode(array('identifier' => 'node_1'));
    
    $values = array(
      'identifier' => 'node_1_updated',
      'id' => 4,
      'c_left' => 3,
      'c_right' => 4,
      'c_level' => 2,
    );
    $this->imp->updateNode($id, $values);
    
    $expected_result = array(
      'identifier' => 'node_1_updated',
      'id' => $id,
      'c_left' => 2,
      'c_right' => 3,
      'c_level' => 1,
    );
    
    $this->assertEqual($expected_result, $this->imp->getNode($id)->export());
  }
  
  
  function testDeleteNode()
  {
    $this->imp->initTree(array('identifier' => 'root'));
    $id = $this->imp->createNode(array('identifier' => 'node_1'));
    $this->imp->createNode(array('identifier' => 'node_2'));
    $this->imp->createNode(array('identifier' => 'node_1_1'), $id);
    
    $this->imp->deleteNode($this->imp->getNode($id));
    
    $rs = $this->db->select('test_nested_sets_tree');
    $this->assertEqual($rs->count(), 2);      
  }
  
  function testGetParentFailed()
  {
    $root_id = $this->imp->initTree(array('identifier' => 'root'));
    $id = $this->imp->createNode(array('identifier' => 'node_1'));

    $this->assertNull($this->imp->getParent(10000));
    $this->assertNull($this->imp->getParent($root_id));      
  }
  
  function testGetParent()
  {
    $this->imp->initTree(array('identifier' => 'root'));
    $id = $this->imp->createNode(array('identifier' => 'node_1'));
    $this->imp->createNode(array('identifier' => 'node_2'));
    $node_id = $this->imp->createNode(array('identifier' => 'node_1_1'), $id);
    
    $parent = $this->imp->getParent($node_id);
    
    $this->assertEqual($this->imp->getNode($id)->export(), $parent->export());      
  }
  
  function testGetParentsFailed()
  {
    $root_id = $this->imp->initTree(array('identifier' => 'root'));
    $id = $this->imp->createNode(array('identifier' => 'node_1'));

    $this->assertNull($this->imp->getParents(10000));
    $this->assertNull($this->imp->getParents($root_id));      
  }
  
  function testGetParents()
  {
    $this->imp->initTree(array('identifier' => 'root'));
    $id = $this->imp->createNode(array('identifier' => 'node_1'));
    $node_id = $this->imp->createNode(array('identifier' => 'node_1_1'), $id);
    $child_id = $this->imp->createNode(array('identifier' => 'node_1_1_1'), $node_id);
    
    $parents = $this->imp->getParents($child_id);
    
    $this->assertEqual(2, $parents->count());      
  }
  
  function testGetChildrenFailed()
  {
    $this->assertNull($this->imp->getChildren(10000));
  }
  
  function testGetChildren()
  {
    $this->imp->initTree(array('identifier' => 'root'));
    $id = $this->imp->createNode(array('identifier' => 'node_1'));
    $node_id = $this->imp->createNode(array('identifier' => 'node_1_1'), $id);
    $child_id = $this->imp->createNode(array('identifier' => 'node_1_1_1'), $node_id);
    $this->imp->createNode(array('identifier' => 'node_1_2'), $id);
    
    $children = $this->imp->getChildren($id);
    
    $this->assertEqual(2, $children->count());      
  }

  function testGetChildrenDepth()
  {
    $root_id = $this->imp->initTree(array('identifier' => 'root'));
    $sub_node_id_1 = $this->imp->createNode(array('identifier' => 'test', $root_id));
    $sub_node_id_1_1 = $this->imp->createNode(array('identifier' => 'test'), $sub_node_id_1);
    $sub_node_id_1_1_1 = $this->imp->createNode(array('identifier' => 'test'), $sub_node_id_1_1);
    $sub_node_id_1_1_2 = $this->imp->createNode(array('identifier' => 'test'), $sub_node_id_1_1);

    //getting all
    $rs = $this->imp->getChildren($sub_node_id_1, -1);
    $branch = $rs->getArray('id');
    $this->assertEqual(3, sizeof($branch));


    $node = current($branch);
    $this->assertEqual($node['id'], $sub_node_id_1_1, '%s, invalid parameter: id');


    //getting at depth = 1
    $rs = $this->imp->getChildren($sub_node_id_1, 1);
    $branch = $rs->getArray('id');
    $this->assertEqual(1, sizeof($branch));
  }

   
  function testGetChildrenAllFailed()
  {
    $this->assertNull($this->imp->getChildrenAll(10000));
  }
  
  
  function testGetChildrenAll()
  {
    $this->imp->initTree(array('identifier' => 'root'));
    $id = $this->imp->createNode(array('identifier' => 'node_1'));
    $node_id = $this->imp->createNode(array('identifier' => 'node_1_1'), $id);
    $child_id = $this->imp->createNode(array('identifier' => 'node_1_1_1'), $node_id);
    $this->imp->createNode(array('identifier' => 'node_1_2'), $id);
    
    $children = $this->imp->getChildrenAll($id);
    
    $this->assertEqual(3, $children->count());      
  }
  
  function testCountChildrenFailed()
  {
    $this->assertNull($this->imp->countChildren(10000));
  }
  
  function testCountChildren()
  {
    $this->imp->initTree(array('identifier' => 'root'));
    $id = $this->imp->createNode(array('identifier' => 'node_1'));
    $node_id = $this->imp->createNode(array('identifier' => 'node_1_1'), $id);
    $child_id = $this->imp->createNode(array('identifier' => 'node_1_1_1'), $node_id);
    $this->imp->createNode(array('identifier' => 'node_1_2'), $id);
    
    $children = $this->imp->countChildren($id);
    $this->assertEqual(2, $children);
    
    $children = $this->imp->countChildren($id, 2);
    $this->assertEqual(3, $children);            
  }
  
  function testCountChildrenDepth()
  {
    $this->imp->initTree(array('identifier' => 'root'));
    $id = $this->imp->createNode(array('identifier' => 'node_1'));
    $node_id = $this->imp->createNode(array('identifier' => 'node_1_1'), $id);
    $child_id = $this->imp->createNode(array('identifier' => 'node_1_1_1'), $node_id);
    $this->imp->createNode(array('identifier' => 'node_1_2'), $id);
    
    $children = $this->imp->countChildren($id, 1);
    $this->assertEqual(2, $children);
    
    $children = $this->imp->countChildren($id, 2);
    $this->assertEqual(3, $children);
    
    $children = $this->imp->countChildren($id, -1);
    $this->assertEqual(3, $children);            
  }
 
  function testCountChildrenAllFailed()
  {
    $this->assertNull($this->imp->countChildrenAll(10000));
  }
  
  function testCountChildrenAll()
  {
    $this->imp->initTree(array('identifier' => 'root'));
    $id = $this->imp->createNode(array('identifier' => 'node_1'));
    $node_id = $this->imp->createNode(array('identifier' => 'node_1_1'), $id);
    $child_id = $this->imp->createNode(array('identifier' => 'node_1_1_1'), $node_id);
    $this->imp->createNode(array('identifier' => 'node_1_2'), $id);
    
    $children = $this->imp->countChildrenAll($id);
    
    $this->assertEqual(3, $children);      
  }
  
  function testGetSiblingsFailed()
  {
    $this->assertNull($this->imp->getSiblings(10000));  
  }
  
  function testGetSiblings()
  {
    $this->imp->initTree(array('identifier' => 'root'));
    $id = $this->imp->createNode(array('identifier' => 'node_1'));
    $node_id = $this->imp->createNode(array('identifier' => 'node_1_1'), $id);
    $child_id = $this->imp->createNode(array('identifier' => 'node_1_1_1'), $node_id);
    $this->imp->createNode(array('identifier' => 'node_1_2'), $id);
    $this->imp->createNode(array('identifier' => 'node_1_3'), $id);
    
    $siblings = $this->imp->getSiblings($node_id);
    
    $this->assertEqual(3, $siblings->count());      
  }
  
  function testGetNodesByIds()
  {
    $this->imp->initTree(array('identifier' => 'root'));
    $id = $this->imp->createNode(array('identifier' => 'node_1'));
    $node_id = $this->imp->createNode(array('identifier' => 'node_1_1'), $id);
    $child_id = $this->imp->createNode(array('identifier' => 'node_1_1_1'), $node_id);

    $nodes = $this->imp->getNodesByIds(
      array(
        $id,
        $child_id,
        -1
      )
    );

    $this->assertEqual($nodes->count(), 2);


    $rs = $this->imp->getNodesByIds(array());

    $nodes = $rs->getArray();

    $this->assertEqual(sizeof($nodes), 0);
  }
  



  function testMoveNodeFromAnotherBrunchUp()
  {
    $this->imp->initTree(array('identifier' => 'root'));
    $id = $this->imp->createNode(array('identifier' => 'node_1'));
    $source_id = $this->imp->createNode(array('identifier' => 'node_2'));
    $node_id = $this->imp->createNode(array('identifier' => 'node_1_1'), $id);

    
    $this->imp->moveNode($source_id, $id);
    
    $moved_node = array(
      'identifier' => 'node_2',
      'id' => $source_id,
      'c_left' => 5,
      'c_right' => 6,
      'c_level' => 2,
    );
    
    $this->assertEqual($moved_node, $this->imp->getNode($source_id)->export());
  }
  
  function testMoveNodeFromAnotherBrunchDown()
  {
    $this->imp->initTree(array('identifier' => 'root'));
    $source_id = $this->imp->createNode(array('identifier' => 'node_1'));
    $dest_id = $this->imp->createNode(array('identifier' => 'node_2'));
    $node_id = $this->imp->createNode(array('identifier' => 'node_1_1'), $source_id);

    
    $this->imp->moveNode($source_id, $dest_id);
    
    $moved_node = array(
      'identifier' => 'node_1',
      'id' => $source_id,
      'c_left' => 3,
      'c_right' => 6,
      'c_level' => 2,
    );
    
    $this->assertEqual($moved_node, $this->imp->getNode($source_id)->export());
  }
  
  function testMoveNodeUpwardByPath()
  {
    $this->imp->initTree(array('identifier' => 'root'));
    $id = $this->imp->createNode(array('identifier' => 'node_1'));
    $this->imp->createNode(array('identifier' => 'node_2'));
    $node_id = $this->imp->createNode(array('identifier' => 'node_1_1'), $id);
    $source_id = $this->imp->createNode(array('identifier' => 'node_1_1_1'), $node_id);
    
    $this->imp->moveNode($source_id, $id);
    
    $moved_node = array(
      'identifier' => 'node_1_1_1',
      'id' => 5,
      'c_left' => 5,
      'c_right' => 6,
      'c_level' => 2,
    );
    
    $this->assertEqual($moved_node, $this->imp->getNode($source_id)->export());
  }
  
  function testGetNodeByPathFailed()
  {
    $this->imp->initTree(array('identifier' => '/'));
    $root_id = $this->imp->createNode(array('identifier' => 'root'));
    $sub_node_id_1 = $this->imp->createNode(array('identifier' => '1_test'), $root_id);
    $sub_node_id_1_1 = $this->imp->createNode(array('identifier' => '1_1_test'), $sub_node_id_1);
    $sub_node_id_1_1_1 = $this->imp->createNode(array('identifier' => '1_1_1_test'), $sub_node_id_1_1);
    $sub_node_id_1_1_2 = $this->imp->createNode(array('identifier' => '1_1_2_test'), $sub_node_id_1_1);

    $root_id2 = $this->imp->createNode(array('identifier' => 'root2'));
    $sub_node_id_2 = $this->imp->createNode(array('identifier' => '2_test'), $root_id2);

    $this->assertFalse($this->imp->getNodeByPath(''));
    $this->assertFalse($this->imp->getNodeByPath('/root///'));
    $this->assertFalse($this->imp->getNodeByPath('/root/wow/yo'));
    $this->assertFalse($this->imp->getNodeByPath('/root/2_test'));
  }

  function testGetNodeByPath()
  {
    $this->imp->initTree(array('identifier' => '/'));
    $root_id = $this->imp->createNode(array('identifier' => 'root'));
    $sub_node_id_1 = $this->imp->createNode(array('identifier' => 'test1'), $root_id);
    $sub_node_id_1_1 = $this->imp->createNode(array('identifier' => 'test1'), $sub_node_id_1);
    $sub_node_id_1_1_1 = $this->imp->createNode(array('identifier' => 'test1'), $sub_node_id_1_1);
    $sub_node_id_1_1_2 = $this->imp->createNode(array('identifier' => 'test2'), $sub_node_id_1_1);

    $node = $this->imp->getNodeByPath('/root/');
    $this->assertEqual($node['id'], $root_id);
    $this->_checkResultNodesArray($node);

    $node = $this->imp->getNodeByPath('/root/test1/test1/');
    $this->assertEqual($node['id'], $sub_node_id_1_1);
    $this->_checkResultNodesArray($node,  __LINE__);

    $node = $this->imp->getNodeByPath('/root/test1/test1/test2');
    $this->assertEqual($node['id'], $sub_node_id_1_1_2);
    $this->_checkResultNodesArray($node,  __LINE__);

    $node = $this->imp->getNodeByPath('/root/test1/test1/test1/');
    $this->assertEqual($node['id'], $sub_node_id_1_1_1);
    $this->_checkResultNodesArray($node,  __LINE__);
  }
  
  function testGetPathToNode()
  {
    $this->imp->initTree(array('identifier' => '/'));
    $root_id = $this->imp->createNode(array('identifier' => 'root'));
    $sub_node_id_1 = $this->imp->createNode(array('identifier' => 'test1'), $root_id);
    $sub_node_id_1_1 = $this->imp->createNode(array('identifier' => 'test1'), $sub_node_id_1);
    $sub_node_id_1_1_1 = $this->imp->createNode(array('identifier' => 'test1'), $sub_node_id_1_1);
    $sub_node_id_1_1_2 = $this->imp->createNode(array('identifier' => 'test2'), $sub_node_id_1_1);

    $path = $this->imp->getPathToNode($root_id, '|');
    $this->assertEqual($path, '|root');

    $path = $this->imp->getPathToNode($sub_node_id_1_1);
    $this->assertEqual($path, '/root/test1/test1');

    $path = $this->imp->getPathToNode($sub_node_id_1_1_2, '|');
    $this->assertEqual($path, '|root|test1|test1|test2');

    $path = $this->imp->getPathToNode(-1000000);
    $this->assertNull($path);
  }
  
  function testMoveNodeUpDown()
  {
    $this->imp->initTree(array('identifier' => 'root'));
    $id = $this->imp->createNode(array('identifier' => 'node_1'));
    $node = $this->imp->createNode(array('identifier' => 'node_2'));
    $node_id = $this->imp->createNode(array('identifier' => 'node_1_1'), $id);
    $source_id = $this->imp->createNode(array('identifier' => 'node_1_1_1'), $node_id);
    
    $this->imp->moveNodeUp($node);
    
    $moved_node = array(
      'identifier' => 'node_2',
      'id' => $node,
      'c_left' => 2,
      'c_right' => 3,
      'c_level' => 1,
    );
    
    $this->assertEqual($this->imp->getNode($node)->export(), $moved_node);
    
    $this->imp->moveNodeDown($node);
    
    $moved_node = array(
      'identifier' => 'node_2',
      'id' => $node,
      'c_left' => 8,
      'c_right' => 9,
      'c_level' => 1,
    );
    
    $this->assertEqual($this->imp->getNode($node)->export(), $moved_node);
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
