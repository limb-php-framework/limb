<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCachingTreeTest.class.php 5561 2007-04-06 13:07:43Z pachanga $
 * @package    tree
 */
lmb_require('limb/cache/src/lmbCachePersister.interface.php');
lmb_require('limb/tree/src/tree/lmbTree.interface.php');
lmb_require('limb/tree/src/tree/lmbCachingTree.class.php');

Mock :: generate('lmbTree', 'MockTree');
Mock :: generate('lmbCachePersister', 'MockCachePersister');
Mock :: generatePartial('lmbCachingTree',
                        'CachingTreeSpecialVersion',
                        array('_createCache'));

class CachePersisterSpecialVersion extends MockCachePersister
{
  var $put;

  function put($key, $value, $group = 'default')
  {
    parent :: put($key, $value, $group);
    $this->put = $value;
  }
}

class lmbCachingTreeTest extends UnitTestCase
{
  var $tree;
  var $driver;
  var $cache;
  function setUp()
  {
    $this->tree = new MockTree();
    $this->cache = new CachePersisterSpecialVersion($this);

    $this->decorator = new CachingTreeSpecialVersion($this);
    $this->decorator->setReturnReference('_createCache', $this->cache);
    $this->decorator->__construct($this->tree, LIMB_VAR_DIR . '/tree');
  }

  function testGetNodeCacheHit()
  {
    $this->_testCacheHit(array('getNode', array($node_id = 100)),
                           $result = 'whatever',
                           $key = array('node', $node_id),
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testGetNodeCacheMiss()
  {
    $this->_testCacheMiss(array('getNode', array($node_id = 100)),
                           $result = 'whatever',
                           $key = array('node', $node_id),
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testGetParentsCacheHit()
  {
    $this->_testCacheHit(array('getParents', array($node_id = 100)),
                           new lmbArrayDataset(array('whatever')),
                           $key = array('parents', $node_id),
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testGetParentsCacheMiss()
  {
    $this->_testCacheMiss(array('getParents', array($node_id = 100)),
                           new lmbArrayDataset(array('whatever')),
                           $key = array('parents', $node_id),
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testGetChildrenCacheHit()
  {
    $this->_testCacheHit(array('getChildren', array($node_id = 100)),
                           $result = 'whatever',
                           $key = array('children', $node_id),
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testGetChildrenCacheMiss()
  {
    $this->_testCacheMiss(array('getChildren', array($node_id = 100)),
                           new lmbArrayDataset(array('whatever')),
                           $key = array('children', $node_id),
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testCountChildrenCacheHit()
  {
    $this->_testCacheHit(array('countChildren', array($node_id = 100)),
                           $result = 'whatever',
                           $key = array('count_children', $node_id),
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testCountChildrenCacheMiss()
  {
    $this->_testCacheMiss(array('countChildren', array($node_id = 100)),
                           $result = 'whatever',
                           $key = array('count_children', $node_id),
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testGetNodesByIdsCacheHit()
  {
    //sorting ids
    $this->_testCacheHit(array('getNodesByIds', array(array(1, 3, 2))),
                           $result = 'whatever',
                           $key = array('ids', array(1, 2, 3)),
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testGetNodesByIdsCacheMiss()
  {
    //sorting ids
    $this->_testCacheMiss(array('getNodesByIds', array(array(1, 3, 2))),
                           new lmbArrayDataset(array('whatever')),
                           $key = array('ids', array(1, 2, 3)),
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testGetPathToNodeCacheHit()
  {
    $this->_testCacheHit(array('getPathToNode', array($node_id = 100, $delim = '|')),
                           $result = 'whatever',
                           $key = array('path_to_node', $node_id),
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testGetPathToNodeCacheMiss()
  {
    $this->_testCacheMiss(array('getPathToNode', array($node_id = 100, $delim = '|')),
                           $result = 'whatever',
                           $key = array('path_to_node', $node_id),
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testGetNodeByPathCacheHit()
  {
    //trimming trailing slash
    $this->_testCacheHit(array('getNodeByPath', array('path/')),
                           $result = 'whatever',
                           $key = array('path', 'path'),
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testGetNodeByPathCacheMiss()
  {
    //trimming trailing slash
    $this->_testCacheMiss(array('getNodeByPath', array('path/')),
                           $result = 'whatever',
                           $key = array('path', 'path'),
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testGetSubBranchCacheHit()
  {
    $key = array('sub_branch',
                 'node_id' => $node_id = 100,
                 'depth' => $depth = -1,
                 'include_parent' => false,
                 'check_expanded_parents' => false);

    $this->_testCacheHit(array('getSubBranch', array($node_id, $depth, false, false)),
                           $result = 'whatever',
                           $key,
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testGetSubBranchCacheMiss()
  {
    $key = array('sub_branch',
                 'node_id' => $node_id = 100,
                 'depth' => $depth = -1,
                 'include_parent' => false,
                 'check_expanded_parents' => false);

    $this->_testCacheMiss(array('getSubBranch', array($node_id, $depth, false, false)),
                           new lmbArrayDataset(array('whatever')),
                           $key,
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testGetSubBranchDontCacheExpandedParents()
  {
    $this->_assertCacheNotCalled();

    $this->tree->expectOnce('getSubBranch');
    $this->tree->setReturnValue('getSubBranch',
                                $expected = 'result',
                                array($node_id = 100, $depth = -1, false, $check_expanded_parents = true));

    $result = $this->decorator->getSubBranch($node_id, $depth, false, $check_expanded_parents);

    $this->assertEqual($result, $expected);
  }

  function testGetSubBranchByPathCacheHit()
  {
    //trimming trailing slash
    $key = array('sub_branch_by_path',
                 'path' => 'path',
                 'depth' => $depth = -1,
                 'include_parent' => false,
                 'check_expanded_parents' => false);

    $this->_testCacheHit(array('getSubBranchByPath', array('path/', $depth, false, false)),
                           $result = 'whatever',
                           $key,
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testGetSubBranchByPathCacheMiss()
  {
    //trimming trailing slash
    $key = array('sub_branch_by_path',
                 'path' => 'path',
                 'depth' => $depth = -1,
                 'include_parent' => false,
                 'check_expanded_parents' => false);

    $this->_testCacheMiss(array('getSubBranchByPath', array('path/', $depth, false, false)),
                           new lmbArrayDataset(array('whatever')),
                           $key,
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testGetRootNodesCacheHit()
  {
    $this->_testCacheHit(array('getRootNodes'),
                           $result = 'whatever',
                           $key = array('root_nodes'),
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testGetRootNodesCacheMiss()
  {
    $this->_testCacheMiss(array('getRootNodes'),
                           new lmbArrayDataset(array('whatever')),
                           $key = array('root_nodes'),
                           LIMB_CACHING_TREE_COMMON_GROUP);
  }

  function testCreateNode()
  {
    $this->tree->setReturnValue('createNode', $result = 'someResult', array($values = 'whatever', $parent = 1));
    $this->cache->expectOnce('flushAll');
    $this->assertEqual($this->decorator->createNode($values, $parent), $result);
  }

  function testCreateRootNode()
  {
    $this->tree->setReturnValue('createRootNode', $result = 'someResult', array($values = 'whatever'));
    $this->cache->expectOnce('flushAll');
    $this->assertEqual($this->decorator->createRootNode($values), $result);
  }

  function testCreateSubNode()
  {
    $this->tree->setReturnValue('createSubNode',
                                $result = 'someResult',
                                array($id = 'id',$values = 'whatever'));

    $this->cache->expectOnce('flushAll');
    $this->assertEqual($this->decorator->createSubNode($id, $values), $result);
  }

  function testDeleteNode()
  {
    $this->tree->setReturnValue('deleteNode', $result = 'someResult', array($id = 'id'));
    $this->cache->expectOnce('flushAll');
    $this->assertEqual($this->decorator->deleteNode($id), $result);
  }

  function testUpdateNode()
  {
    $this->tree->setReturnValue('updateNode',
                                $result = 'someResult',
                                array($id = 'id', $values = 'whatever', false));

    $this->cache->expectOnce('flushAll');
    $this->assertEqual($this->decorator->updateNode($id, $values), $result);
  }

  function testMoveTree()
  {
    $this->tree->setReturnValue('moveTree',
                                $result = 'some result',
                                array($id = 'id', $target_id = 'target'));

    $this->cache->expectOnce('flushAll');
    $this->assertEqual($this->decorator->moveTree($id, $target_id), $result);
  }

  function _testCacheHit($callback, $expected, $key, $group)
  {
    $this->cache->expectOnce('get', array($key, $group));
    $this->cache->setReturnReference('get', $expected);

    $this->tree->expectNever($callback[0]);
    $result = $this->_callDecorator($callback);

    $this->assertEqual($result, $expected);
  }

  function _testCacheMiss($callback, $expected, $key, $group)
  {
    $this->cache->expectOnce('get', array($key, $group));
    $this->cache->setReturnValue('get', LIMB_CACHE_NULL_RESULT);

    $this->tree->expectOnce($callback[0]);
    $this->tree->setReturnValue($callback[0], $expected, isset($callback[1]) ? $callback[1] : false);

    if(is_object($expected))
      $this->cache->expectOnce('put', array($key, new IsAExpectation('lmbCachedPagedIterator'), $group));
    else
      $this->cache->expectOnce('put', array($key, $expected, $group));

    $result = $this->_callDecorator($callback);

    if(is_object($result)) //ugly hack
    {
      $this->assertEqual($result->getArray(), $expected->getArray());
      $this->assertEqual($this->cache->put->getArray(), $expected->getArray());
    }
    else
      $this->assertEqual($result, $expected);
  }

  function _assertCacheNotCalled()
  {
    $this->cache->expectNever('get');
    $this->cache->expectNever('put');
  }

  function _callDecorator($callback)
  {
    return call_user_func_array(array(&$this->decorator, $callback[0]),
                                isset($callback[1]) ? $callback[1] : null);
  }
}

?>
