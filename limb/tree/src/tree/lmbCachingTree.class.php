<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCachingTree.class.php 5552 2007-04-06 08:56:27Z pachanga $
 * @package    tree
 */
lmb_require('limb/cache/src/lmbCacheMemoryPersister.class.php');
lmb_require('limb/cache/src/lmbCachedPagedIterator.class.php');
lmb_require('limb/cache/src/lmbCacheCompositePersister.class.php');
lmb_require('limb/cache/src/lmbCachePersisterKeyDecorator.class.php');
lmb_require('limb/tree/src/tree/lmbTreeDecorator.class.php');
lmb_require('limb/cache/src/lmbCacheFilePersister.class.php');

define('LIMB_CACHING_TREE_COMMON_GROUP', 'tree');

class lmbCachingTree extends lmbTreeDecorator
{
  protected $cache;
  protected $current_key;
  protected $current_group;

  function __construct($tree, $cache_dir)
  {
    parent :: __construct($tree);
    $this->cache = $this->_createCache($cache_dir);
  }

  function _createCache($cache_dir)
  {
    $persister = new lmbCacheCompositePersister();
    $persister->registerPersister(new lmbCacheMemoryPersister());
    $persister->registerPersister(new lmbCacheFilePersister($cache_dir));

    return new lmbCachePersisterKeyDecorator($persister);
  }

  function getNode($node)
  {
    $id = $this->_getIdLazy($node);
    $this->_useCacheKey(array('node', $id));
    return $this->_cacheCallback('getNode', array($node));
  }

  function getParents($node)
  {
    $id = $this->_getIdLazy($node);
    $this->_useCacheKey(array('parents', $id));
    return $this->_cacheCallback('getParents', array($node));
  }

  function getChildren($node)
  {
    $id = $this->_getIdLazy($node);
    $this->_useCacheKey(array('children', $id));
    return $this->_cacheCallback('getChildren', array($node));
  }

  function countChildren($node)
  {
    $id = $this->_getIdLazy($node);
    $this->_useCacheKey(array('count_children', $id));
    return $this->_cacheCallback('countChildren', array($node));
  }

  function getNodesByIds($ids)
  {
    $sorted_ids = $ids;
    sort($sorted_ids);
    $this->_useCacheKey(array('ids', $sorted_ids));

    return $this->_cacheCallback('getNodesByIds', array($ids));
  }

  function getPathToNode($node, $delimiter = '/')
  {
    $id = $this->_getIdLazy($node);
    $this->_useCacheKey(array('path_to_node', $id));
    return $this->_cacheCallback('getPathToNode', array($node, $delimiter));
  }

  function getNodeByPath($path, $delimiter = '/')
  {
    $this->_useCacheKey(array('path', rtrim($path, '/')));
    return $this->_cacheCallback('getNodeByPath', array($path));
  }

  function getAllNodes()
  {
    $this->_useCacheKey(array('all_nodes'));
    return $this->_cacheCallback('getAllNodes');
  }

  function getRootNodes()
  {
    $this->_useCacheKey(array('root_nodes'));
    return $this->_cacheCallback('getRootNodes');
  }

  function getSubBranch($node, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    if($check_expanded_parents)
      return parent :: getSubBranch($node, $depth, $include_parent, $check_expanded_parents);

    $id = $this->_getIdLazy($node);

    $key = array('sub_branch',
                 'node_id' => $id,
                 'depth' => $depth,
                 'include_parent' => $include_parent,
                 'check_expanded_parents' => $check_expanded_parents);

    $this->_useCacheKey($key, LIMB_CACHING_TREE_COMMON_GROUP);

    return $this->_cacheCallback('getSubBranch',
                                   array($node, $depth, $include_parent, $check_expanded_parents));
  }

  function getSubBranchByPath($path, $depth = -1, $include_parent = false, $check_expanded_parents = false)
  {
    if($check_expanded_parents)
      return $this->original->getSubBranchByPath($path, $depth, $include_parent, $check_expanded_parents);

    $key = array('sub_branch_by_path',
                 'path' => rtrim($path, '/'),
                 'depth' => $depth,
                 'include_parent' => $include_parent,
                 'check_expanded_parents' => $check_expanded_parents);

    $this->_useCacheKey($key, LIMB_CACHING_TREE_COMMON_GROUP);

    return $this->_cacheCallback('getSubBranchByPath',
                                   array($path, $depth, $include_parent, $check_expanded_parents));
  }

  function createNode($values, $parent_node = null)
  {
    $result = parent :: createNode($values, $parent_node);
    $this->flushCache();
    return $result;
  }

  function createRootNode($values)
  {
    $result = parent :: createRootNode($values);
    $this->flushCache();
    return $result;
  }

  function createSubNode($id, $values)
  {
    $result = parent :: createSubNode($id, $values);
    $this->flushCache();
    return $result;
  }

  function deleteNode($id)
  {
    $result = parent :: deleteNode($id);
    $this->flushCache();
    return $result;
  }

  function updateNode($id, $values, $internal = false)
  {
    $result = parent :: updateNode($id, $values, $internal);
    $this->flushCache();
    return $result;
  }

  function moveTree($id, $target_id)
  {
    $result = parent :: moveTree($id, $target_id);
    $this->flushCache();
    return $result;
  }

  function _useCacheKey($key, $group = null)
  {
    $this->current_key = $key;
    $this->current_group = is_null($group) ? LIMB_CACHING_TREE_COMMON_GROUP : $group;
  }

  function flushCache()
  {
    $this->cache->flushAll();
  }

  function _cacheCallback($method, $args = null, $is_rs = true, $key = null, $group = null)
  {
    $group = is_null($group) ? $this->current_group : $group;
    $key = is_null($key) ? $this->current_key : $key;

    if(($result = $this->cache->get($key, $group)) !== LIMB_CACHE_NULL_RESULT)
      return $result;

    $result = $this->___invoke($method, $args);

    if(is_object($result))//assuming it's an iterator object
    {
      $cached_result = new lmbCachedPagedIterator($result, $this->cache);
      $this->cache->put($key, $cached_result, $group);
      return $cached_result;
    }
    else
    {
      $this->cache->put($key, $result, $group);
      return $result;
    }
  }

  function _getIdLazy($node)
  {
    if(is_array($node))
      return $node['id'];
    else
      return $node;
  }
}
?>