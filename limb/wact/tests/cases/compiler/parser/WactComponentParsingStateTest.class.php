<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactComponentParsingStateTest.class.php 5553 2007-04-06 09:05:17Z serega $
 * @package    wact
 */

require_once('limb/wact/tests/cases/compiler/parser/WactBaseParsingStateTestCase.class.php');

class WactComponentParsingStateTest extends WactBaseParsingStateTestCase
{
  protected $component;
  protected $node_builder;
  protected $tree_builder;
  protected $tag_dictionary;
  protected $WactHTMLParser;
  protected $parser;
  protected $locator;

  function setUp()
  {
    $this->component = new MockWactRuntimeComponentTag();

    $this->parser = new MockWactSourceFileParser();

    $this->node_builder = new MockNodeBuilder();
    $this->node_builder->setReturnReference('buildTagNode', $this->component);

    $this->tree_builder = new MockTreeBuilder();
    $this->tree_builder->component = $this->component;
    $this->tree_builder->setReturnReference('getCursor', $this->component);

    $this->tag_dictionary = new MockWactTagDictionary();
    $this->state = new WactComponentParsingState($this->parser, $this->tree_builder, $this->node_builder, $this->tag_dictionary);

    $this->locator = new MockWactHTMLParser();
    $this->state->setDocumentLocator($this->locator);
  }

  function testGetAttributeStringRunat()
  {
    $attrs = array('foo'=>'bar', 'runat'=>'client');
    $this->assertIdentical($this->state->getAttributeString($attrs), ' foo="bar"');
  }

  function testStartElementIsPlain()
  {
    $tag = 'test';
    $attrs = array('foo'=>'bar');

    $this->locator->expectOnce('getCurrentLocation');
    $this->locator->setReturnValue('getCurrentLocation', $location = new WactSourceLocation('my_file', 10));

    $this->tag_dictionary->setReturnValue('findTagInfo', NULL);
    $this->tree_builder->expectOnce('pushExpectedTag', array($tag, PARSER_TAG_IS_PLAIN, $location));
    $this->node_builder->expectOnce('addContent', array('<test foo="bar">'));

    $this->state->startElement($tag, $attrs);
  }

  function testStartElementIsComponent()
  {
    $tag = 'test';
    $attrs = array('foo'=>'bar');

    $info = new WactTagInfo($tag, 'test');
    $this->tag_dictionary->setReturnReference('findTagInfo', $info);

    $this->locator->expectOnce('getCurrentLocation');
    $this->locator->setReturnValue('getCurrentLocation', $location = new WactSourceLocation('my_file', 10));

    $this->node_builder->expectOnce('buildTagNode', array($info, $tag, $attrs, FALSE));
    $this->tree_builder->expectOnce('pushExpectedTag', array($tag, PARSER_TAG_IS_COMPONENT, $location));

    $this->state->startElement($tag, $attrs);
  }

  function testStartElementLiteralViaTagInfo()
  {
    $tag = 'test';
    $attrs = array('foo'=>'bar');

    $info = new WactTagInfo($tag, 'test');
    $info->setForbidParsing();

    $this->tag_dictionary->setReturnReference('findTagInfo', $info);

    $this->tree_builder->expectOnce('pushNode');

    $this->parser->expectOnce('changeToLiteralParsingState', array($tag));

    $this->state->startElement($tag,$attrs);
  }

  function testStartElementLiteralViaPushNodeResult()
  {
    $tag = 'test';
    $attrs = array('foo'=>'bar');

    $info = new WactTagInfo($tag, 'test');

    $this->tag_dictionary->setReturnReference('findTagInfo', $info);

    $this->tree_builder->expectOnce('pushNode');
    $this->tree_builder->setReturnValue('pushNode', WACT_PARSER_FORBID_PARSING);

    $this->parser->expectOnce('changeToLiteralParsingState', array($tag));

    $this->state->startElement($tag,$attrs);
  }

  function testStartElementEndtagForbidden()
  {
    $tag = 'test';
    $attrs = array('foo'=>'bar');

    $info = new WactTagInfo($tag, 'test');
    $info->setForbidEndTag();
    $this->tag_dictionary->setReturnReference('findTagInfo', $info);

    $this->locator->expectOnce('getCurrentLocation');
    $this->locator->setReturnValue('getCurrentLocation', $location = new WactSourceLocation('my_file', 10));

    $this->tree_builder->expectNever('popNode');
    $this->tree_builder->expectNever('pushExpectedTag');

    $this->parser->expectNever('changeToComponentParsingState');

    try
    {
      $this->state->startElement($tag, $attrs);
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Closing tag is forbidden for this tag\. Use self closing notation/', $e->getMessage());
      $this->assertEqual($e->getParam('tag'), 'test');
      $this->assertEqual($e->getParam('file'), 'my_file');
      $this->assertEqual($e->getParam('line'), 10);
    }
  }

  function testStartElementIllegalAttributeName()
  {
    $tag = 'test';
    $attrs = array('runat'=>'{$hello}');

    $this->locator->expectOnce('getCurrentLocation');
    $this->locator->setReturnValue('getCurrentLocation', $location = new WactSourceLocation('my_file', 10));

    $this->component->tag = $tag;

    try
    {
      $this->state->startElement($tag, $attrs);
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Illegal use of variable reference/', $e->getMessage());
      $this->assertEqual($e->getParam('expression'), '{$hello}');
      $this->assertEqual($e->getParam('file'), 'my_file');
      $this->assertEqual($e->getParam('line'), 10);
    }
  }

  function testEndElementIsPlain()
  {
    $tag = 'test';

    $this->locator->expectOnce('getCurrentLocation');
    $this->locator->setReturnValue('getCurrentLocation', $location = new WactSourceLocation('my_file', 10));

    $this->tree_builder->expectOnce('popExpectedTag', array($tag, $location ));
    $this->tree_builder->setReturnValue('popExpectedTag', PARSER_TAG_IS_PLAIN);
    $this->tree_builder->expectNever('popNode');
    $this->tree_builder->expectOnce('addWactTextNode', array('</'.$tag.'>'));

    $this->state->endElement($tag);
  }

  function testEndElementIsComponent()
  {
    $tag = 'test';

    $this->locator->expectOnce('getCurrentLocation');
    $this->locator->setReturnValue('getCurrentLocation', $location = new WactSourceLocation('my_file', 10));

    $this->tree_builder->expectOnce('popExpectedTag', array($tag, $location));
    $this->tree_builder->setReturnValue('popExpectedTag', PARSER_TAG_IS_COMPONENT);
    $this->tree_builder->expectOnce('popNode', array(TRUE));

    $this->state->endElement($tag);
  }

  function testEndElementForbidden()
  {
    $tag = 'test';

    $this->locator->expectOnce('getCurrentLocation');
    $this->locator->setReturnValue('getCurrentLocation', $location = new WactSourceLocation('my_file', 10));

    $this->component->tag = 'test';

    $info = new WactTagInfo($tag, 'test');
    $info->setForbidEndTag();
    $this->tag_dictionary->setReturnReference('getWactTagInfo', $info);

    try
    {
      $this->state->endElement($tag);
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Closing tag forbidden/', $e->getMessage());
      $this->assertEqual($e->getParam('tag'), $tag);
      $this->assertEqual($e->getParam('file'), 'my_file');
      $this->assertEqual($e->getParam('line'), 10);
    }
  }

  function testEmptyElement()
  {
    $tag = 'test';
    $attrs = array('foo' => 'bar');

    $this->tag_dictionary->setReturnValue('findTagInfo', NULL);

    $this->tree_builder->expectNever('pushExpectedTag');
    $this->tree_builder->expectNever('pushNode');
    $this->node_builder->expectOnce('addContent', array('<test foo="bar" />'));

    $this->state->emptyElement($tag, $attrs);
  }

  function testEmptyElementIsComponent()
  {
    $tag = 'test';
    $attrs = array('foo'=>'bar');

    $info = new WactTagInfo($tag, 'test');
    $this->tag_dictionary->setReturnReference('findTagInfo', $info);

    $this->node_builder->expectOnce('buildTagNode', array($info, $tag, $attrs, TRUE ));
    $this->tree_builder->expectOnce('pushNode');
    $this->tree_builder->expectNever('pushExpectedTag');

    $this->state->emptyElement($tag, $attrs);
  }

  function testEmptyElementLiteral()
  {
    $tag = 'test';
    $attrs = array('foo'=>'bar');

    $info = new WactTagInfo($tag, 'test');
    $info->setForbidParsing();
    $this->tag_dictionary->setReturnReference('findTagInfo', $info);

    $this->tree_builder->expectOnce('pushNode');

    $this->parser->expectOnce('changeToLiteralParsingState',array($tag));

    $this->state->emptyElement($tag,$attrs);
  }

  function testEmptyElementEndtagForbidden()
  {
    $tag = 'test';
    $attrs = array('foo'=>'bar');

    $info = new WactTagInfo($tag, 'test');
    $info->setForbidEndTag();
    $this->tag_dictionary->setReturnReference('findTagInfo', $info);

    $this->tree_builder->expectOnce('popNode', array(FALSE));

    $this->parser->expectOnce('changeToComponentParsingState');

    $this->state->emptyElement($tag, $attrs);
  }

  function testEmptyElementIllegalAttributeName()
  {
    $tag = 'test';
    $attrs = array('runat'=>'{$hello}');

    $this->locator->expectOnce('getCurrentLocation');
    $this->locator->setReturnValue('getCurrentLocation', $location = new WactSourceLocation('my_file', 10));

    $this->component->tag = $tag;

    try
    {
       $this->state->emptyElement($tag, $attrs);
       $this->assertTrue($false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Illegal use of variable reference/', $e->getMessage());
      $this->assertEqual($e->getParam('expression'), '{$hello}');
      $this->assertEqual($e->getParam('file'), 'my_file');
      $this->assertEqual($e->getParam('line'), 10);
    }
  }

  function testCharacters()
  {
    $text = 'test';
    $this->node_builder->expectOnce('addContent', array($text));
    $this->state->characters($text);
  }

  function testProcessingInstruction()
  {
    $target = 'test';
    $instruction = 'doit';
    $this->node_builder->expectOnce('addProcessingInstruction', array($target, $instruction));
    $this->state->processingInstruction($target, $instruction);
  }

  function testJasp()
  {
    $text = 'test';
    $this->node_builder->expectOnce('addContent', array('<%' . $text . '%>'));
    $this->state->jasp($text);
  }

  function testComment()
  {
    $text = 'test';
    $this->node_builder->expectOnce('addContent', array('<!--' . $text . '-->'));
    $this->state->comment($text);
  }

  function testEscape()
  {
    $text = 'test';
    $this->node_builder->expectOnce('addContent', array('<!' . $text . '>'));
    $this->state->escape($text);
  }

  function testDoctype()
  {
    $text = 'test';
    $this->node_builder->expectOnce('addContent', array('<!' . $text . '>'));
    $this->state->doctype($text);
  }

  function testUnexpectedEOF()
  {
    $text = 'test';
    $this->node_builder->expectOnce('addContent', array($text));
    $this->state->unexpectedEOF($text);
  }

  function testInvalidEntitySyntax()
  {
    $text = 'test';
    $this->node_builder->expectOnce('addContent', array($text));
    $this->state->invalidEntitySyntax($text);
  }
}
?>