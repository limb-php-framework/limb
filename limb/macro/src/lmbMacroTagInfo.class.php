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

  static function extractFromFile($file)
  {
    $infos = array();
    $tokenizer = new lmbPHPTokenizer(file_get_contents($file));
    while($token = $tokenizer->next())
    {
      if(!is_array($token))
        continue;

      //found class token
      if($token[0] == T_CLASS)
      {
        //fetching class name
        $token = $tokenizer->next();
        $class = $token[1]; 

        //now checking prev token for /**/
        if(!is_array($prev_token) || $prev_token[0] != T_DOC_COMMENT)
          throw new lmbMacroException('Invalid token, doc comment is expected');

        //now parsing annotations
        $annotations = self :: _extractAnnotations($prev_token[1]);
        if(!$annotations)
          throw new lmbMacroException("No annotations found in doc comment '{$prev_token[1]}' in file $file");

        $infos[] = self :: createByAnnotations($class, $annotations);
      }
      $prev_token = $token;
    }
    return $infos;
  }

  static function createByAnnotations($class, $annotations)
  {
    if(!isset($annotations['tag']))
      throw new lmbMacroException("@tag annotation is missing for class '$class'");

    $tag = $annotations['tag'];
    $info = new lmbMacroTagInfo($tag, $class);

    if(isset($annotations['endtag']) && $annotations['endtag'] == 'no')
      $info->setForbidEndtag(true);

    return $info;
  }

  static protected function _extractAnnotations($content)
  {
    if(!preg_match_all('~@(\S+)([^\n]+)?\n~', $content, $matches))
      return false;
    $annotations = array();
    for($i=0;$i<count($matches[0]);$i++)
      $annotations[trim($matches[1][$i])] = trim($matches[2][$i]);
    return $annotations;
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

