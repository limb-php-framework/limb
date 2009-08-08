<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/fs/src/exception/lmbFileNotFoundException.class.php');

/**
 * class lmbMacroParser.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroParser implements lmbMacroTokenizerListener
{
  protected $active_parsing_state;

  protected $tag_parsing_state;

  /**
   * @var lmbMacroTreebuilder
   */
  protected $tree_builder;

  protected $tokenizer;

  /**
   * @param lmbMacroTreeBuilder $tree_builder
   * @param lmbMacroTagDictionary $tag_dictionary
   */
  function __construct($tree_builder, $tag_dictionary)
  {
    $this->tokenizer = new lmbMacroTokenizer($this);
    $this->preprocessor = new lmbMacroPreprocessor();

    $this->tree_builder = $tree_builder;

    $this->tag_parsing_state = $this->_createTagParsingState($tag_dictionary);
  }

  function getCurrentLocation()
  {
    return $this->tokenizer->getCurrentLocation();
  }

  protected function _createTagParsingState($tag_dictionary)
  {
    return new lmbMacroTagParsingState($this, $this->tree_builder, $tag_dictionary);
  }

  /**
  * Used to parse the source template.
  * Initially invoked by the CompileTemplate function,
  * the first component argument being a root node.
  * @param string $source_file_path 
  * @param lmbMacroNode $root_node
  */
  function parse($source_file_path, $root_node)
  {
    $tags_before_parse = $this->tree_builder->getExpectedTagCount();

    $this->tree_builder->setCursor($root_node);

    $this->changeToTagParsingState();    
    
    if(!file_exists($source_file_path))
    {
      throw new lmbFileNotFoundException(
        $source_file_path,
        'Template file not found', array(
          'parent_file' => $root_node->getTemplateFile(),          
          'parent_file_line' => $root_node->getTemplateLine())
      );      
    }    

    $content = file_get_contents($source_file_path);

    $this->preprocessor->process($content);

    $this->tokenizer->parse($content, $source_file_path);

    if($tags_before_parse != $this->tree_builder->getExpectedTagCount())
    {
      $location = $this->tree_builder->getExpectedTagLocation();
      throw new lmbMacroException('Missing close tag',
                              array('tag' => $this->tree_builder->getExpectedTag(),
                                    'file' => $location->getFile(),
                                    'line' => $location->getLine()));
    }
  }

  function getActiveParsingState()
  {
    return $this->active_parsing_state;
  }
  
  function changeToTagParsingState()
  {
    $this->active_parsing_state = $this->tag_parsing_state;
  }  


  function startElement($tag, $attrs)
  {
    $this->active_parsing_state->startElement($tag, $attrs);
  }

  function endElement($tag)
  {
    $this->active_parsing_state->endElement($tag);
  }

  function emptyElement($tag, $attrs)
  {
    $this->active_parsing_state->emptyElement($tag, $attrs);
  }

  function characters($text)
  {
    $this->active_parsing_state->characters($text);
  }

  function php($text)
  {
    $this->active_parsing_state->php($text);
  }

  function unexpectedEOF($text)
  {
    $this->active_parsing_state->unexpectedEOF($text);
  }

  function invalidEntitySyntax($text)
  {
    $this->active_parsing_state->invalidEntitySyntax($text);
  }

  function invalidAttributeSyntax($text)
  {
    $this->active_parsing_state->invalidAttributeSyntax($text);
  }
}


