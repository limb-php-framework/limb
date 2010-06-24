<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Base class for runtime components.<br />
 * Note that components that output XML tags should not inherit directly from
 * WactRuntimeComponent but rather the child WactRuntimeTagComponent<br />
 * Note that in the comments for this class, the terms parent and child
 * refer to the given components relative position in a template's
 * hierarchy, not to the PHP class hierarchy
 * @package wact
 * @version $Id: WactRuntimeComponent.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactRuntimeComponent
{
  // can make it protected since used in generated template (see WactRuntimeComponentTag)
  public $children = array();

  protected $parent;

  protected $id;

  function __construct($id)
  {
    $this->id = $id;
  }

  function getId()
  {
    return $this->id;
  }

  /**
  * Returns a child component given it's ID.<br />
  * Note this is a potentially expensive operation if dealing with
  * many components, as it calls the findChild method of children
  * based on alphanumeric order: strcasecmp(). Attempt to call it via
  * the nearest known component to the required child.
  */
  function findChild($ServerId)
  {
    foreach(array_keys($this->children) as $key)
    {
      if (strcasecmp($key, $ServerId))
      {
        if ($result = $this->children[$key]->findChild($ServerId))
          return $result;
      }
      else
        return $this->children[$key];
    }
    return FALSE;
  }

  /**
  * Same as findChild, except raises error if child is not found
  */
  function getChild($ServerId)
  {
    $result = $this->findChild($ServerId);
    if (!is_object($result))
    {
        throw new WactException('Could not find child component',
                                array('ServerId' => $ServerId,
                                      'file' => $this->getRootComponent($this)->getTemplatePath(),
                                      'line' => 0));
    }
    return $result;
  }

  function getRootComponent($item)
  {
    if(!$item->parent)
      return $item;
    else
      return $this->getRootComponent($item->parent);
  }

  function getDatasource()
  {
    return parent :: getDatasource();
  }

  /**
  * Set the data source of a child component, or raise an error
  * if the child is not found.
  */
  function setChildDataSource($path, $datasource)
  {
    $child = $this->getChild($path);
    $child->registerDataSource($datasource);
  }

  function setChildDataSet($path, $datasource)
  {
    $child = $this->getChild($path);
    $child->registerDataSet($datasource);
  }

  /**
  * Returns the first child component matching the supplied class name
  */
  function findChildByClass($class)
  {
    foreach( array_keys($this->children) as $key)
    {
      if (is_a($this->children[$key], $class))
        return $this->children[$key];
      elseif ($result = $this->children[$key]->findChildByClass($class))
        return $result;
    }
    return FALSE;
  }

  /**
  * Recursively searches through parents of this component searching for a given class name
  */
  function findParentByClass($class)
  {
    $parent = $this->parent;
    while ($parent && !is_a($parent, $class))
      $parent = $parent->parent;
    return $parent;
  }

  /**
  * Adds a reference to a child component to this component, using it's
  * ID attribute as the child array key
  */
  function addChild($child)
  {
    $child->parent = $this;
    $this->children[$child->getId()] = $child;
  }

  /**
  * Outputs the component, rendering any child components as well
  * This method will only ever be called on components that support
  * Dynamic rendering.
  */
  function render()
  {
    foreach(array_keys($this->children) as $key)
      $this->children[$key]->render();
  }
}

