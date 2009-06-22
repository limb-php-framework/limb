<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMacroTagParsingState.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTagParsingState extends lmbMacroBaseParsingState implements lmbMacroTokenizerListener
{
  protected $tag_dictionary;

  function __construct(lmbMacroParser $parser, lmbMacroTreeBuilder $tree_builder, lmbMacroTagDictionary $tag_dictionary)
  {
    parent :: __construct($parser, $tree_builder);
    $this->tag_dictionary = $tag_dictionary;
  }
  
  function raiseNotSuchTagException($tag, $location)
  {
    $params = array('file', $location->getFile(),
                    'line', $location->getLine());
    
    throw new lmbMacroException("Tag '$tag' not found in dictionary", $params);
  }

  function startElement($tag, $attrs)
  {
    $location = $this->parser->getCurrentLocation();

    $lower_attributes = $this->normalizeAttributes($attrs, $location);

    if(!$tag_info = $this->tag_dictionary->findTagInfo($tag))
      $this->raiseNotSuchTagException($tag, $location);

    if($tag_info->isEndTagForbidden())
    {
      $tag_node = $this->buildTagNode($tag_info, $tag, $attrs);
      $tag_node->setHasClosingTag(false);
      $this->tree_builder->pushNode($tag_node); // for cases like {{include}} we do pushNode() and popNode() here.
      $this->tree_builder->popNode();
    }
    else
    {
      $this->tree_builder->pushExpectedTag($tag, $location);
      $tag_node = $this->buildTagNode($tag_info, $tag, $attrs, $self_closed_tag = false);
      $result = $this->tree_builder->pushNode($tag_node);
    }
  }

  function endElement($tag)
  {
    $location = $this->parser->getCurrentLocation();
	
    if(!$tag_info = $this->tag_dictionary->findTagInfo($tag))
      $this->raiseNotSuchTagException($tag, $location);
    
    if($tag_info->isEndTagForbidden())
    {
      throw new lmbMacroException('Closing tag forbidden',
                              array('tag' => $tag_info->getTag(),
                                    'file' => $location->getFile(),
                                    'line' => $location->getLine()));
    }

    $this->tree_builder->popExpectedTag($tag, $location);
    $this->tree_builder->popNode();
  }

  function emptyElement($tag, $attrs)
  {
    $location = $this->parser->getCurrentLocation();
    $lower_attributes = $this->normalizeAttributes($attrs, $location);

    if(!$tag_info = $this->tag_dictionary->findTagInfo($tag))
      $this->raiseNotSuchTagException($tag, $location);
    $tag_info->load();

    $tag_node = $this->buildTagNode($tag_info, $tag, $attrs);
    $tag_node->setHasClosingTag(false);
    $this->tree_builder->pushNode($tag_node); // for cases like {{include}} we do pushNode() and popNode() here.
    $this->tree_builder->popNode();
  }

  /**
  * Builds a component, adding attributes
  * @param lmbMacroTagInfo
  * @param string XML tag name of component
  * @param array attributes for tag
  * @param boolean whether the tag has contents
  * @return lmbMacroNode
  */
  function buildTagNode($tag_info, $tag, $attrs)
  {
    $tag_node = $this->_createTagNode($tag_info, $tag);
    $this->_addAttributesToTagNode($tag_node, $attrs);
    return $tag_node;
  }

  protected function _addAttributesToTagNode($tag_node, $attrs)
  {
    foreach($attrs as $name => $value)
    {
      if($value === null)
      {
        $location = $this->parser->getCurrentLocation();
        throw new lmbMacroException('Attribute should have a value',
                              array('file' => $location->getFile(),
                                    'line' => $location->getLine(),
                                    'tag' => $tag_node->getTag(),
                                    'attribute' => $name));
      }

      $attribute = new lmbMacroTagAttribute($name);
      $listener = new lmbMacroTagAttributeBlockAnalizerListener($attribute, $tag_node);

      $analizer = new lmbMacroBlockAnalizer();
      $analizer->parse($value, $listener);

      $tag_node->add($attribute);
    }
  }

  protected function _createTagNode($tag_info, $tag)
  {
    $class = $tag_info->getClass();
    require_once($tag_info->getFile());
    $tag_node = new $class($this->parser->getCurrentLocation(), $tag, $tag_info);
    return $tag_node;
  }

  function normalizeAttributes($attrs)
  {
    return array_change_key_case($attrs, CASE_LOWER);
  }

  function characters($text)
  {
    $this->tree_builder->addContent($text, $this->parser->getCurrentLocation());
  }

  function php($text)
  {
    $this->tree_builder->addTextNode($text);
  }

  function unexpectedEOF($text)
  {
    $this->tree_builder->addTextNode($text);
  }

  function invalidEntitySyntax($text)
  {
    $this->tree_builder->addTextNode($text);
  }
}

