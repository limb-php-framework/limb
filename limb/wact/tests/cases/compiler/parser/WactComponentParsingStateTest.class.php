<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/tests/cases/compiler/parser/WactBaseParsingStateTestCase.class.php');

Mock :: generate('WactTreeBuilder', 'MockWactTreeBuilder');
Mock :: generate('WactTagDictionary', 'MockWactTagDictionary');

class WactComponentParsingStateTest extends WactBaseParsingStateTestCase
{
  protected $tree_builder;
  protected $tag_dictionary;
  protected $cursor_node;
  protected $parser;
  protected $state;

  function setUp()
  {
    $this->parser = new MockWactSourceFileParser();

    $this->cursor_node = new WactCompileTreeNode();
    $this->tag_dictionary = new MockWactTagDictionary();

    $this->tree_builder = new MockWactTreeBuilder();
    $this->tree_builder->setReturnValue('getCursor', $this->cursor_node);

    $this->state = new WactComponentParsingState($this->parser, $this->tree_builder, $this->tag_dictionary);
  }

  function testStartTagPlain()
  {
    $tag = 'test';
    $location = new WactSourceLocation('my_file', 10);

    $this->tree_builder->expectOnce('pushExpectedPlainTag', array($tag, $location));
    $this->tree_builder->expectOnce('addContent', array('<test foo="bar">', $location));

    $this->state->startTag($tag, $attrs = array('foo'=>'bar'), $location);
  }

  function testStartTagIsWactTag()
  {
    $tag = 'test';
    $attrs = array('foo'=>'bar');

    $info = new WactTagInfo($tag, $tag_class = 'SomeTagClass');
    $this->tag_dictionary->setReturnValue('findTagInfo', $info, array($tag, $attrs, FALSE, new ReferenceExpectation($this->cursor_node)));

    $location = new WactSourceLocation('my_file', 10);

    $this->tree_builder->expectOnce('buildTagNode', array($info, $tag, $location, $attrs));
    $this->tree_builder->expectOnce('pushExpectedWactTag', array($tag, $location));

    $this->parser->expectNever('changeToLiteralParsingState');

    $this->state->startTag($tag, $attrs, $location);
  }

  function testStartTagIsWactTagWithForbidParseTagContentProperty()
  {
    $tag = 'test';
    $attrs = array('foo'=>'bar');

    $info = new WactTagInfo($tag, $tag_class = 'SomeTagClass');
    $info->setForbidParsing(true);

    $this->tag_dictionary->setReturnValue('findTagInfo', $info, array($tag, $attrs, FALSE, new ReferenceExpectation($this->cursor_node)));

    $location = new WactSourceLocation('my_file', 10);

    $this->tree_builder->expectOnce('buildTagNode', array($info, $tag, $location, $attrs));
    $tag_node = new WactCompilerTag($location, $tag, $info);
    $this->tree_builder->setReturnValue('buildTagNode', $tag_node);
    $this->tree_builder->expectOnce('pushExpectedWactTag', array($tag, $location));
    $this->tree_builder->expectOnce('pushNode', array($tag_node));

    $this->parser->expectOnce('changeToLiteralParsingState', array($tag));

    $this->state->startTag($tag, $attrs, $location);
  }

  function testStartTagIsWactTagWithForbidParseContentResultFromPushNode()
  {
    $tag = 'test';
    $attrs = array('foo'=>'bar');

    $info = new WactTagInfo($tag, $tag_class = 'SomeTagClass');

    $this->tag_dictionary->setReturnValue('findTagInfo', $info, array($tag, $attrs, FALSE, new ReferenceExpectation($this->cursor_node)));

    $location = new WactSourceLocation('my_file', 10);

    $this->tree_builder->expectOnce('buildTagNode', array($info, $tag, $location, $attrs));
    $tag_node = new WactCompilerTag($location, $tag, $info);
    $this->tree_builder->setReturnValue('buildTagNode', $tag_node);
    $this->tree_builder->expectOnce('pushExpectedWactTag', array($tag, $location));
    $this->tree_builder->expectOnce('pushNode', array($tag_node));
    $this->tree_builder->setReturnValue('pushNode', WACT_PARSER_FORBID_PARSING);

    $this->parser->expectOnce('changeToLiteralParsingState', array($tag));

    $this->state->startTag($tag, $attrs, $location);
  }

  function testStartTagIsWactTagWithForbidEndTag()
  {
    $tag = 'test';
    $attrs = array('foo'=>'bar');

    $info = new WactTagInfo($tag, $tag_class = 'SomeTagClass');
    $info->setForbidEndTag(true);

    $this->tag_dictionary->setReturnValue('findTagInfo', $info, array($tag, $attrs, FALSE, new ReferenceExpectation($this->cursor_node)));

    $location = new WactSourceLocation('my_file', 10);

    $this->tree_builder->expectOnce('buildTagNode', array($info, $tag, $location, $attrs, FALSE, FALSE));
    $this->tree_builder->expectNever('pushExpectedWactTag');
    $this->tree_builder->expectOnce('pushNode');
    $this->tree_builder->expectOnce('popNode');

    $this->state->startTag($tag, $attrs, $location);
  }

  function testStartTagWithIllegalRunatAttributeValue()
  {
    $tag = 'test';
    $attrs = array('runat'=>'{$hello}');

    $location = new WactSourceLocation('my_file', 10);

    try
    {
      $this->state->startTag($tag, $attrs, $location);
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

  function testEndTagIsPlain()
  {
    $tag = 'test';

    $location = new WactSourceLocation('my_file', 10);

    $this->tree_builder->expectOnce('popExpectedPlainTag', array($tag, $location));
    $this->tree_builder->setReturnValue('popExpectedPlainTag', WACT_EXPECTED_PLAIN_TAG);
    $this->tree_builder->expectNever('popNode');
    $this->tree_builder->expectOnce('addWactTextNode', array('</' . $tag . '>'));

    $this->state->endTag($tag, $location);
  }

  function testEndTagIsWactTagAndRunAtServer()
  {
    $tag = 'test';
    $info = new WactTagInfo($tag, 'test');
    $info->setRunat('server');

    $this->tag_dictionary->setReturnReference('getWactTagInfo', $info);

    $location = new WactSourceLocation('my_file', 10);

    $this->tree_builder->expectOnce('popExpectedWactTag', array($tag, $location));
    $this->tree_builder->setReturnValue('popExpectedWactTag', WACT_EXPECTED_WACT_TAG);
    $this->tree_builder->expectOnce('popNode');

    $this->state->endTag($tag, $location);
  }

  function testEndTagIsPlainSinceRunAtClient()
  {
    $tag = 'test';
    $info = new WactTagInfo($tag, 'test');
    $info->setRunat('client');

    $this->tag_dictionary->setReturnReference('getWactTagInfo', $info);

    $location = new WactSourceLocation('my_file', 10);

    $this->tree_builder->expectOnce('popExpectedPlainTag', array($tag, $location));
    $this->tree_builder->expectNever('popNode');
    $this->tree_builder->expectOnce('addWactTextNode', array('</' . $tag . '>'));

    $this->state->endTag($tag, $location);
  }

  function testEndTagIsPlainSinceRunAtClientButPopExpectedPlainTagReturnMatchToWactTag()
  {
    $tag = 'test';
    $info = new WactTagInfo($tag, 'test');
    $info->setRunat('client');

    $this->tag_dictionary->setReturnReference('getWactTagInfo', $info);

    $location = new WactSourceLocation('my_file', 10);

    $this->tree_builder->expectOnce('popExpectedPlainTag', array($tag, $location));
    $this->tree_builder->setReturnValue('popExpectedPlainTag', WACT_EXPECTED_WACT_TAG);
    $this->tree_builder->expectOnce('popNode');

    $this->state->endTag($tag, $location);
  }

  function testEmptyTagIsPlain()
  {
    $tag = 'test';
    $attrs = array('foo' => 'bar');

    $this->tag_dictionary->expectOnce('findTagInfo');
    $this->tag_dictionary->setReturnValue('findTagInfo', NULL);

    $location = new WactSourceLocation('my_file', 10);

    $this->tree_builder->expectNever('pushExpectedWactTag');
    $this->tree_builder->expectNever('pushExpectedPlainTag');
    $this->tree_builder->expectNever('pushNode');
    $this->tree_builder->expectOnce('addContent', array('<test foo="bar" />', $location));

    $this->state->emptyTag($tag, $attrs, $location);
  }

  function testEmptyTagIsWactTag()
  {
    $tag = 'test';
    $attrs = array('foo'=>'bar');

    $info = new WactTagInfo($tag, 'test');
    $this->tag_dictionary->setReturnReference('findTagInfo', $info);

    $location = new WactSourceLocation('my_file', 10);

    $this->tree_builder->expectOnce('buildTagNode', array($info, $tag, $location, $attrs, TRUE, false ));
    $tag_node = new WactCompilerTag($location, $tag, $info);
    $this->tree_builder->setReturnValue('buildTagNode', $tag_node);

    $this->tree_builder->expectNever('addNode');
    $this->tree_builder->expectOnce('pushNode', array($tag_node));
    $this->tree_builder->expectOnce('popNode');
    $this->tree_builder->expectNever('pushExpectedWactTag');
    $this->tree_builder->expectNever('pushExpectedPlainTag');

    $this->state->emptyTag($tag, $attrs, $location);
  }

  function testCharacters()
  {
    $text = 'test';
    $location = new WactSourceLocation('my_file', 10);
    $this->tree_builder->expectOnce('addContent', array($text, $location));
    $this->state->characters($text, $location);
  }

  function testProcessingInstruction()
  {
    $target = 'test';
    $instruction = 'doit';
    $location = new WactSourceLocation('my_file', 10);
    $this->tree_builder->expectOnce('addProcessingInstruction', array($target, $instruction, $location));
    $this->state->instruction($target, $instruction, $location);
  }

  /*
  function testStartElementForTagWithEndTagForbiddenThrowsExceptionInStrictMode()
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
      $this->state->startTag($tag, $attrs);
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
      $this->state->endTag($tag);
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
  */
}

