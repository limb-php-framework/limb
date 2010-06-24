<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class WactCompileTreeNode.
 *
 * @package wact
 * @version $Id: WactCompileTreeNode.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactCompileTreeNode
{
  var $children = array();

  var $parent = NULL;

  var $ServerId;

  /**
  * @var WactSourceLocation
  **/
  protected $location_in_template;

  protected $properties = array();

  function __construct($location = null)
  {
    if($location)
      $this->location_in_template = $location;
    else
      $this->location_in_template = new WactSourceLocation();
  }

  function raiseCompilerError($error, $vars = array())
  {
    $vars['file'] = $this->location_in_template->getFile();
    $vars['line'] = $this->location_in_template->getLine();
    throw new WactException($error, $vars);
  }

  function getLocationInTemplate()
  {
    return $this->location_in_template;
  }

  function getTemplateFile()
  {
    return $this->location_in_template->getFile();
  }

  function getTemplateLine()
  {
    return $this->location_in_template->getLine();
  }

  function getServerId()
  {
    if (empty($this->ServerId))
      $this->ServerId = self :: generateNewServerId();

    return $this->ServerId;
  }

  function addChild($child)
  {
    $child->parent = $this;
    $this->children[] = $child;
  }

  function removeChild($ServerId)
  {
    foreach(array_keys($this->children) as $key)
    {
      $child = $this->children[$key];
      if ($child->getServerid() == $ServerId)
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
    foreach (array_keys($this->children) as $key)
    {
      $this->children[$key]->removeChildren();
      unset($this->children[$key]);
    }
  }

  function getChild($ServerId)
  {
    if($child = $this->findChild($ServerId))
      return $child;
    else
      $this->raiseCompilerError('Could not find component', array('ServerId' => $ServerId));
  }

  function findChild($ServerId)
  {
    foreach( array_keys($this->children) as $key)
    {
      if ($this->children[$key]->getServerid() == $ServerId)
        return $this->children[$key];
      else
      {
        if ($result = $this->children[$key]->findChild($ServerId))
          return $result;
      }
    }
    return FALSE;
  }

  /**
   * Sometimes it is useful to find treeNode located in another tree branch, eg:
   *  <code>
   *  <core:BLOCK><some_tag server_id='tag1'></core:BLOCK>
   *  <core:BLOCK><some_tag server_id='tag2'></core:BLOCK>
   *  </code>
   * in this case we can find tag1 tag from tag2 tag using findUpChild.
   * @see findChild()
   * @param string needed tag ServerId
   * @return object|false
   */

  function findUpChild($ServerId)
  {
    if($child = $this->findChild($ServerId))
      return $child;

    if($this->parent)
      return $this->parent->findUpChild($ServerId);
  }

  function findChildByClass($class)
  {
    foreach( array_keys($this->children) as $key)
    {
      if (is_a($this->children[$key], $class))
          return $this->children[$key];
      else
      {
        if ($result = $this->children[$key]->findChildByClass($class))
          return $result;
      }
    }
    $false = FALSE;
    return $false;
  }

  function findChildrenByClass($class)
  {
    $ret = array();
    foreach( array_keys($this->children) as $key)
    {
      if (is_a($this->children[$key], $class))
          $ret[] =& $this->children[$key];
      else
      {
        $more_children = $this->children[$key]->findChildrenByClass($class);
        if (count($more_children))
          $ret = array_merge($ret, $more_children);
      }
    }
    return $ret;
  }

  function findImmediateChildByClass($class)
  {
    foreach( array_keys($this->children) as $key)
    {
      if (is_a($this->children[$key], $class))
          return $this->children[$key];
    }
    $false = FALSE;
    return $false;
  }

  function findImmediateChildrenByClass($class)
  {
    $result = array();

    foreach(array_keys($this->children) as $key)
    {
      if (is_a($this->children[$key], $class))
        $result[] = $this->children[$key];
    }

    return $result;
  }

  function registerProperty($name, $property)
  {
      $this->properties[$name] = $property;
  }

  function getProperty($name)
  {
    if (array_key_exists($name, $this->properties))
        return $this->properties[$name];

    if($this->parent)
      return $this->parent->getProperty($name);
  }

  function findParentByClass($class)
  {
    $parent = $this->parent;

    while($parent && !is_a($parent, $class))
      $parent = $parent->parent;

    return $parent;
  }

  function findSelfOrParentByClass($class)
  {
    if (is_a($this, $class))
     return $this;
    else
     return $this->findParentByClass($class);
  }

  function prepare()
  {
    foreach( array_keys($this->children) as $key)
      $this->children[$key]->prepare();
  }

  /**
  * Used to perform error checking on template related to the syntax of
  * the concrete tag implementing this method.
  */
  function preParse()
  {
  }

  function isDataSource()
  {
    return FALSE;
  }

  /**
  * If a parent compile time component exists, returns the value of the
  * parent's getDataSource() method, which will be a concrete implementation
  */
  function getDataSource()
  {
    $result = null;
    if (!$this->isDataSource())
    {
      if (isset($this->parent))
        $result = $this->parent->getDataSource();
    }

    return $result;
  }

  /**
  * Gets the parent in the DataSource, if one exists
  */
  function getParentDataSource()
  {
    $result = null;

    $DataSource = $this->getDataSource();
    if($DataSource && !isset($DataSource->parent))
      return $DataSource;

    if (isset($DataSource->parent))
      $result = $DataSource->parent->getDataSource();

    return $result;
  }

  function getParent()
  {
    return $this->parent;
  }

  /**
  * Returns a root DataSource
  */
  function getRootDataSource()
  {
    $root = $this;
    while ($root->parent != NULL)
      $root = $root->parent;
    return $root;
  }

  /**
  * Gets the component reference code of the parent. This is a PHP string
  * which is used in the compiled template to reference the component in
  * the hierarchy at runtime
  */
  function getComponentRefCode()
  {
    return $this->parent->getComponentRefCode();
  }

  function getDatasourceRefCode()
  {
    return $this->getDatasource()->getComponentRefCode() . "->datasource";
  }

  function generateConstructor($code_writer)
  {
    foreach( array_keys($this->children) as $key)
      $this->children[$key]->generateConstructor($code_writer);
  }

  function generate($code_writer)
  {
    foreach( array_keys($this->properties) as $key)
    {
      if ($this->properties[$key]->isActive())
        $this->properties[$key]->generateScopeEntry($code_writer);
    }

    $this->generateContent($code_writer);

    foreach(array_keys($this->properties) as $key)
    {
      if ($this->properties[$key]->isActive())
        $this->properties[$key]->generateScopeExit($code_writer);
    }
  }

  function generateContent($code_writer)
  {
    $this->generateChildren($code_writer);
  }

  function generateChildren($code_writer)
  {
    foreach( array_keys($this->children) as $key)
      $this->children[$key]->generate($code_writer);
  }

  function setServerId($id)
  {
    $this->ServerId = $id;
  }

  static function generateNewServerId()
  {
    static $counter = 1;
    return 'id00' . $counter++;
  }

  /**
  * Checks that each immediate child of the current component has a unique ID
  * amongst its siblings.
  */
  function checkChildrenServerIds()
  {
    $child_ids = array();
    $checked_children = array();
    foreach ($this->getChildren() as $key => $child)
    {
      $id = $child->getServerId();
      if (in_array($id, $child_ids))
      {
        $duplicate_child = $checked_children[$id];
        $child->raiseCompilerError('Duplicate "id" attribute',
                                   array('ServerId' => $id,
                                         'duplicate_component_file' => $duplicate_child->getTemplateFile(),
                                         'duplicate_component_line' => $duplicate_child->getTemplateLine()));
      }
      else
      {
        $child_ids[] = $id;
        $checked_children[$id] = $child;
      }
    }
  }
}

