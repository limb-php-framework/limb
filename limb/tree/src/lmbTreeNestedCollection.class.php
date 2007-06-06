<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbCollectionDecorator.class.php');

/**
 * class lmbTreeNestedCollection.
 *
 * @package tree
 * @version $Id: lmbTreeNestedCollection.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbTreeNestedCollection extends lmbCollectionDecorator
{
  protected $node_field = 'id';
  protected $parent_field = 'parent_id';

  function setNodeField($name)
  {
    $this->node_field = $name;
  }

  function setParentField($name)
  {
    $this->parent_field = $name;
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

      if($level == 0 && $item->get($this->parent_field) !== $prev_item_id)
        $parent_id = $item->get($this->parent_field);

      if($item->get($this->parent_field) == $parent_id)
      {
        $nested_array[] = $item->export();
        $rs->next();
      }
      elseif($item->get($this->parent_field) === $prev_item_id)
      {
        $nested_array[sizeof($nested_array) - 1]['children'] = array();
        $new_nested =& $nested_array[sizeof($nested_array) - 1]['children'];
        self :: _doMakeNested($rs, $new_nested, $prev_item_id, $level + 1);
      }
      else
        return;

      $prev_item_id = $item->get($this->node_field);
    }
  }
}

?>