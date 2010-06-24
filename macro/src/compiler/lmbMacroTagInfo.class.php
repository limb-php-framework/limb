<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMacroTagInfo.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTagInfo
{
  protected $tag;
  protected $class;
  protected $file;
  protected $aliases = array();
  protected $req_attributes = array();
  protected $parent_class;
  protected $restrict_self_nesting = false;
  protected $require_endtag = true;

  function __construct($tag, $class, $require_endtag = true)
  {
    $this->tag = $tag;
    $this->class = $class;
    $this->require_endtag = $require_endtag;
  }

  static function createByAnnotations($file, $class, $annotations)
  {
    if(!isset($annotations['tag']))
      throw new lmbMacroException("@tag annotation is missing for class '$class'");

    $tag = $annotations['tag'];
    $info = new lmbMacroTagInfo($tag, $class);

    $info->setFile($file);

    if(isset($annotations['forbid_end_tag']))
      $info->setForbidEndtag(true);

    if(isset($annotations['restrict_self_nesting']))
      $info->setRestrictSelfNesting(true);

    if(isset($annotations['parent_tag_class']))
      $info->setParentClass(trim($annotations['parent_tag_class']));
    
    if(isset($annotations['req_attributes']))
    {
      $req_attributes = explode(',' , $annotations['req_attributes']);
      $req_attributes = array_map('trim', $req_attributes);
      $info->setRequiredAttributes($req_attributes);
    }
    
    if(isset($annotations['aliases']))
    {
      $filter_aliases = explode(',' , $annotations['aliases']);
      $filter_aliases = array_map('trim', $filter_aliases);
      $info->setAliases($filter_aliases);
    }

    return $info;
  }

  function getTag()
  {
    return $this->tag;
  }

  function getClass()
  {
    return $this->class;
  }

  function setFile($file)
  {
    $this->file = $file;
  }

  function getFile()
  {
    return $this->file;
  }

  function setForbidEndtag($flag = true)
  {
    $this->require_endtag = !$flag;
  }

  function isEndtagForbidden()
  {
    return !$this->require_endtag;
  }

  function setRequiredAttributes($attributes)
  {
    $this->req_attributes = $attributes;
  }

  function getRequiredAttributes()
  {
    return $this->req_attributes;
  }

  function setParentClass($parent_tag_class)
  {
    $this->parent_class = $parent_tag_class;
  }

  function getParentClass()
  {
    return $this->parent_class;
  }

  function setAliases($aliases)
  {
    $this->aliases = $aliases;
  }
  
  function getAliases()
  {
    return $this->aliases;
  }
  
  function setRestrictSelfNesting($flag = true)
  {
    $this->restrict_self_nesting = $flag;
  }

  function isRestrictSelfNesting()
  {
    return $this->restrict_self_nesting;
  }

  function setForbidParsing($flag = true)
  {
    $this->forbid_parsing = $flag;
  }

  function isParsingForbidden()
  {
    return $this->forbid_parsing;
  }

  function load()
  {
    if(!class_exists($this->class) && isset($this->file))
      require_once($this->file);
  }
}

