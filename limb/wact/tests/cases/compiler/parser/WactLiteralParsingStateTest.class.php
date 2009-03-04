<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/tests/cases/compiler/parser/WactBaseParsingStateTestCase.class.php');

class WactLiteralParsingStateTest extends WactBaseParsingStateTestCase
{
  protected $state;
  protected $tree_builder;
  protected $parser;

  function setUp()
  {
    $this->location = new WactSourceLocation('my_file', 10);
    $this->parser = new MockWactSourceFileParser();
    $this->tree_builder = new MockTreeBuilder();
    $this->state = new WactLiteralParsingState($this->parser, $this->tree_builder);
  }

  function testGetAttributeStringRunatTrimmed()
  {
    $attrs = array('foo'=>'bar', 'runat'=>'client');
    $this->assertIdentical($this->state->getAttributeString($attrs), ' foo="bar"');
  }

  function testStartElement()
  {
    $tag = 'test';
    $attrs = array('foo' => 'bar');
    $this->state->setLiteralTag('foo');
    $this->tree_builder->expectOnce('addWactTextNode', array( '<test foo="bar">'));
    $this->state->startTag($tag, $attrs, $this->location);
  }

  function testEndElement()
  {
    $tag = 'test';
    $this->state->setLiteralTag('foo');
    $this->tree_builder->expectOnce('addWactTextNode', array( '</' . $tag . '>'));
    $this->state->endTag($tag, $this->location);
  }

  function testEndElementLiteral()
  {
    $tag = 'test';
    $this->state->setLiteralTag('test');
    $this->tree_builder->expectOnce('popNode');
    $this->parser->expectOnce('changeToComponentParsingState');
    $this->state->endTag($tag, $this->location);
  }

  function testEmptyElement()
  {
    $tag = 'test';
    $attrs = array('foo' => 'bar');
    $this->state->setLiteralTag('foo');
    $this->tree_builder->expectOnce('addWactTextNode', array( '<test foo="bar" />'));
    $this->state->emptyTag($tag, $attrs, $this->location);
  }

  function testCharacters()
  {
    $text = 'test';
    $this->tree_builder->expectOnce('addWactTextNode', array($text));
    $this->state->characters($text, $this->location);
  }

  function testInstruction()
  {
    $target = 'test';
    $instruction = 'doit';
    $pi = '<?' . $target . ' ' . $instruction . '?>';
    $this->tree_builder->expectOnce('addWactTextNode', array($pi));
    $this->state->instruction($target, $instruction, $this->location);
  }
}

