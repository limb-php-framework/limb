<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbCollectionDecorator.class.php');

/**
 * class lmbTreeNestedCollection.
 *
 * @package tree
 * @version $Id: lmbTreeNestedCollection.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbTreeNestedCollection extends lmbCollectionDecorator
{
  protected $node_field = 'id';
  protected $parent_field = 'parent_id';
  protected $children_field = 'children';
  
  function setNodeField($name)
  {
    $this->node_field = $name;
  }

  function setParentField($name)
  {
    $this->parent_field = $name;
  }

  function setChildrenField($name)
  {
    $this->children_field = $name;
  }
  
  function rewind()
  {
    parent :: rewind();

    if($this->iterator->valid())
    {
      $nested_array = array();
      self :: _doMakeNested($this->iterator, $nested_array);
      $iterator = new lmbCollection($nested_array);
    }
    else
      $iterator = new lmbCollection();

    $this->iterator = $iterator;

    return $this->iterator->rewind();
  }

  function _doMakeNested($rs, &$nested_array, $parent_id=null, $level=0)
  {
    $prev_item_id = null;

    while($rs->valid())
    {
      $item = $rs->current();

      if($level == 0 && ($item[$this->parent_field] !== $prev_item_id))
        $parent_id = $item[$this->parent_field];

      if($item[$this->parent_field] == $parent_id)
      {
        $nested_array[] = $item;
        $rs->next();
      }
      elseif($item[$this->parent_field] === $prev_item_id)
      {
        $children = array();
        $pos = sizeof($nested_array) - 1;
        self :: _doMakeNested($rs, $children, $prev_item_id, $level + 1);
        $nested_array[$pos][$this->children_field] = $children;
      }
      else
        return;

      $prev_item_id = $item[$this->node_field];
    }
  }
}


