<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactSourceFileParser.class.php 5513 2007-04-03 07:37:51Z pachanga $
 * @package    wact
 */

class WactSourceFileParser implements WactParserListener
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
  protected $TreeBuilder;

  /**
   * @var WactTemplateLocator
   */
  protected $template_locator;

  function __construct($tree_builder, $node_builder, $config, $template_locator, $tag_dictionary)
  {
    $this->TreeBuilder = $tree_builder;

    $this->config = $config;
    $this->template_locator = $template_locator;

    $this->component_parsing_state = $this->_createComponentParsingState($node_builder, $tag_dictionary);

    $this->literal_parsing_state = $this->_createLiteralParsingState();

    $this->changeToComponentParsingState();
  }

  // for testing purposes
  protected function _createComponentParsingState($node_builder, $tag_dictionary)
  {
    return new WactComponentParsingState($this, $this->TreeBuilder, $node_builder, $tag_dictionary);
  }

  // for testing purposes
  protected function _createLiteralParsingState()
  {
    return new WactLiteralParsingState($this, $this->TreeBuilder);
  }

  function buildFilterChain($sax_filters = '')
  {
    // Build a filter chain
    if (empty($sax_filters))
      return $this;

    $chain = $this;

    foreach(explode(':', $sax_filters) as $sax_filter)
    {
      $sax_filter_file = 'Wact' . $sax_filter . 'SaxFilter.class.php';
      $sax_filter_class = 'Wact' . $sax_filter . 'SaxFilter';
      require_once('limb/wact/src/compiler/saxfilters/'.$sax_filter_file);

      $chain = new $sax_filter_class($chain);
    }

    return $chain;
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

    $tagCountBeforeParse = $this->TreeBuilder->getExpectedTagCount();

    $this->TreeBuilder->setCursor($compile_tree_root_node);

    $this->changeToComponentParsingState();

    $parser = new WactHTMLParser($this->buildFilterChain($this->config->getSaxFilters()));

    $this->setDocumentLocator($parser);

    $template = $this->template_locator->readTemplateFile($source_file_path);

    $parser->parse($template, $source_file_path);

    if($tagCountBeforeParse != $this->TreeBuilder->getExpectedTagCount())
    {
      $location = $this->TreeBuilder->getExpectedTagLocation();
      throw new WactException('Missing close tag',
                              array('tag' => $this->TreeBuilder->getExpectedTag(),
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

  function setDocumentLocator($template_locator)
  {
    $this->literal_parsing_state->setDocumentLocator($template_locator);
    $this->component_parsing_state->setDocumentLocator($template_locator);
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

  function cdata($text)
  {
    $this->active_parsing_state->cdata($text);
  }

  function processingInstruction($target, $instruction)
  {
    $this->active_parsing_state->processingInstruction($target, $instruction);
  }

  function escape($text)
  {
    $this->active_parsing_state->escape($text);
  }

  function comment($text)
  {
    $this->active_parsing_state->comment($text);
  }

  function doctype($text)
  {
    $this->active_parsing_state->doctype($text);
  }

  function jasp($text)
  {
    $this->active_parsing_state->jasp($text);
  }

  function unexpectedEOF($text)
  {
    $this->active_parsing_state->unexpectedEOF($text);
  }

  function invalidEntitySyntax($text)
  {
    $this->active_parsing_state->invalidEntitySyntax($text);
  }

  function invalidAttributeSyntax()
  {
    $this->active_parsing_state->invalidAttributeSyntax();
  }
}

?>
