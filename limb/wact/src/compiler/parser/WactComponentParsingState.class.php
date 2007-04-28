<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactComponentParsingState.class.php 5780 2007-04-28 13:03:26Z serega $
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

    $runtime_tag_info = $this->tag_dictionary->findTagInfo($tag, $lower_attributes, FALSE, $this->tree_builder->getCursor());

    if (is_object($runtime_tag_info))
    {
      $runtime_tag_info->load();

      if($runtime_tag_info->isEndTagForbidden())
      {
        $tag_node = $this->node_builder->buildTagNode($runtime_tag_info, $tag, $attrs, $self_closed_tag = TRUE);
        $tag_node->hasClosingTag = false;
        $this->tree_builder->pushNode($tag_node); // for cases like <core:include> we do pushNode() and popNode() here.
        $this->tree_builder->popNode();
      }
      else
      {
        $this->tree_builder->pushExpectedTag($tag, PARSER_TAG_IS_COMPONENT, $location);
        $tag_node = $this->node_builder->buildTagNode($runtime_tag_info, $tag, $attrs, $self_closed_tag = FALSE);
        $result = $this->tree_builder->pushNode($tag_node);

        if (($result == WACT_PARSER_FORBID_PARSING) || $runtime_tag_info->isParsingForbidden())
          $this->parser->changeToLiteralParsingState($tag);
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
    $tag_info = $this->tag_dictionary->getWactTagInfo($tag);
    $location = $this->locator->getCurrentLocation();

    if (is_object($tag_info))
    {
      if ($tag_info->isEndTagForbidden())
      {
        throw new WactException('Closing tag forbidden',
                                array('tag' => $tag_info->Tag,
                                      'file' => $location->getFile(),
                                      'line' => $location->getLine()));
      }
    }

    if($tag_info && ($tag_info->getRunat() == LOCATION_SERVER))
      $info = PARSER_TAG_IS_COMPONENT;
    else
      $info = PARSER_TAG_IS_PLAIN;

    if ($this->tree_builder->popExpectedTag($tag, $location, $info) == PARSER_TAG_IS_COMPONENT)
      $this->tree_builder->popNode();
    else
      $this->tree_builder->addWactTextNode('</' . $tag .'>');
  }

  function emptyElement($tag, $attrs)
  {
    $location = $this->locator->getCurrentLocation();
    $lower_attributes = $this->checkAttributes($attrs, $location);

    $runtime_tag_info = $this->tag_dictionary->findTagInfo($tag, $lower_attributes, TRUE, $this->tree_builder->getCursor());
    if (is_object($runtime_tag_info))
    {
      $runtime_tag_info->load();

      $tag_node = $this->node_builder->buildTagNode($runtime_tag_info, $tag, $attrs, $self_closed_tag = TRUE);
      $tag_node->hasClosingTag = false;
      $this->tree_builder->pushNode($tag_node); // for cases like <core:include> we do pushNode() and popNode() here.
      $this->tree_builder->popNode();
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