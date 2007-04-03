<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactBaseParsingState.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* Base state handler for the WactSourceFileParser.
*/
abstract class WactBaseParsingState
{
  /**
  * @var WactSourceFileParser
  */
  protected $parser;

  /**
  * Used to locate position within source template
  */
  protected $locator;

  /**
  * @var WactTreeBuilder
  */
  protected $tree_builder;

  function __construct($parser, $tree_builder)
  {
    $this->parser = $parser;
    $this->tree_builder = $tree_builder;
  }

  function setDocumentLocator($locator)
  {
    $this->locator = $locator;
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

  protected function _addTextNode($text)
  {
    $location = null; // we never care where text nodes are
    $this->tree_builder->addNode(new WactTextNode($location, $text));
  }

  function invalidAttributeSyntax()
  {
    throw new WactException('Attribute syntax error',
                            array('file' => $this->locator->getPublicId(),
                                  'line' => $this->locator->getLineNumber()));
  }
}
?>