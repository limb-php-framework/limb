<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/compiler/parser/WactHTMLParserListener.interface.php');
require_once('limb/wact/src/compiler/parser/WactBaseParsingState.class.php');

define ('WACT_PARSER_FORBID_PARSING', 10);

/**
 * class WactComponentParsingState.
 *
 * @package wact
 * @version $Id: WactComponentParsingState.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactComponentParsingState extends WactBaseParsingState implements WactHTMLParserListener
{
  protected $tag_dictionary;

  protected $node_builder;

  function __construct($parser, $tree_builder, $tag_dictionary)
  {
    parent :: __construct($parser, $tree_builder);

    $this->tag_dictionary = $tag_dictionary;
  }

  function startTag($tag, $attrs, $location)
  {
    $lower_attributes = $this->checkAttributes($attrs, $location);

    $runtime_tag_info = $this->tag_dictionary->findTagInfo($tag, $lower_attributes, FALSE, $this->tree_builder->getCursor());

    if (is_object($runtime_tag_info))
    {
      $runtime_tag_info->load();

      if($runtime_tag_info->isEndTagForbidden())
      {
        $tag_node = $this->tree_builder->buildTagNode($runtime_tag_info, $tag, $location, $attrs, $self_closed_tag = FALSE, $has_closing_tag = false);
        // for cases like <core:include> or <core:wrap> we have to pushNode() and popNode() here.
        $this->tree_builder->pushNode($tag_node);
        $this->tree_builder->popNode();
      }
      else
      {
        $this->tree_builder->pushExpectedWactTag($tag, $location);
        $tag_node = $this->tree_builder->buildTagNode($runtime_tag_info, $tag, $location, $attrs);
        $result = $this->tree_builder->pushNode($tag_node);

        if (($result == WACT_PARSER_FORBID_PARSING) || $runtime_tag_info->isParsingForbidden())
          $this->parser->changeToLiteralParsingState($tag);
      }
    }
    else
    {
      $this->tree_builder->pushExpectedPlainTag($tag, $location);
      $this->tree_builder->addContent('<' . $tag . $this->getAttributeString($attrs) . '>', $location);
    }
  }

  function endTag($tag, $location)
  {
    $tag_info = $this->tag_dictionary->getWactTagInfo($tag);

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
      $result = $this->tree_builder->popExpectedWactTag($tag, $location);
    else
      $result = $this->tree_builder->popExpectedPlainTag($tag, $location);

    if ($result == WACT_EXPECTED_WACT_TAG)
      $this->tree_builder->popNode();
    else
      $this->tree_builder->addWactTextNode('</' . $tag .'>');
  }

  function emptyTag($tag, $attrs, $location)
  {
    $lower_attributes = $this->checkAttributes($attrs, $location);

    $runtime_tag_info = $this->tag_dictionary->findTagInfo($tag, $lower_attributes, TRUE, $this->tree_builder->getCursor());
    if (is_object($runtime_tag_info))
    {
      $runtime_tag_info->load();

      $tag_node = $this->tree_builder->buildTagNode($runtime_tag_info, $tag, $location, $attrs, $self_closed_tag = TRUE, $has_closing_tag = false);
      // for cases like <core:include> or <core:wrap> we have to pushNode() and popNode() here.
      $this->tree_builder->pushNode($tag_node);
      $this->tree_builder->popNode();
    }
    else
      $this->tree_builder->addContent('<' . $tag . $this->getAttributeString($attrs) . ' />', $location);
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

  function characters($text, $location)
  {
    $this->tree_builder->addContent($text, $location);
  }

  function instruction($target, $instruction, $location)
  {
    $this->tree_builder->addProcessingInstruction($target, $instruction, $location);
  }
}

