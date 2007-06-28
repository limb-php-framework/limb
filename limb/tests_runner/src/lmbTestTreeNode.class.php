<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/lmbTestTreePath.class.php');

/**
 * abstract class lmbTestTreeNode.
 *
 * @package tests_runner
 * @version $Id: lmbTestTreeNode.class.php 6021 2007-06-28 13:18:44Z pachanga $
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
    $this->_loadChildren();
    return $this->children;
  }

  protected function _loadChildren(){}

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

  function init(){}

  function getTestLabel()
  {
    return $this->_doCreateTestCase()->getLabel();
  }

  function createTestCase()
  {
    $test = $this->_doCreateTestCase();
    $children = $this->getChildren();//getter instead of raw property, since child classes may need customization
    foreach($children as $child)
    {
      if($child->isSkipped())
        continue;
      $child->init();
      $test->addTestCase($child->createTestCase());
    }
    return $test;
  }

  protected function _doCreateTestCase()
  {
    return new TestSuite();
  }
}

?>
