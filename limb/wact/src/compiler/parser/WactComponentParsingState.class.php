<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactComponentParsingState.class.php 5244 2007-03-14 14:35:20Z serega $
 * @package    wact
 */

require_once('limb/wact/src/compiler/parser/WactParserListener.interface.php');
require_once('limb/wact/src/compiler/parser/WactBaseParsingState.class.php');

define ('PARSER_TAG_IS_COMPONENT', 	1);
define ('PARSER_TAG_IS_PLAIN', 		2);
define ('WACT_PARSER_FORBID_PARSING', 		10);

class WactComponentParsingState extends WactBaseParsingState implements WactParserListener
{
  protected $tag_dictionary;

  protected $node_builder;

  function __construct($parser, $tree_builder, $node_builder, $tag_dictionary)
  {
    parent :: __construct($parser, $tree_builder);

    $this->node_builder = $node_builder;
    $this->tag_dictionary = $tag_dictionary;
  }

  function setDocumentLocator($locator)
  {
    parent :: setDocumentLocator($locator);

    $this->node_builder->setDocumentLocator($locator);
  }

  function startElement($tag, $attrs)
  {
    $location = $this->locator->getCurrentLocation();

    $lower_attributes = $this->checkAttributes($attrs, $location);

    $WactTagInfo = $this->tag_dictionary->findTagInfo($tag, $lower_attributes, FALSE, $this->tree_builder->getCursor());

    if (is_object($WactTagInfo))
    {
      if ($WactTagInfo->isEndTagForbidden() && WACT_STRICT_MODE)
      {
        throw new WactException('Closing tag is forbidden for this tag. Use self closing notation',
                                array('tag' => $WactTagInfo->Tag,
                                      'file' => $location->getFile(),
                                      'line' => $location->getLine()));
      }

      $WactTagInfo->load();

      $this->tree_builder->pushExpectedTag($tag, PARSER_TAG_IS_COMPONENT, $location);

      $tag_node = $this->node_builder->buildTagNode($WactTagInfo, $tag, $attrs, FALSE);

      $result = $this->tree_builder->pushNode($tag_node);

      if (($result == WACT_PARSER_FORBID_PARSING) || $WactTagInfo->isParsingForbidden())
        $this->parser->changeToLiteralParsingState($tag);

      // Cleanup for components that have no closing tag in none strict parsint mode
      if ($WactTagInfo->isEndTagForbidden() && !WACT_STRICT_MODE)
      {
        $this->tree_builder->popNode(FALSE);
        $this->tree_builder->popExpectedTag($tag, $location);
        $this->parser->changeToComponentParsingState();
      }
    }
    else
    {
      $this->tree_builder->pushExpectedTag($tag, PARSER_TAG_IS_PLAIN, $location);
      $this->node_builder->addContent('<' . $tag . $this->getAttributeString($attrs) . '>');
    }
  }

  /**
  * Handle closing tags
  * @param string tag name
  * @access public
  */
  function endElement($tag)
  {
    $WactTagInfo = $this->tag_dictionary->getWactTagInfo($tag);
    $location = $this->locator->getCurrentLocation();

    if (is_object($WactTagInfo))
    {
      if ($WactTagInfo->isEndTagForbidden())
      {
        throw new WactException('Closing tag forbidden',
                                array('tag' => $WactTagInfo->Tag,
                                      'file' => $location->getFile(),
                                      'line' => $location->getLine()));
      }
    }

    if ($this->tree_builder->popExpectedTag($tag, $location) == PARSER_TAG_IS_COMPONENT)
      $this->tree_builder->popNode(TRUE);
    else
      $this->tree_builder->addWactTextNode('</' . $tag .'>');
  }

  function emptyElement($tag, $attrs)
  {
    $location = $this->locator->getCurrentLocation();
    $lower_attributes = $this->checkAttributes($attrs, $location);

    $WactTagInfo = $this->tag_dictionary->findTagInfo($tag, $lower_attributes, TRUE, $this->tree_builder->getCursor());
    if (is_object($WactTagInfo))
    {
      $WactTagInfo->load();

      $tag_node = $this->node_builder->buildTagNode($WactTagInfo, $tag, $attrs, TRUE);
      $this->tree_builder->pushNode($tag_node);

      if ($WactTagInfo->isParsingForbidden())
        $this->parser->changeToLiteralParsingState($tag);

       // Cleanup for components that have no closing tag
      if ( $WactTagInfo->isEndTagForbidden())
      {
        $this->parser->changeToComponentParsingState();
        $this->tree_builder->popNode(FALSE);
      }
      else
        $this->tree_builder->popNode(TRUE);
    }
    else
      $this->node_builder->addContent('<' . $tag . $this->getAttributeString($attrs) . ' />');
  }

  /**
  * Transforms attributes so keys are lowercase, and checks for
  * illegal DBEs
  */
  function checkAttributes($attrs, $location)
  {
    $lower_attributes = array_change_key_case($attrs, CASE_LOWER);
    if ( isset($lower_attributes['runat']) &&
            strpos($lower_attributes['runat'], '{$') !== FALSE)
    {
        throw new WactException('Illegal use of variable reference for attribute',
                                array('expression' => $lower_attributes['runat'],
                                      'file' => $location->getFile(),
                                      'line' => $location->getLine()));
    }
    return $lower_attributes;
  }

  function characters($text)
  {
    $this->node_builder->addContent($text);
  }

  function cdata($text)
  {
    $this->node_builder->addContent('<![CDATA[' . $text . ']]>');
  }

  function processingInstruction($target, $instruction)
  {
    $this->node_builder->addProcessingInstruction($target, $instruction);
  }

  function jasp($text)
  {
    $this->node_builder->addContent('<%' . $text . '%>');
  }

  function escape($text)
  {
    $this->node_builder->addContent('<!' . $text . '>');
  }

  function doctype($text)
  {
    $this->node_builder->addContent('<!' . $text . '>');
  }

  function comment($text)
  {
    $this->node_builder->addContent('<!--' . $text . '-->');
  }

  function unexpectedEOF($text)
  {
    $this->node_builder->addContent($text);
  }

  function invalidEntitySyntax($text)
  {
    $this->node_builder->addContent($text);
  }
}
?>