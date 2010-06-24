<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

Mock :: generate('lmbMacroTokenizerListener', 'MockMacroTokenizerListener');

class lmbMacroTokenizerTest extends UnitTestCase
{
  protected $parser;
  protected $listener;

  function setUp()
  {
    $this->listener = new MockMacroTokenizerListener();
    $this->parser = new lmbMacroTokenizer($this->listener);
  }

  function testEmpty()
  {
    $this->listener->expectNever('characters');
    $this->listener->expectNever('startElement');
    $this->listener->expectNever('endElement');
    $this->parser->parse('');
  }

  function testSimpledata()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->parser->parse('stuff');
  }

  function testPreservingWhiteSpace()
  {
    $this->listener->expectOnce('characters', array(" stuff\t\r\n "));
    $this->parser->parse(" stuff\t\r\n ");
  }

  function testEmptyElement()
  {
    $this->listener->expectOnce('startElement', array('tag', array()));
    $this->listener->expectOnce('endElement', array('tag'));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('{{tag}}{{/tag}}');
  }

  function testEmptyElementSelfClose()
  {
    $this->listener->expectOnce('emptyElement', array('tag', array()));
    $this->listener->expectNever('startElement');
    $this->listener->expectNever('endElement');
    $this->parser->parse('{{tag/}}');
  }

  function testElementWithContent()
  {
    $this->listener->expectOnce('startElement', array('tag', array()));
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('endElement', array('tag'));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('{{tag}}stuff{{/tag}}');
  }

  function testElementNestedSingleQuote()
  {
    $this->listener->expectOnce('startElement', array('tag', array('attribute' => '\'')));
    $this->listener->expectNever('characters');
    $this->listener->expectNever('endElement');
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('{{tag attribute="\'"}}');
  }

  function testElementNestedDoubleQuote()
  {
    $this->listener->expectOnce('startElement', array('tag', array('attribute' => '"')));
    $this->listener->expectNever('characters');
    $this->listener->expectNever('endElement');
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('{{tag attribute=\'"\'}}');
  }

  function testEmptyClose()
  {
    $this->listener->expectOnce('endElement', array(''));
    $this->listener->expectNever('characters');
    $this->parser->parse('{{/}}');
  }

  function testSelfClosingPHPBlock()
  {
    $this->listener->expectNever('startElement');
    $this->listener->expectNever('characters');
    $this->listener->expectNever('endElement');
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->listener->expectOnce('php', array('<?php $var = "{{tag}}{{/tag}}";?>'));
    $this->parser->parse('<?php $var = "{{tag}}{{/tag}}";?>');
  }

  function testSeveralPHPBlocks()
  {
    $this->listener->expectCallCount('characters', 2);
    $this->listener->expectArgumentsAt(0, 'characters', array('hey'));
    $this->listener->expectArgumentsAt(1, 'characters', array('foo'));
    $this->listener->expectCallCount('php', 2);
    $this->listener->expectArgumentsAt(0, 'php', array('<?php $yo = "{{foo/}}";?>'));
    $this->listener->expectArgumentsAt(1, 'php', array('<?php $var = "{{tag}}{{/tag}}";?>'));
    $this->listener->expectNever('startElement');
    $this->listener->expectNever('endElement');
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('<?php $yo = "{{foo/}}";?>hey<?php $var = "{{tag}}{{/tag}}";?>foo');
  }

  function testNonClosingPHPBlock()
  {
    $this->listener->expectNever('startElement');
    $this->listener->expectNever('characters');
    $this->listener->expectNever('endElement');
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->listener->expectOnce('php', array('<?php $var = "{{tag}}{{/tag}}";'));
    $this->parser->parse('<?php $var = "{{tag}}{{/tag}}";');
  }

  function testTagAfterPHPBlock()
  {
    $this->listener->expectOnce('startElement', array('foo', array()));
    $this->listener->expectOnce('characters', array('hey'));
    $this->listener->expectOnce('endElement', array('foo'));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->listener->expectOnce('php', array('<?php $var = "{{tag}}{{/tag}}";?>'));
    $this->parser->parse('<?php $var = "{{tag}}{{/tag}}";?>{{foo}}hey{{/foo}}');
  }

  function testTagBeforePHPBlock()
  {
    $this->listener->expectOnce('startElement', array('foo', array()));
    $this->listener->expectOnce('characters', array('hey'));
    $this->listener->expectOnce('endElement', array('foo'));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->listener->expectOnce('php', array('<?php $var = "{{tag}}{{/tag}}";?>'));
    $this->parser->parse('{{foo}}hey{{/foo}}<?php $var = "{{tag}}{{/tag}}";?>');
  }

  function testCharactersBeforePHPBlock()
  {
    $this->listener->expectNever('startElement');
    $this->listener->expectOnce('characters', array('hey'));
    $this->listener->expectNever('endElement');
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->listener->expectOnce('php', array('<?php $var = "{{tag}}{{/tag}}";?>'));
    $this->parser->parse('hey<?php $var = "{{tag}}{{/tag}}";?>');
  }

  function testMixedTagsAndPHPBlocks()
  {
    $this->listener->expectCallCount('startElement', 2);
    $this->listener->expectArgumentsAt(0, 'startElement', array('foo', array()));
    $this->listener->expectArgumentsAt(1, 'startElement', array('zoo', array()));
    $this->listener->expectCallCount('characters', 4);
    $this->listener->expectArgumentsAt(0, 'characters', array('hey'));
    $this->listener->expectArgumentsAt(1, 'characters', array('baz'));
    $this->listener->expectArgumentsAt(2, 'characters', array('wow'));
    $this->listener->expectArgumentsAt(3, 'characters', array('hm..'));
    $this->listener->expectCallCount('endElement', 2);
    $this->listener->expectArgumentsAt(0, 'endElement', array('foo'));
    $this->listener->expectArgumentsAt(1, 'endElement', array('zoo'));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->listener->expectCallCount('php', 2);
    $this->listener->expectArgumentsAt(0, 'php', array('<?php $var = "{{tag}}{{/tag}}";?>'));
    $this->listener->expectArgumentsAt(1, 'php', array('<?php echo 1;?>'));
    $this->parser->parse('{{foo}}hey{{/foo}}baz<?php $var = "{{tag}}{{/tag}}";?>{{zoo}}wow{{/zoo}}hm..<?php echo 1;?>');
  }

  function testOutputTag()
  {
    $this->listener->expectOnce('startElement', array('$value', array()));
    $this->listener->expectNever('characters');
    $this->listener->expectNever('endElement');
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('{{$value}}');
  }

  function testElementWithPreContent()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('startElement', array('br', array()));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('stuff{{br}}');
  }

  function testElementWithPostContent()
  {
    $this->listener->expectOnce('startElement', array('br', array()));
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('{{br}}stuff');
  }

  function testExpressionAfterTag()
  {
    $this->listener->expectOnce('emptyElement', array('br', array()));
    $this->listener->expectOnce('characters', array('{$str}'));
    $this->parser->parse('{{br/}}{$str}');
  }

  function testSelfClosingTagWithArgumentsAndNoSpaceBeforeClosing()
  {
    $this->listener->expectOnce('emptyElement', array('tag', array('str' => 'abcdefgh')));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('{{tag str="abcdefgh"/}}');
  }

  function testExpressionAfterTagWithArguments()
  {
    $this->listener->expectOnce('emptyElement', array('tag', array('str' => 'abcdefgh')));
    $this->listener->expectOnce('characters', array('{$str}'));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('{{tag str="abcdefgh" /}}{$str}');
  }

  function testMismatchedElements()
  {
    $this->listener->expectArgumentsAt(0, 'startElement', array('b', array()));
    $this->listener->expectArgumentsAt(1, 'startElement', array('i', array()));
    $this->listener->expectArgumentsAt(0, 'endElement', array('b'));
    $this->listener->expectArgumentsAt(1, 'endElement', array('i'));
    $this->listener->expectCallCount('startElement', 2);
    $this->listener->expectCallCount('endElement', 2);
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('{{b}}{{i}}stuff{{/b}}{{/i}}');
  }

  function testAttributes()
  {
    $this->listener->expectOnce('startElement', array('tag', array("a" => "A", "b" => "B", "c" => "C")));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('{{tag a="A" b=\'B\' c = "C"}}');
  }

  function testEmptyAttributes()
  {
    $this->listener->expectOnce('startElement', array('tag', array("a" => NULL, "b" => NULL, "c" => NULL)));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('{{tag a b c}}');
  }

  function testNastyAttributes()
  {
    $this->listener->expectOnce('startElement', array('tag', array("a" => "&{\$'?<>",
                                                                   "b" => "\r\n\t\"",
                                                                   "c" => "")));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse("{{tag a=\"&{\$'?<>\" b='\r\n\t\"' c = ''}}");
  }

  function testAttributesPadding()
  {
    $this->listener->expectOnce('startElement', array('tag', array("a" => "A", "b" => "B", "c" => "C")));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse("{{tag\ta=\"A\"\rb='B'\nc = \"C\"\n}}");
  }
}

