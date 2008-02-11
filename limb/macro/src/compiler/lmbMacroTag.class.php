<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMacroTag.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTag extends lmbMacroNode
{
  protected $tag;
  protected $tag_info;
  protected $has_closing_tag = true;
  protected $attributes = array();

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

  function getHasClosingTag()
  {
    return $this->has_closing_tag;
  }

  function setHasClosingTag($flag)
  {
    return $this->has_closing_tag = $flag;
  }

  function getNodeId()
  {
    if($this->node_id)
      return $this->node_id;

    if($this->hasConstant('id'))
      $this->node_id = $this->get('id');
    else
      $this->node_id = self :: generateNewId();

    return $this->node_id;
  }
  
  function getEscapedNodeId()
  {
    $id = $this->getNodeId();
    return "'" .  $id . "'";
  }

  function get($name)
  {
    if(!array_key_exists(strtolower($name), $this->attributes))
      return;

    return $this->attributes[strtolower($name)]->getValue();
  }
  
  function getAttributeObject($name)
  {
    if(!array_key_exists(strtolower($name), $this->attributes))
      return;

    return $this->attributes[strtolower($name)];
  }

  function getEscaped($name)
  {
    if(!$this->has($name))
      return;
      
    $value = $this->get($name);
    if($this->isDynamic($name))
      return $value;
    else
      return "'" .  $value . "'";
  }

  /**
  * Should be used for testing purposes only since not parses $value for any output expressions
  */
  function set($name, $value)
  {
    $this->attributes[strtolower($name)] = new lmbMacroTagAttribute($name, $value);
  }

  function add($attribute)
  {
    $this->attributes[strtolower($attribute->getName())] = $attribute;
  }

  function has($name)
  {
    return array_key_exists(strtolower($name), $this->attributes);
  }
  
  function hasConstant($name)
  {
    return $this->has($name) && !$this->attributes[strtolower($name)]->isDynamic();
  }

  function isDynamic($name)
  {
    return !$this->hasConstant($name);
  }
  
  function getConstantAttributes()
  {
    $res = array();
    foreach($this->attributes as $key => $attr)
    {
      if(!$attr->isDynamic())
        $res[$attr->getName()] = $attr->getValue();
    }
    return $res;
  }

  /**
  * Return the value of a boolean attribute as a boolean.
  * ATTRIBUTE=ANYTHING  (true)
  * ATTRIBUTE=(false|N|NA|NO|NONE|0) (false)
  * ATTRIBUTE (true)
  * (attribute unspecified) (default)
  */
  function getBool($name, $default = false)
  {
    if(!isset($this->attributes[strtolower($name)]))
      return $default;

    return self :: getBooleanValue($this->attributes[strtolower($name)]->getValue());
  }

  static function getBooleanValue($value)
  {
    if(!$value)
      return $value;

    switch(strtoupper($value))
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

  function generate($code_writer)
  {
    $this->_preGenerateAttributes($code_writer);
    
    $this->_generateBeforeContent($code_writer);
    
    $this->_generateContent($code_writer);
    
    $this->_generateAfterContent($code_writer);
  }
  
  // children can override this method if they need to generate some code around content in simple cases
  // but it's recommended to override generateBeforeContent() and generateAfterContent() instead.
  protected function _generateContent($code_writer)
  {
    parent :: generate($code_writer);
  }
 
  protected function _generateBeforeContent($code_writer)
  {
  }

  protected function _generateAfterContent($code_writer)
  {
  }
  
  protected function _preGenerateAttributes($code_writer)
  {
    foreach($this->attributes as $attribute)
      $attribute->preGenerate($code_writer);
  }

  function remove($attrib)
  {
    unset($this->attributes[strtolower($attrib)]);
  }

  function raise($error, $vars = array())
  {
    $vars['tag'] = $this->tag;
    parent :: raise($error, $vars);
  }

  function raiseRequiredAttribute($attribute_name)
  {
    $this->raise('Missing required attribute', array('attribute' => $attribute_name));
  }

  function preParse($compiler)
  {
    foreach($this->tag_info->getRequiredAttributes() as $attr_name)
    {
      if(!$this->has($attr_name))
        $this->raiseRequiredAttribute($attr_name);
    }

    if($this->tag_info->isRestrictSelfNesting() && $parent = $this->findParentByClass(get_class($this)))
      $this->raise('Tag cannot be nested within the same tag',
                                array('same_tag_file' => $parent->getTemplateFile(),
                                      'same_tag_line' => $parent->getTemplateLine()));

    if(($parent_class = $this->tag_info->getParentClass()) &&
       !$parent = $this->findParentByClass($parent_class))
    {
      $this->raise('Tag must be enclosed by a proper parent tag',
                                array('required_parent_tag_class' => $parent_class));

    }
  }
  
  function attributesIntoArgs()
  {
    $keys = array();
    $vals = array();
    foreach($this->attributes as $k => $attribute)
    {
      $keys[] = '$' . $attribute->getName();
      $vals[] = $this->getEscaped($k);
    }
    return array($keys, $vals);
  }

  function attributesIntoArray($skip = array())
  {
    $arr = array();
    foreach($this->attributes as $k => $attribute)
    {
      $name = $attribute->getName();
      if(in_array($name, $skip))
        continue;
      
      $arr[$name] = $this->getEscaped($k);
    }
    return $arr;
  }
  
  function attributesIntoArrayString($skip = array())
  {
    $args = $this->attributesIntoArray($skip);

    $arg_str = 'array(';
    foreach($args as $key => $value)
      $arg_str .= "'$key' => $value,";
    $arg_str .= ')';
    
    return $arg_str;
  }
}

