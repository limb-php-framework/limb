<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';
require_once 'limb/wact/src/WactTemplateConfig.interface.php';
require_once 'limb/wact/src/locator/WactTemplateLocator.interface.php';

Mock::generate('WactLiteralParsingState','MockWactLiteralParsingState');
Mock::generate('WactComponentParsingState','MockWactComponentParsingState');
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
    $this->locator = new MockWactTemplateLocator();

    $this->parser = new WactSourceFileParserTestVersion($this->tree_builder,
                                                        $this->locator,
                                                        $this->tag_dictionary);

    $this->component_parsing_state = $this->parser->mock_component_parsing_state;
    $this->literal_parsing_state = $this->parser->mock_literal_parsing_state;
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

  function testStartTag()
  {
    $tag = 'test';
    $attributes = array('foo' => 'bar');
    $location = new WactSourceLocation('my_file', 10);
    $this->component_parsing_state->expectOnce('startTag', array($tag, $attributes, $location));
    $this->parser->startTag($tag, $attributes, $location);
  }

  function testEndTag()
  {
    $tag = 'test';
    $location = new WactSourceLocation('my_file', 10);
    $this->component_parsing_state->expectOnce('endTag', array($tag, $location));
    $this->parser->endTag($tag, $location);
  }

  function testEmptyTag()
  {
    $tag = 'test';
    $attributes = array('foo' => 'bar');
    $location = new WactSourceLocation('my_file', 10);
    $this->component_parsing_state->expectOnce('emptyTag', array($tag, $attributes, $location));
    $this->parser->emptyTag($tag, $attributes, $location);
  }

  function testCharacters()
  {
    $data = 'test';
    $location = new WactSourceLocation('my_file', 10);
    $this->component_parsing_state->expectOnce('characters', array($data, $location));
    $this->parser->characters($data, $location);
  }

  function testInstruction()
  {
    $target = 'test';
    $instruction = 'hi';
    $location = new WactSourceLocation('my_file', 10);
    $this->component_parsing_state->expectOnce('instruction', array($target, $instruction, $location));
    $this->parser->instruction($target, $instruction, $location);
  }
}

