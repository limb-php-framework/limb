<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/lmbMacroTokenizerListener.interface.php');
lmb_require('limb/macro/src/lmbMacroBaseParsingState.class.php');

/**
 * class lmbMacroTagParsingState.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTagParsingState extends lmbMacroBaseParsingState implements lmbMacroTokenizerListener
{
  protected $tag_dictionary;

  function __construct($parser, $tree_builder, $tag_dictionary)
  {
    parent :: __construct($parser, $tree_builder);
    $this->tag_dictionary = $tag_dictionary;
  }

  function startElement($tag, $attrs)
  {
    $location = $this->parser->getCurrentLocation();

    $lower_attributes = $this->normalizeAttributes($attrs, $location);

    $tag_info = $this->tag_dictionary->findTagInfo($tag);

    if($tag_info->isEndTagForbidden())
    {
      $tag_node = $this->buildTagNode($tag_info, $tag, $attrs, $self_closed_tag = true);
      $tag_node->setHasClosingTag(false);
      $this->tree_builder->pushNode($tag_node); // for cases like <%include%> we do pushNode() and popNode() here.
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
    $tag_info = $this->tag_dictionary->findTagInfo($tag);
    $location = $this->parser->getCurrentLocation();

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

    $tag_info = $this->tag_dictionary->findTagInfo($tag);
    $tag_info->load();

    $tag_node = $this->buildTagNode($tag_info, $tag, $attrs, $self_closed_tag = true);
    $tag_node->setHasClosingTag(false);
    $this->tree_builder->pushNode($tag_node); // for cases like <%include%> we do pushNode() and popNode() here.
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
  function buildTagNode($tag_info, $tag, $attrs, $isEmpty)
  {
    $tag_node = $this->_createTagNode($tag_info, $tag);
    $tag_node->emptyClosedTag = $isEmpty;
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
      $tag_node->set($name, $value);
    }
  }  
  
  protected function _createTagNode($tag_info, $tag)
  {
    $class = $tag_info->getClass();
    $tag_node = new $class($this->parser->getCurrentLocation(), $tag, $tag_info);
    return $tag_node;
  }    

  function normalizeAttributes($attrs)
  {
    return array_change_key_case($attrs, CASE_LOWER);    
  }

  function characters($text)
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

