<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @package macro
 * @version $Id$
 */
class lmbMacroBaseParsingState
{
  /**
  * @var lmbMacroSourceFileParser
  */
  protected $parser;

  /**
  * @var lmbMacroTreeBuilder
  */
  protected $tree_builder;

  protected $locator;

  function __construct($parser, $tree_builder)
  {
    $this->parser = $parser;
    $this->tree_builder = $tree_builder;
  }

  function setTemplateLocator($locator)
  {
    $this->locator = $locator;
  }

  function invalidAttributeSyntax($data)
  {
    throw new lmbException('Invalid attribute syntax starting from: ' . $data);
  }

  function getAttributeString($attrs)
  {
    $attrib_str = '';
    foreach($attrs as $key => $value)
    {
      $attrib_str .= ' ' . $key;
      if(!is_null($value))
      {
        if(strpos($value, '"') === FALSE)
          $attrib_str .= '="' . $value . '"';
        else
          $attrib_str .= '=\'' . $value . '\'';
      }
    }
    return $attrib_str;
  }
}

