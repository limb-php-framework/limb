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
lmb_require('limb/tree/src/tree/lmbTreeException.class.php');
lmb_require('limb/tree/src/tree/lmbInvalidNodeTreeException.class.php');
lmb_require('limb/tree/src/tree/lmbConsistencyTreeException.class.php');

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
    $node = $this->imp->getRootNode();
    $this->assertEqual($id, $node['id']);
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

  function testNodeContainsParentId()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));

    $node = $this->imp->getNode($node_1);
    $this->assertEqual($node['id'], $node_1);
    $this->assertEqual($node['parent_id'], $root_id);
  }

  function testNodeContainsLevel()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));

    $node = $this->imp->getNode($root_id);
    $this->assertEqual($node['level'], 0);

    $node = $this->imp->getNode($node_1);
    $this->assertEqual($node['level'], 1);
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
    $this->assertFalse($this->imp->isNode('/'));
  }

  function testIsNodeForRootNode()
  {
    $id = $this->imp->initTree();
    $this->assertTrue($this->imp->isNode($id));
    $this->assertTrue($this->imp->isNode('/'));
  }

  function testIsNode()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $this->assertTrue($this->imp->isNode($root_id));
    $this->assertTrue($this->imp->isNode($node_1));
    $this->assertTrue($this->imp->isNode($node_2));
    $this->assertTrue($this->imp->isNode($node_1_1));
  }

  function testIsNodeByPath()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $this->assertTrue($this->imp->isNode('/'));
    $this->assertTrue($this->imp->isNode('/node_1'));
    $this->assertTrue($this->imp->isNode('/node_2'));
    $this->assertTrue($this->imp->isNode('/node_1/node_1_1'));
    $this->assertFalse($this->imp->isNode('/node_1_1'));
  }

  function testGetParentFailed()
  {
    try
    {
      $this->imp->getParent(1000);
      $this->assertTrue(false);
    }
    catch(lmbInvalidNodeTreeException $e){}
  }

  function testGetParentReturnsNullForRootNode()
  {
    $id = $this->imp->initTree();
    $this->assertNull($this->imp->getParent($id));
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

  function testGetParentByPath()
  {
    $root_id = $this->imp->initTree();
    $parent_node_id = $this->imp->createNode($root_id, array('identifier' => 'node_1'));
    $node_id = $this->imp->createNode($parent_node_id, array('identifier' => 'node_1_1'));

    $parent_node = $this->imp->getParent('/node_1/node_1_1');
    $this->assertEqual($parent_node['id'], $parent_node_id);
    $this->assertEqual($parent_node['identifier'], 'node_1');
  }

  function testCreateNodeThrowsInvalidNodeException()
  {
    try
    {
      $this->imp->createNode(100, array('identifier'=>'node_1'));
      $this->assertTrue(false);
    }
    catch(lmbInvalidNodeTreeException $e){}
  }

  function testCreateNodeThrowsConsistencyException()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));

    try
    {
      $this->imp->createNode($root_id, array('identifier'=>'node_2'));
      $this->assertTrue(false);
    }
    catch(lmbConsistencyTreeException $e){}
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

  function testUpdateNodeFailed()
  {
    try
    {
      $this->imp->updateNode(1000, array('junk'));
      $this->assertTrue(false);
    }
    catch(lmbInvalidNodeTreeException $e){}
  }

  function testUpdateNodeFailedWithDuplicateIdentifier()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));

    try
    {
      $this->imp->updateNode($node_1, array('identifier' => 'node_2'));
      $this->assertTrue(false);
    }
    catch(lmbConsistencyTreeException $e){}
  }

  function testGetNodeByInvalidArray()
  {
    $this->assertNull($this->imp->getNode(array('identifier' => 'node_1')));
  }

  function testGetNodeByArrayWithId()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node = $this->imp->getNode(array('id' => $node_1));
    $this->assertEqual($node['id'], $node_1);
    $this->assertEqual($node['identifier'], 'node_1');
  }

  function testGetNodeByInvalidObject()
  {
    $obj = new lmbSet();
    $this->assertNull($this->imp->getNode($obj));
  }

  function testGetNodeByObjectWithId()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node = $this->imp->getNode(new lmbSet(array('id' => $node_1)));
    $this->assertEqual($node['id'], $node_1);
    $this->assertEqual($node['identifier'], 'node_1');
  }

  function testGetNodeByStringCallsGetNodeByPath()
  {
    $root_id = $this->imp->initTree();
    $node_id = $this->imp->createNode($root_id, array('identifier' => 'node_1'));
    $this->assertEqual($this->imp->getNode('/node_1')->export(),
                       $this->imp->getNode($node_id)->export());
  }

  function testGetNodeByInvalidPath()
  {
    $this->assertNull($this->imp->getNodeByPath('/blah'));
    $this->assertNull($this->imp->getNodeByPath('/\'\''));
    $this->assertNull($this->imp->getNodeByPath('/""'));
  }

  function testGetRootNodeByPath()
  {
    $id = $this->imp->initTree();
    $node = $this->imp->getNodeByPath('/');
    $this->assertEqual($node['id'], $id);
    $this->assertEqual($node['identifier'], '');
  }

  function testGetNodeByPathWithExcessiveSlashes()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $node = $this->imp->getNodeByPath('////');
    $this->assertEqual($node['id'], $root_id);

    $node = $this->imp->getNodeByPath('/node_1///');
    $this->assertEqual($node['id'], $node_1);

    $node = $this->imp->getNodeByPath('//node_2///');
    $this->assertEqual($node['id'], $node_2);

    $node = $this->imp->getNodeByPath('//node_1//node_1_1//');
    $this->assertEqual($node['id'], $node_1_1);
  }

  function testGetNodeByPath()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $node = $this->imp->getNodeByPath('/');
    $this->assertEqual($node['id'], $root_id);

    $node = $this->imp->getNodeByPath('/node_1');
    $this->assertEqual($node['id'], $node_1);

    $this->assertNull($this->imp->getNodeByPath('node_1'));

    $node = $this->imp->getNodeByPath('/node_2');
    $this->assertEqual($node['id'], $node_2);

    $node = $this->imp->getNodeByPath('/node_1/node_1_1');
    $this->assertEqual($node['id'], $node_1_1);
  }

  function testGetPathToNodeFailed()
  {
    try
    {
      $this->assertNull($this->imp->getPathToNode(1000));
      $this->assertTrue(false);
    }
    catch(lmbInvalidNodeTreeException $e){}
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
    $node_1_1_1 = $this->imp->createNode($node_1_1, array('identifier'=>'node_1_1_1'));

    $this->assertEqual($this->imp->getPathToNode($node_1), '/node_1');
    $this->assertEqual($this->imp->getPathToNode($node_2), '/node_2');
    $this->assertEqual($this->imp->getPathToNode($node_1_1), '/node_1/node_1_1');
    $this->assertEqual($this->imp->getPathToNode($node_1_1_1), '/node_1/node_1_1/node_1_1_1');
  }

  function testGetParentsFailed()
  {
    try
    {
      $this->assertNull($this->imp->getParents(1000));
      $this->assertTrue(false);
    }
    catch(lmbInvalidNodeTreeException $e){}
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

  function testGetSiblingsFailed()
  {
    try
    {
      $this->assertNull($this->imp->getSiblings(1000));
      $this->assertTrue(false);
    }
    catch(lmbInvalidNodeTreeException $e){}
  }

  function testGetRootSiblings()
  {
    $root_id = $this->imp->initTree();
    $arr = $this->imp->getSiblings($root_id);
    $this->assertEqual(sizeof($arr), 1);
    $this->assertEqual($arr[0]['id'], $root_id);
  }

  function testGetSiblings()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $arr = $this->imp->getSiblings($node_1);
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]['id'], $node_1);
    $this->assertEqual($arr[1]['id'], $node_2);
  }

  function testCountChildrenFailed()
  {
    try
    {
      $this->assertEqual($this->imp->countChildren(1000), 0);
      $this->assertTrue(false);
    }
    catch(lmbInvalidNodeTreeException $e){}
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

  function testCountChildrenWithSmallDepth()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));
    $node_1_1_1 = $this->imp->createNode($node_1_1, array('identifier'=>'node_1_1_1'));

    $this->assertEqual($this->imp->countChildren($root_id, 2), 3);
  }

  function testCountChildrenWithLargeDepth()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));
    $node_1_1_1 = $this->imp->createNode($node_1_1, array('identifier'=>'node_1_1_1'));

    $this->assertEqual($this->imp->countChildren($root_id, 10), 4);
  }

  function testCountChildrenWithInfiniteDepth()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));
    $node_1_1_1 = $this->imp->createNode($node_1_1, array('identifier'=>'node_1_1_1'));

    $this->assertEqual($this->imp->countChildren($root_id, -1), 4);
  }

  function testCountAllChildrenFailed()
  {
    try
    {
      $this->imp->countChildrenAll(1000);
      $this->assertTrue(false);
    }
    catch(lmbInvalidNodeTreeException $e){}
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

  function testGetChildrenFailed()
  {
    try
    {
      $this->assertNull($this->imp->getChildren(1000));
      $this->assertTrue(false);
    }
    catch(lmbInvalidNodeTreeException $e){}
  }

  function testGetRootChildren()
  {
    $root_id = $this->imp->initTree();
    $this->assertEqual($this->imp->getChildren($root_id)->count(), 0);
  }

  function testGetChildren()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $arr = $this->imp->getChildren($root_id);
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]['id'], $node_1);
    $this->assertEqual($arr[1]['id'], $node_2);

    $arr = $this->imp->getChildren($node_1);
    $this->assertEqual(sizeof($arr), 1);
    $this->assertEqual($arr[0]['id'], $node_1_1);

    $arr = $this->imp->getChildren($node_2);
    $this->assertEqual(sizeof($arr), 0);

    $arr = $this->imp->getChildren($node_1_1);
    $this->assertEqual(sizeof($arr), 0);
  }

  function testGetChildrenWithSmallDepth()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));
    $node_1_1_1 = $this->imp->createNode($node_1_1, array('identifier'=>'node_1_1_1'));

    $arr = $this->imp->getChildren($root_id, 2);
    $this->assertEqual(sizeof($arr), 3);
    $this->assertEqual($arr[0]['id'], $node_1);
    $this->assertEqual($arr[1]['id'], $node_1_1);
    $this->assertEqual($arr[2]['id'], $node_2);
  }

  function testGetChildrenWithLargeDepth()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $arr = $this->imp->getChildren($root_id, 10);
    $this->assertEqual(sizeof($arr), 3);
    $this->assertEqual($arr[0]['id'], $node_1);
    $this->assertEqual($arr[1]['id'], $node_1_1);
    $this->assertEqual($arr[2]['id'], $node_2);
  }

  function testGetChildrenWithInfiniteDepth()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $arr = $this->imp->getChildren($root_id, -1);
    $this->assertEqual(sizeof($arr), 3);
    $this->assertEqual($arr[0]['id'], $node_1);
    $this->assertEqual($arr[1]['id'], $node_1_1);
    $this->assertEqual($arr[2]['id'], $node_2);
  }

  function testGetChildrenAll()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));
    $node_1_1_1 = $this->imp->createNode($node_1_1, array('identifier'=>'node_1_1_1'));

    $arr = $this->imp->getChildrenAll($root_id);
    $this->assertEqual(sizeof($arr), 4);
    $this->assertEqual($arr[0]['id'], $node_1);
    $this->assertEqual($arr[1]['id'], $node_1_1);
    $this->assertEqual($arr[2]['id'], $node_1_1_1);
    $this->assertEqual($arr[3]['id'], $node_2);

    $arr = $this->imp->getChildrenAll($node_1);
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]['id'], $node_1_1);
    $this->assertEqual($arr[1]['id'], $node_1_1_1);

    $arr = $this->imp->getChildrenAll($node_1_1);
    $this->assertEqual(sizeof($arr), 1);
    $this->assertEqual($arr[0]['id'], $node_1_1_1);

    $arr = $this->imp->getChildrenAll($node_1_1_1);
    $this->assertEqual(sizeof($arr), 0);

    $arr = $this->imp->getChildrenAll($node_2);
    $this->assertEqual(sizeof($arr), 0);
  }

  function testDeleteNodeFailed()
  {
    try
    {
      $this->assertFalse($this->imp->deleteNode(100000));
      $this->assertTrue(false);
    }
    catch(lmbInvalidNodeTreeException $e){}
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

  function testMoveNodeFailed()
  {
    try
    {
      $this->assertFalse($this->imp->moveNode(100, 1000));
      $this->assertTrue(false);
    }
    catch(lmbInvalidNodeTreeException $e){}
  }

  function testMoveRootNodeOnItselfFailed()
  {
    $root_id = $this->imp->initTree();
    try
    {
      $this->assertFalse($this->imp->moveNode($root_id, $root_id));
      $this->assertTrue(false);
    }
    catch(lmbTreeException $e){}
  }

  function testMoveRootNodeFailed()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));

    $this->assertFalse($this->imp->moveNode($root_id, $node_1));
    $this->assertFalse($this->imp->moveNode($root_id, $node_2));
  }

  function testMoveParentNodeToChildFailed()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));

    $this->assertFalse($this->imp->moveNode($node_1, $node_1_1));
  }

  function testMoveNode()
  {
    $root_id = $this->imp->initTree();
    $node_1 = $this->imp->createNode($root_id, array('identifier'=>'node_1'));
    $node_2 = $this->imp->createNode($root_id, array('identifier'=>'node_2'));
    $node_1_1 = $this->imp->createNode($node_1, array('identifier'=>'node_1_1'));
    $node_1_1_1 = $this->imp->createNode($node_1_1, array('identifier'=>'node_1_1_1'));

    $this->imp->moveNode($node_1_1, $node_2);

    $arr = $this->imp->getChildrenAll($root_id);
    $this->assertEqual(sizeof($arr), 4);
    $this->assertEqual($arr[0]['id'], $node_1);
    $this->assertEqual($arr[1]['id'], $node_2);
    $this->assertEqual($arr[2]['id'], $node_1_1);
    $this->assertEqual($arr[3]['id'], $node_1_1_1);
    $this->assertEqual($this->imp->getNode($node_1_1_1)->export(),
                       $this->imp->getNodeByPath('/node_2/node_1_1/node_1_1_1/')->export());
  }
}
?>
