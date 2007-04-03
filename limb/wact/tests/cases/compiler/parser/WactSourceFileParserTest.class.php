<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactSourceFileParserTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';
require_once 'limb/wact/src/WactTemplateConfig.interface.php';
require_once 'limb/wact/src/locator/WactTemplateLocator.interface.php';

Mock::generate('WactLiteralParsingState','MockWactLiteralParsingState');
Mock::generate('WactComponentParsingState','MockWactComponentParsingState');
Mock::generate('WactNodeBuilder', 'MockWactNodeBuilder');
Mock::generate('WactTreeBuilder', 'MockWactTreeBuilder');
Mock::generate('WactTemplateConfig', 'MockWactTemplateConfig');
Mock::generate('WactTemplateLocator', 'MockWactTemplateLocator');

class WactSourceFileParserTestVersion extends WactSourceFileParser
{
  public $mock_component_parsing_state;
  public $mock_literal_parsing_state;

  protected function _createComponentParsingState()
  {
    $this->mock_component_parsing_state = new MockWactComponentParsingState();
    return $this->mock_component_parsing_state;
  }

  protected function _createLiteralParsingState()
  {
    $this->mock_literal_parsing_state = new MockWactLiteralParsingState();
    return $this->mock_literal_parsing_state;
  }
}

class WactSourceFileParserTest extends UnitTestCase
{
  protected $parser;
  protected $tree_builder;
  protected $node_builder;
  protected $config;
  protected $locator;
  protected $tag_dictionary;
  protected $component_parsing_state;
  protected $literal_parsing_state;

  function setUp()
  {
    $this->tree_builder = new MockWactTreeBuilder();
    $this->node_builder = new MockWactNodeBuilder();
    $this->config = new MockWactTemplateConfig();
    $this->locator = new MockWactTemplateLocator();

    $this->parser = new WactSourceFileParserTestVersion($this->tree_builder,
                                                        $this->node_builder,
                                                        $this->config,
                                                        $this->locator,
                                                        $this->tag_dictionary);

    $this->component_parsing_state = $this->parser->mock_component_parsing_state;
    $this->literal_parsing_state = $this->parser->mock_literal_parsing_state;
  }

  function testBuildFilterChain()
  {
    $Parser = $this->parser->buildFilterChain('TagsToLower');
    $this->component_parsing_state->expectOnce('startElement', array('test', '*'));
    $Parser->startElement('TEST', array());
  }

  function testChangeToComponentParsingState()
  {
    $this->parser->changeToComponentParsingState();
    $this->assertIsA($this->component_parsing_state, 'MockWactComponentParsingState');
  }

  function testChangeToLiteralParsingState()
  {
    $this->literal_parsing_state->expectOnce('setLiteralTag', array('test'));
    $this->parser->changeToLiteralParsingState('test');
    $this->assertTrue($this->parser->getActiveParsingState(), 'MockWactLiteralParsingState');
  }

  function testChangeStates()
  {
    $this->parser->changeToComponentParsingState();
    $this->assertIsA($this->parser->getActiveParsingState(), 'MockWactComponentParsingState');
    $this->parser->changeToLiteralParsingState('test');
    $this->assertIsA($this->parser->getActiveParsingState(), 'MockWactLiteralParsingState');
    $this->parser->changeToComponentParsingState();
    $this->assertIsA($this->parser->getActiveParsingState(), 'MockWactComponentParsingState');
  }

  function testSetDocumentLocator()
  {
    $this->component_parsing_state->expectOnce('setDocumentLocator');
    $this->literal_parsing_state->expectOnce('setDocumentLocator');
    $Locator = NULL;

    $this->parser->setDocumentLocator($Locator);
  }

  function testStartElement()
  {
    $tag = 'test';
    $attributes = array('foo' => 'bar');
    $this->component_parsing_state->expectOnce('startElement', array($tag, $attributes));
    $this->parser->startElement($tag, $attributes);
  }

  function testEndElement()
  {
    $tag = 'test';
    $this->component_parsing_state->expectOnce('endElement', array($tag));
    $this->parser->endElement($tag);
  }

  function testEmptyElement()
  {
    $tag = 'test';
    $attributes = array('foo' => 'bar');
    $this->component_parsing_state->expectOnce('emptyElement', array($tag, $attributes));
    $this->parser->emptyElement($tag, $attributes);
  }

  function testCharacters()
  {
    $data = 'test';
    $this->component_parsing_state->expectOnce('characters', array($data));
    $this->parser->characters($data);
  }

  function testCdata()
  {
    $data = 'test';
    $this->component_parsing_state->expectOnce('cdata', array($data));
    $this->parser->cdata($data);
  }

  function testProcessingInstruction()
  {
    $target = 'test';
    $instruction = 'hi';
    $this->component_parsing_state->expectOnce('processingInstruction', array($target, $instruction));
    $this->parser->processingInstruction($target, $instruction);
  }

  function testEscape()
  {
    $text = 'test';
    $this->component_parsing_state->expectOnce('escape', array($text));
    $this->parser->escape($text);
  }

  function testComment()
  {
    $text = 'test';
    $this->component_parsing_state->expectOnce('comment', array($text));
    $this->parser->comment($text);
  }

  function testDoctype()
  {
    $text = 'test';
    $this->component_parsing_state->expectOnce('doctype', array($text));
    $this->parser->doctype($text);
  }

  function testJasp()
  {
    $text = 'test';
    $this->component_parsing_state->expectOnce('jasp', array($text));
    $this->parser->jasp($text);
  }

  function testUnexpectedEOF()
  {
    $text = 'test';
    $this->component_parsing_state->expectOnce('unexpectedEOF', array($text));
    $this->parser->unexpectedEOF($text);
  }

  function testInvalidEntitySyntax()
  {
    $text = 'test';
    $this->component_parsing_state->expectOnce('invalidEntitySyntax', array($text));
    $this->parser->invalidEntitySyntax($text);
  }

  function testInvalidAttributeSyntax()
  {
    $this->component_parsing_state->expectOnce('invalidAttributeSyntax', array());
    $this->parser->invalidAttributeSyntax();
  }
}
?>