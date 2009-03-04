<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactTagInfoExtractor.
 *
 * @package wact
 * @version $Id: WactTagInfoExtractor.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactTagInfoExtractor
{
  protected $dictionary;
  protected $file;
  protected $annotations = array();

  function __construct($dict, $file)
  {
    $this->dictionary = $dict;
    $this->file = $file;
  }

  function setCurrentFile($file)
  {
    $this->file = $file;
  }

  function annotation($name, $value)
  {
    $this->annotations[$name] = $value;
  }

  function beginClass($class, $parent_class)
  {
    $this->_validate();

    $tag_aliases = explode(',' , $this->annotations['tag']);
    foreach($tag_aliases as $tag)
    {
      $info = new WactTagInfo(trim($tag), $class);

      $this->_fillTagInfo($info);

      $this->dictionary->registerWactTagInfo($info, $this->file);
    }
  }

  protected function _fillTagInfo($info)
  {
    if(isset($this->annotations['suppress_attributes']))
    {
      $attrs = $this->_processAttributesString($this->annotations['suppress_attributes']);
      $info->setSuppressAttributes($attrs);
    }

    if(isset($this->annotations['req_attributes']))
    {
      $attrs = $this->_processAttributesString($this->annotations['req_attributes']);
      $info->setRequiredAttributes($attrs);
    }

    if(isset($this->annotations['req_const_attributes']))
    {
      $attrs = $this->_processAttributesString($this->annotations['req_const_attributes']);
      $info->setRequiredConstantAttributes($attrs);
    }

    if(array_key_exists('forbid_parsing', $this->annotations))
      $info->setForbidParsing();

    if(isset($this->annotations['parent_tag_class']))
      $info->setParentTagClass($this->annotations['parent_tag_class']);

    if(array_key_exists('restrict_self_nesting', $this->annotations))
      $info->setRestrictSelfNesting();

    if(array_key_exists('forbid_end_tag', $this->annotations))
      $info->setForbidEndTag();

    if(isset($this->annotations['runat']))
      $info->setRunat($this->annotations['runat']);

    if(isset($this->annotations['runat_as']))
      $info->setRunatAs($this->annotations['runat_as']);

    // this code added to support old form of DBE expressions in some attributes
    // like <core:optional for='var'> should actually be <core:optional for='{$var}'>
    if(isset($this->annotations['convert_to_expression']))
    {
      $attrs = $this->_processAttributesString($this->annotations['convert_to_expression']);
      $info->setConvertAttributesToExpressions($attrs);
    }
  }

  function endClass()
  {
    $this->annotations = array();
  }

  function _processAttributesString($attributes_string)
  {
    return explode(' ', preg_replace('~\s+~', ' ', trim($attributes_string)));
  }

  function _validate()
  {
    if(!file_exists($this->file))
      throw new WactException('File not found', array('file' => $this->file));

    if(!isset($this->annotations['tag']))
      throw new WactException('Annotation not found in file',
                              array('annotation' => 'tag', 'file' => $this->file));
  }
}
