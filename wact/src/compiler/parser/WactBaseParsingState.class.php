<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Base state handler for the WactSourceFileParser.
 * @package wact
 * @version $Id: WactBaseParsingState.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
abstract class WactBaseParsingState
{
  /**
  * @var WactSourceFileParser
  */
  protected $parser;

  /**
  * @var WactTreeBuilder
  */
  protected $tree_builder;

  function __construct($parser, $tree_builder)
  {
    $this->parser = $parser;
    $this->tree_builder = $tree_builder;
  }

  function getAttributeString($attrs)
  {
    $attrib_str = '';
    foreach ( $attrs as $key => $value )
    {
      if (strcasecmp($key, 'runat') == 0)
        continue;

      $attrib_str .= ' ' . $key;
      if (!is_null($value))
      {
        if (strpos($value, '"') === FALSE)
          $attrib_str .= '="' . $value . '"';
        else
          $attrib_str .= '=\'' . $value . '\'';
      }
    }
    return $attrib_str;
  }
}

