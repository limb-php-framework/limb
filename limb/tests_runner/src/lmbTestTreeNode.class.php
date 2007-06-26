<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * abstract class lmbTestTreeNode.
 *
 * @package tests_runner
 * @version $Id: lmbTestTreeNode.class.php 6016 2007-06-26 13:31:54Z pachanga $
 */
abstract class lmbTestTreeNode
{
  protected $parent = null;
  protected $children = array();

  function setParent($parent)
  {
    $this->parent = $parent;
  }

  function getParent()
  {
    return $this->parent;
  }

  function addChild($child)
  {
    $child->setParent($this);
    $this->children[] = $child;
  }

  function getChildren()
  {
    return $this->children;
  }

  function findChildByPath($path)
  {
    // return itself in case of / path
    if(!$array_path = lmbTestTreePath :: toArray($path))
      return $this;

    return $this->_findChildByArrayPath($array_path);
  }

  protected function _findChildByArrayPath($array_path)
  {
    if(sizeof($array_path) == 1)
      return $this->_getImmediateChildByIndex(array_shift($array_path));

    $index = array_shift($array_path);

    if(!$child = $this->_getImmediateChildByIndex($index))
      return null;

    if($child->isTerminal())
      return null;

    return $child->_findChildByArrayPath($array_path);
  }

  protected function _getImmediateChildByIndex($index)
  {
    $children = $this->getChildren();
    if(isset($children[$index]))
      return $children[$index];
  }

  function isTerminal()
  {
    return false;
  }

  function isSkipped()
  {
    return false;
  }

  abstract function createTestGroup();

  abstract function createTestGroupWithoutChildren();

  function bootstrap()
  {
    return true;
  }

  function bootstrapPath($path)
  {
    // return itself in case of / path
    if(!$array_path = lmbTestTreePath :: toArray($path))
      return $this->bootstrap();

    $reverse_path = array();
    foreach($array_path as $path_item)
    {
      $reverse_path[] = $path_item;
      if($node = $this->_findChildByArrayPath($reverse_path))
        $node->bootstrap();
    }
  }

  function getTestLabel()
  {
    return $this->createTestGroup()->getLabel();
  }

  function wrapWithParentTestGroups($case)
  {
    $new_group = $this->createTestGroupWithoutChildren();
    $new_group->addTestCase($case);

    if($parent = $this->getParent())
      return $parent->wrapWithParentTestGroups($new_group);
    else
      return $new_group;
  }

  function createTestGroupWithParents()
  {
    $group = $this->createTestGroup();

    if($parent = $this->getParent())
    {
      $final_group = new TestSuite($group->getLabel());
      $wrapped = $parent->wrapWithParentTestGroups($group);
      $final_group->addTestCase($wrapped);
      return $final_group;
    }
    else
      return $group;
  }
}

?>
