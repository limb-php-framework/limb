<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactSourceFileParser.
 *
 * @package wact
 * @version $Id: WactSourceFileParser.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactSourceFileParser implements WactHTMLParserListener
{
  /**
  * @var WactComponentParsingState
  */
  protected $component_parsing_state;

  /**
  * @var WactLiteralParsingState
  */
  protected $literal_parsing_state;

  protected $active_parsing_state;

  /**
   * @var WactConfig
   */
  protected $config;

  /**
   * @var WactTreeBuilder
   */
  protected $tree_builder;

  /**
   * @var WactTemplateLocator
   */
  protected $template_locator;

  function __construct($tree_builder, $template_locator, $tag_dictionary)
  {
    $this->tree_builder = $tree_builder;

    $this->template_locator = $template_locator;

    $this->component_parsing_state = $this->_createComponentParsingState($tag_dictionary);

    $this->literal_parsing_state = $this->_createLiteralParsingState();

    $this->changeToComponentParsingState();
  }

  // for testing purposes
  protected function _createComponentParsingState($tag_dictionary)
  {
    return new WactComponentParsingState($this, $this->tree_builder, $tag_dictionary);
  }

  // for testing purposes
  protected function _createLiteralParsingState()
  {
    return new WactLiteralParsingState($this, $this->tree_builder);
  }

  /**
  * Used to parse the source template.
  * Initially invoked by the CompileTemplate function,
  * the first component argument being a WactCompileTreeRootNode.
  */
  function parse($file_name, $compile_tree_root_node)
  {
    $source_file_path = $this->template_locator->locateSourceTemplate($file_name);

    if(empty($source_file_path))
        throw new WactException('Template source file not found', array('file_name' => $file_name));

    $tag_count_before_parse = $this->tree_builder->getExpectedTagCount();

    $this->tree_builder->setCursor($compile_tree_root_node);

    $this->changeToComponentParsingState();

    $parser = new WactHTMLParser($this);

    $template = $this->template_locator->readTemplateFile($source_file_path);

    $parser->parse($template, $source_file_path);

    if($tag_count_before_parse != $this->tree_builder->getExpectedTagCount())
    {
      $location = $this->tree_builder->getExpectedTagLocation();
      throw new WactException('Missing close tag',
                              array('tag' => $this->tree_builder->getExpectedTag(),
                                    'file' => $location->getFile(),
                                    'line' => $location->getLine()));
    }
  }

  function changeToComponentParsingState()
  {
    $this->active_parsing_state = $this->component_parsing_state;
  }

  function getActiveParsingState()
  {
    return $this->active_parsing_state;
  }

  function changeToLiteralParsingState($tag)
  {
    $this->active_parsing_state = $this->literal_parsing_state;
    $this->active_parsing_state->setLiteralTag($tag);
  }

  function startTag($tag, $attrs, $location)
  {
    $this->active_parsing_state->startTag($tag, $attrs, $location);
  }

  function endTag($tag, $location)
  {
    $this->active_parsing_state->endTag($tag, $location);
  }

  function emptyTag($tag, $attrs, $location)
  {
    $this->active_parsing_state->emptyTag($tag, $attrs, $location);
  }

  function characters($text, $location)
  {
    $this->active_parsing_state->characters($text, $location);
  }

  function instruction($target, $instruction, $location)
  {
    $this->active_parsing_state->instruction($target, $instruction, $location);
  }
}


