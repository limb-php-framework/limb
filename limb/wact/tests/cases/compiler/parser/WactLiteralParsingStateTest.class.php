<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactLiteralParsingStateTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/wact/tests/cases/compiler/parser/WactBaseParsingStateTestCase.class.php');

class WactLiteralParsingStateTest extends WactBaseParsingStateTestCase
{
  protected $state;
  protected $tree_builder;
  protected $parser;

  function setUp()
  {
    $this->parser = new MockWactSourceFileParser();
    $this->tree_builder = new MockTreeBuilder();
    $this->state = new WactLiteralParsingState($this->parser, $this->tree_builder);
    $this->state->setDocumentLocator(new MockWactHTMLParser());
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
    $this->state->startElement($tag, $attrs);
  }

  function testEndElement()
  {
    $tag = 'test';
    $this->state->setLiteralTag('foo');
    $this->tree_builder->expectOnce('addWactTextNode', array( '</' . $tag . '>'));
    $this->state->endElement($tag);
  }

  function testEndElementLiteral()
  {
    $tag = 'test';
    $this->state->setLiteralTag('test');
    $this->tree_builder->expectOnce('popNode', array(TRUE));
    $this->parser->expectOnce('changeToComponentParsingState');
    $this->state->endElement($tag);
  }

  function testEmptyElement()
  {
    $tag = 'test';
    $attrs = array('foo' => 'bar');
    $this->state->setLiteralTag('foo');
    $this->tree_builder->expectOnce('addWactTextNode', array( '<test foo="bar" />'));
    $this->state->emptyElement($tag, $attrs);
  }

  function testCharacters()
  {
    $text = 'test';
    $this->tree_builder->expectOnce('addWactTextNode', array($text));
    $this->state->characters($text);
  }

  function testCdata()
  {
    $text = 'test';
    $this->tree_builder->expectOnce('addWactTextNode', array('<![CDATA[' . $text . ']]>'));
    $this->state->cdata($text);
  }

  function testProcessingInstruction()
  {
    $target = 'test';
    $instruction = 'doit';
    $pi = '<?' . $target . ' ' . $instruction . '?>';
    $this->tree_builder->expectOnce('addWactTextNode', array($pi));
    $this->state->processingInstruction($target, $instruction);
  }

  function testJasp()
  {
    $text = 'test';
    $this->tree_builder->expectOnce('addWactTextNode', array('<%' . $text . '%>'));
    $this->state->jasp($text);
  }

  function testComment()
  {
    $text = 'test';
    $this->tree_builder->expectOnce('addWactTextNode', array('<!--' . $text . '-->'));
    $this->state->comment($text);
  }

  function testEscape()
  {
    $text = 'test';
    $this->tree_builder->expectOnce('addWactTextNode', array('<!' . $text . '>'));
    $this->state->escape($text);
  }

  function testDoctype()
  {
    $text = 'test';
    $this->tree_builder->expectOnce('addWactTextNode', array('<!' . $text . '>'));
    $this->state->doctype($text);
  }

  function testUnexpectedEOF()
  {
    $text = 'test';
    $this->tree_builder->expectOnce('addWactTextNode', array($text));
    $this->state->unexpectedEOF($text);
  }

  function testInvalidEntitySyntax()
  {
    $text = 'test';
    $this->tree_builder->expectOnce('addWactTextNode', array($text));
    $this->state->invalidEntitySyntax($text);
  }
}
?>