<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbCollection.class.php');

/**
 * class lmbTreeHelper.
 *
 * @package tree
 * @version $Id: lmbTreeHelper.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbTreeHelper
{
  function sort($rs, $sort_params, $id_hash = 'id', $parent_hash = 'parent_id')
  {
    $tree_array = self :: _convertRs2Array($rs);

    $item = reset($tree_array);
    $parent_id = $item[$parent_hash];

    $sorted_tree_array = array();

    self :: _doSort($tree_array, $sorted_tree_array, $sort_params, $parent_id, $id_hash, $parent_hash);

    return new lmbCollection($sorted_tree_array);
  }

  function _convertRs2Array($rs)
  {
    $tree_array = array();
    foreach($rs as $record)
      $tree_array[] = $record;

    return $tree_array;
  }

  function _doSort($tree_array, &$sorted_tree_array, $sort_params, $parent_id, $id_hash, $parent_hash)
  {
    $children = array();

    foreach($tree_array as $index => $item)
    {
      if($item[$parent_hash] == $parent_id)
      {
        $children[] = $item;
        unset($tree_array[$index]);
      }
    }

    if(!($count = sizeof($children)))
      return;

    $children = lmbArrayHelper :: sortArray($children, $sort_params);

    if(!$sorted_tree_array)
    {
      $sorted_tree_array = $children;
    }
    else
    {
      $ids = lmbArrayHelper :: getColumnValues($id_hash, $sorted_tree_array);

      $offset = array_search($parent_id, $ids) + 1;

      array_splice($sorted_tree_array, $offset, 0, $children);
    }

    for($i=0; $i < $count; $i++)
    {
      lmbTreeHelper :: _doSort($tree_array, $sorted_tree_array, $sort_params, $children[$i][$id_hash], $id_hash, $parent_hash);
    }
  }
}


