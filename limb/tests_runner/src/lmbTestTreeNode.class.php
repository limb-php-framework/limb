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
 * @version $Id: lmbTestTreeNode.class.php 6020 2007-06-27 15:12:32Z pachanga $
 */
class lmbTestTreeNode
{
  protected $parent;
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

  function objectifyPath($path)
  {
    if($this->_traverseArrayPath(lmbTestTreePath :: toArray($path), $nodes))
    {
      $tree_path = new lmbTestTreePath();
      foreach($nodes as $node)
        $tree_path->addNode($node);
      return $tree_path;
    }
  }

  function findChildByPath($path)
  {
    return $this->_traverseArrayPath(lmbTestTreePath :: toArray($path));
  }

  protected function _traverseArrayPath($array_path, &$nodes = array())
  {
    $nodes[] = $this;

    // return itself in case of / path
    if(!$array_path)
      return $this;

    if(sizeof($array_path) == 1)
    {
      $child = $this->_getImmediateChildByIndex(array_shift($array_path));
      $nodes[] = $child;
      return $child;
    }

    $index = array_shift($array_path);

    if(!$child = $this->_getImmediateChildByIndex($index))
      return null;

    if($child->isTerminal())
      return null;

    return $child->_traverseArrayPath($array_path, $nodes);
  }

  protected function _getImmediateChildByIndex($index)
  {
    $children = $this->getChildren();
    if(isset($children[$index]))
      return $children[$index];
  }

  function isSkipped()
  {
    return false;
  }

  function isTerminal()
  {
    return false;
  }

  function createTestGroup()
  {
    $group = new TestSuite();
    foreach($this->children as $child)
      $group->addTestCase($child->createTestGroup());
    return $group;
  }

  function init()
  {
    return true;
  }

  function getTestLabel()
  {
    return $this->createTestGroup()->getLabel();
  }
}

?>
