<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbPHPTokenizer.class.php');
lmb_require('limb/macro/src/lmbMacroException.class.php');

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

    if(isset($annotations['endtag']) && $annotations['endtag'] == 'no')
      $info->setForbidEndtag(true);

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

