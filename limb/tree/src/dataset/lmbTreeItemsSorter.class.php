<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTreeItemsSorter.class.php 5561 2007-04-06 13:07:43Z pachanga $
 * @package    tree
 */
lmb_require('limb/tree/src/tree/lmbTreeRsProcessor.class.php');

class lmbTreeItemsSorter extends lmbIteratorDecorator
{
  protected $node_field = 'id';
  protected $parent_field = 'parent_id';
  protected $order_pairs = array();

  function setNodeField($name)
  {
    $this->node_field = $name;
  }

  function setParentField($name)
  {
    $this->parent_field = $name;
  }

  function setOrder($order_string)
  {
    $order_items = explode(',', $order_string);
    foreach($order_items as $order_pair)
    {
      $arr = explode('=', $order_pair);

      if(isset($arr[1]))
      {
        if(strtolower($arr[1]) == 'asc' || strtolower($arr[1]) == 'desc'
           || strtolower($arr[1]) == 'rand()')
          $this->order_pairs[$arr[0]] = strtoupper($arr[1]);
        else
          throw new lmbException('Wrong order type', array('order' => $arr[1]));
      }
      else
        $this->order_pairs[$arr[0]] = 'ASC';
    }
  }

  function rewind()
  {
    parent :: rewind();

    if($this->iterator->valid())
    {
      $nested_array = array();

      $iterator = lmbTreeRsProcessor :: sort($this->iterator, $this->order_pairs, $this->node_field, $this->parent_field);
    }
    else
      $iterator = new lmbIterator();

    $this->iterator = $iterator;

    return $this->iterator->rewind();
  }
}

?>