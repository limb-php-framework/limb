<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactTagInfo.
 *
 * @package wact
 * @version $Id: WactTagInfo.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactTagInfo
{
  public $Tag = '';
  public $TagClass = '';
  public $SuppressAttributes = array();
  public $Runat = 'server';
  public $RunatAs;
  public $File;
  public $RequiredAttributes = array();
  public $RequiredConstantAttributes = array();
  public $ParentTagClass;
  public $RestrictSelfNesting = false;
  public $ForbidParsing = false;
  public $ForbidEndTag = false;
  // this code here for BC only
  public $ConvertAttributesToExpressions = array();

  function WactTagInfo($tag, $class)
  {
    $this->Tag = $tag;
    $this->TagClass = $class;
  }

  function setForbidEndTag($flag = true)
  {
    $this->ForbidEndTag = $flag;
  }

  function isEndTagForbidden()
  {
    return $this->ForbidEndTag;
  }

  function setSuppressAttributes($attributes)
  {
    $this->SuppressAttributes = $attributes;
  }

  function getSuppressAttributes()
  {
    return $this->SuppressAttributes;
  }

  function setRequiredAttributes($attributes)
  {
    $this->RequiredAttributes = $attributes;
  }

  function getRequiredAttributes()
  {
    return $this->RequiredAttributes;
  }

  function setRequiredConstantAttributes($attributes)
  {
    $this->RequiredConstantAttributes = $attributes;
  }

  function getRequiredConstantAttributes()
  {
    return $this->RequiredConstantAttributes;
  }

  function setParentTagClass($parent_tag_class)
  {
    $this->ParentTagClass = $parent_tag_class;
  }

  function getParentTagClass()
  {
    return $this->ParentTagClass;
  }

  function setRunatAs($tag_class_name)
  {
    $this->RunatAs = $tag_class_name;
  }

  function getRunatAs()
  {
    return $this->RunatAs;
  }

  function setRunat($runat)
  {
    $this->Runat = $runat;
  }

  function getRunat()
  {
    return $this->Runat;
  }

  function setRestrictSelfNesting($flag = true)
  {
    $this->RestrictSelfNesting = $flag;
  }

  function isRestrictSelfNesting()
  {
    return (boolean)$this->RestrictSelfNesting;
  }

  function setForbidParsing($flag = true)
  {
    $this->ForbidParsing = $flag;
  }

  function isParsingForbidden()
  {
    return $this->ForbidParsing;
  }

  function load()
  {
    if (!class_exists($this->TagClass) && isset($this->File))
      require_once $this->File;
  }

  // this code here for BC only
  function setConvertAttributesToExpressions($attributes)
  {
    $this->ConvertAttributesToExpressions = $attributes;
  }

  function getConvertAttributesToExpressions()
  {
    return $this->ConvertAttributesToExpressions;
  }
}

