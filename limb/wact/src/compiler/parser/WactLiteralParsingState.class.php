<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactLiteralParsingState.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/wact/src/compiler/parser/WactParserListener.interface.php');
require_once('limb/wact/src/compiler/parser/WactBaseParsingState.class.php');

class WactLiteralParsingState extends WactBaseParsingState  implements WactParserListener
{
  protected $literal_tag;

  function setLiteralTag($tag)
  {
    $this->literal_tag = $tag;
  }

  function getLiteralTag()
  {
    return $this->literal_tag;
  }

  function startElement($tag, $attrs)
  {
    $this->tree_builder->addWactTextNode('<' . $tag . $this->getAttributeString($attrs) . '>');
  }

  function endElement($tag)
  {
    if ($this->literal_tag == $tag)
    {
      $location = $this->locator->getCurrentLocation();
      $this->tree_builder->popExpectedTag($tag, $location);
      $this->tree_builder->popNode(TRUE);
      $this->parser->changeToComponentParsingState();
    }
    else
      $this->tree_builder->addWactTextNode('</' . $tag . '>');
  }

  function emptyElement($tag, $attrs)
  {
    $this->tree_builder->addWactTextNode('<' . $tag . $this->getAttributeString($attrs) . ' />');
  }

  function characters($text)
  {
    $this->tree_builder->addWactTextNode($text);
  }

  function cdata($text)
  {
    $this->tree_builder->addWactTextNode('<![CDATA[' . $text . ']]>');
  }

  function processingInstruction($target, $instruction)
  {
    $this->tree_builder->addWactTextNode('<?' . $target . ' ' . $instruction . '?>');
  }

  function jasp($text)
  {
    $this->tree_builder->addWactTextNode('<%' .  $text . '%>');
  }

  function escape($text)
  {
    $this->tree_builder->addWactTextNode('<!' . $text . '>');
  }

  function comment($text)
  {
    $this->tree_builder->addWactTextNode('<!--' . $text . '-->');
  }

  function doctype($text)
  {
    $this->tree_builder->addWactTextNode('<!' . $text . '>');
  }

  function unexpectedEOF($text)
  {
    $this->tree_builder->addWactTextNode($text);
  }

  function invalidEntitySyntax($text)
  {
    $this->tree_builder->addWactTextNode($text);
  }
}
?>