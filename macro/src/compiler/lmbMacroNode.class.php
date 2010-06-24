<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMacroNode.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroNode
{
  protected $node_id;
  protected $children = array();
  protected $parent;

  static protected $counter = 1;

  /**
  * @var lmbMacroSourceLocation
  */
  protected $location;

  function __construct($location = null)
  {
    if($location)
      $this->location = $location;
    else
      $this->location = new lmbMacroSourceLocation();
  }

  function setParent($parent)
  {
    $this->parent = $parent;
  }

  function getParent()
  {
    return $this->parent;
  }

  function getLocationInTemplate()
  {
    return $this->location;
  }

  function getTemplateFile()
  {
    return $this->location->getFile();
  }

  function getTemplateLine()
  {
    return $this->location->getLine();
  }

  function getNodeId()
  {
    if($this->node_id)
      return $this->node_id;

    $this->node_id = self :: generateNewId();
    return $this->node_id;
  }

  function setNodeId($node_id)
  {
    $this->node_id = $node_id;
  }

  static function generateNewId()
  {
    return 'id00' . self :: $counter++;
  }

  static function generateUniqueId()
  {
    return md5(uniqid(rand(), true));
  }

  function raise($error, $vars = array())
  {
    $vars['file'] = $this->location->getFile();
    $vars['line'] = $this->location->getLine();
    throw new lmbMacroException($error, $vars);
  }

  function addChild($child)
  {
    $child->parent = $this;
    $this->children[] = $child;
  }

  function removeChild($id)
  {
    foreach($this->children as $key => $child)
    {
      if($child->getNodeId() == $id)
      {
        unset($this->children[$key]);
        return $child;
      }
    }
  }

  function getChildren()
  {
    return $this->children;
  }

  function removeChildren()
  {
    foreach($this->children as $child)
    {
      $child->removeChildren();
      unset($child);
    }
  }

  function getChild($id)
  {
    if($child = $this->findChild($id))
      return $child;
    else
      $this->raise('Could not find component', array('id' => $id));
  }

  function findChild($id)
  {
    foreach($this->children as $child)
    {
      if($child->getNodeId() == $id)
        return $child;
      else
      {
        if($result = $child->findChild($id))
          return $result;
      }
    }
  }

  /**
   * Sometimes it is useful to find node located in another tree branch, eg:
   *  <code>
   *  {{block}}{{some_tag id='tag1'}}{{/block}}
   *  {{block}}{{some_tag id='tag2'}}{{/block}}
   *  </code>
   * in this case we can find tag1 tag from tag2 tag using findUpChild.
   */
  function findUpChild($id)
  {
    if($child = $this->findChild($id))
      return $child;

    if($this->parent)
      return $this->parent->findUpChild($id);
  }


  function findChildByClass($class)
  {
    foreach($this->children as $child)
    {
      if($child instanceof $class)
        return $child;
      else
      {
        if($result = $child->findChildByClass($class))
          return $result;
      }
    }
  }

  function findChildrenByClass($class)
  {
    $ret = array();
    foreach($this->children as $child)
    {
      if($child instanceof  $class)
        $ret[] = $child;

      $more_children = $child->findChildrenByClass($class);
      if(count($more_children))
        $ret = array_merge($ret, $more_children);
    }
    return $ret;
  }

  function findImmediateChildByClass($class)
  {
    foreach($this->children as $child)
    {
      if($child instanceof $class)
        return $child;
    }
  }

  function findImmediateChildrenByClass($class)
  {
    $result = array();
    foreach($this->children as $child)
    {
      if($child instanceof $class)
        $result[] = $child;
    }
    return $result;
  }

  function findParentByClass($class)
  {
    $parent = $this->parent;

    while($parent && (!$parent instanceof $class))
      $parent = $parent->parent;

    return $parent;
  }

  function findRoot()
  {
    $root = $this;
    while ($root->parent != NULL)
      $root = $root->parent;
    return $root;
  }

  function preParse($compiler){}

  function generate($code_writer)
  {
    foreach($this->children as $child)
      $child->generate($code_writer);
  }

  /**
  * Checks that each immediate child of the current component has a unique ID
  * amongst its siblings.
  */
  function checkChildrenIds()
  {
    $child_ids = array();
    $checked_children = array();
    foreach($this->getChildren() as $key => $child)
    {
      $id = $child->getNodeId();
      if(in_array($id, $child_ids))
      {
        $duplicate_child = $checked_children[$id];
        $child->raise('Duplicate "id" attribute',
                                   array('id' => $id,
                                         'duplicate_node_file' => $duplicate_child->getTemplateFile(),
                                         'duplicate_node_line' => $duplicate_child->getTemplateLine()));
      }
      else
      {
        $child_ids[] = $id;
        $checked_children[$id] = $child;
      }
    }
  }
}

