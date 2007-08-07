<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
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
  protected $tag = '';
  protected $class = '';
  protected $file;
  protected $req_attributes = array();
  protected $parent_class;
  protected $restrict_self_nesting = false;
  protected $forbid_parsing = false;
  protected $forbid_endtag = false;

  function __construct($tag, $class)
  {
    $this->tag = $tag;
    $this->class = $class;
  }
  
  function getTag()
  {
    return $this->tag;
  }
  
  function setFile($file)
  {
    $this->file = $file;
  }

  function setForbidEndtag($flag = true)
  {
    $this->forbid_endtag = $flag;
  }

  function isEndtagForbidden()
  {
    return $this->forbid_endtag;
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

