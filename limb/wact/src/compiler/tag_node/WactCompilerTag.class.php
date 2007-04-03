<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactCompilerTag.class.php 5203 2007-03-07 08:58:21Z serega $
 * @package    wact
 */

class WactCompilerTag extends WactCompileTreeNode
{
  /**
  * @var array of WactAttributeNode
  **/
  protected $attributeNodes = array();

  public $tag = '';

  public $hasClosingTag = TRUE;

  public $emptyClosedTag = FALSE;

  /**
  * @var WactTagInfo
  **/
  protected $tag_info = NULL;

  function __construct($location, $tag, $tag_info)
  {
    $this->tag = $tag;
    $this->tag_info = $tag_info;

    parent :: __construct($location);
  }

  function getTag()
  {
    return $this->tag;
  }

  function addChildAttribute($child)
  {
    $attrib = strtolower($child->getName());

    if (isset($this->attributeNodes[$attrib]))
        $this->raiseCompilerError('Duplicate attribute',  array('attribute' => $attrib));

    $this->attributeNodes[$attrib] = $child;
  }

  function raiseRequiredAttributeError($attribute_name)
  {
    $this->raiseCompilerError('Missing required attribute', array('attribute' => $attribute_name));
  }

  function raiseCompilerError($error, $vars = array())
  {
    $vars['tag'] = $this->tag;
    parent :: raiseCompilerError($error, $vars);
  }

  function preParse()
  {
    foreach($this->tag_info->getRequiredConstantAttributes() as $attr_name)
    {
      $value = $this->getAttribute($attr_name);
      if(empty($value))
        $this->raiseRequiredAttributeError($attr_name);
    }

    foreach($this->tag_info->getRequiredAttributes() as $attr_name)
    {
      if(!$this->hasAttribute($attr_name))
        $this->raiseRequiredAttributeError($attr_name);
    }

    if($this->tag_info->isRestrictSelfNesting() && $parent = $this->findParentByClass(get_class($this)))
      $this->raiseCompilerError('Tag cannot be nested within the same tag',
                                array('same_tag_file' => $parent->getTemplateFile(),
                                      'same_tag_line' => $parent->getTemplateLine()));

    if(($parent_class = $this->tag_info->getParentTagClass()) &&
       !$parent = $this->findParentByClass($parent_class))
    {
      $this->raiseCompilerError('Tag must be enclosed by a proper parent tag',
                                array('required_parent_tag_class' => $parent_class));

    }

    return parent :: preParse();
  }

  /**
  * Sets an attribute
  */
  function setAttribute($attrib, $value)
  {
    $Component = new WactAttributeNode($attrib, $value);
    $this->addChildAttribute($Component);
  }

  /**
  * Returns the value of an XML attribute (as extracted from template) or
  * NULL if attribute not found
  */
  function getAttribute($attrib)
  {
    if (isset($this->attributeNodes[strtolower($attrib)]))
      return $this->attributeNodes[strtolower($attrib)]->getValue();
  }

  function getAttributeNode($attrib)
  {
    if (isset($this->attributeNodes[strtolower($attrib)]))
      return $this->attributeNodes[strtolower($attrib)];
  }

  function hasAttribute($attrib)
  {
    return isset($this->attributeNodes[strtolower($attrib)]);
  }

  /**
  * Return the value of a boolean attribute as a boolean.
  * ATTRIBUTE=ANYTHING  (true)
  * ATTRIBUTE=(FALSE|N|NA|NO|NONE|0) (false)
  * ATTRIBUTE (true)
  * (attribute unspecified) (default)
  */
  function getBoolAttribute($attrib, $default = FALSE)
  {
    if (!isset($this->attributeNodes[strtolower($attrib)]))
      return $default;

    return self :: getBooleanValue($this->attributeNodes[strtolower($attrib)]->getValue());
  }

  static function getBooleanValue($value)
  {
    switch (strtoupper($value))
    {
      case 'FALSE':
      case 'N':
      case 'NO':
      case 'NONE':
      case 'NA':
      case '0':
        return false;
      default:
        return true;
    }
  }

  function removeAttribute($attrib)
  {
    unset($this->attributeNodes[strtolower($attrib)]);
  }

  /**
  * Returns an array containing the attributes of this component that
  * can be resolved at compile time.
  */
  function getAttributesAsArray($suppress = array())
  {
    $suppress = array_map('strtolower', $suppress);
    $attributes = array();
    foreach( array_keys($this->attributeNodes) as $key)
    {
      if (!in_array($key, $suppress) && $this->attributeNodes[$key]->isConstant())
        $attributes[$this->attributeNodes[$key]->getName()] = $this->getAttribute($key);
    }
    return $attributes;
  }

  function generateAttributeList($code_writer_writer, $suppress = array())
  {
    $suppress = array_map('strtolower', $suppress);
    foreach(array_keys($this->attributeNodes) as $key)
    {
      if (!in_array($key, $suppress))
        $this->attributeNodes[$key]->generate($code_writer_writer);
    }
  }

  function generateDynamicAttributeList($code_writer_writer, $suppress = array())
  {
    $suppress = array_map('strtolower', $suppress);
    foreach( array_keys($this->attributeNodes) as $key)
    {
      if (!in_array($key, $suppress) && !$this->attributeNodes[$key]->isConstant())
        $this->attributeNodes[$key]->generate($code_writer_writer);
    }
  }

  function getServerId()
  {
    if ($this->hasAttribute('wact:id'))
      return $this->getAttribute('wact:id');
    elseif($this->hasAttribute('id'))
      return $this->getAttribute('id');
    else
      return parent :: getServerId();
  }

  function getClientId()
  {
    if ($this->hasAttribute('id'))
      return $this->getAttribute('id');
  }

  function prepare()
  {
    foreach( array_keys($this->attributeNodes) as $key)
        $this->attributeNodes[$key]->prepare();

    parent :: prepare();
  }
}
?>