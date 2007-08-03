<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/lmbMacroTokenizerListener.interface.php');
lmb_require('limb/macro/src/lmbMacroTokenizer.class.php');

Mock::generate('lmbMacroTokenizerListener', 'MockMacroTokenizerListener');

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
    $this->parser->parse('<%tag%><%/tag%>');
  }

  function testEmptyElementSelfClose()
  {
    $this->listener->expectOnce('emptyElement', array('tag', array()));
    $this->listener->expectNever('startElement');
    $this->listener->expectNever('endElement');
    $this->parser->parse('<%tag/%>');
  }

  function testElementWithContent()
  {
    $this->listener->expectOnce('startElement', array('tag', array()));
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('endElement', array('tag'));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('<%tag%>stuff<%/tag%>');
  }

  function testElementNestedSingleQuote()
  {
    $this->listener->expectOnce('startElement', array('tag', array('attribute' => '\'')));
    $this->listener->expectNever('characters');
    $this->listener->expectNever('endElement');
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('<%tag attribute="\'"%>');
  }

  function testElementNestedDoubleQuote()
  {
    $this->listener->expectOnce('startElement', array('tag', array('attribute' => '"')));
    $this->listener->expectNever('characters');
    $this->listener->expectNever('endElement');
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('<%tag attribute=\'"\'%>');
  }


  function testEmptyClose()
  {
    $this->listener->expectOnce('endElement', array(''));
    $this->listener->expectNever('characters');
    $this->parser->parse('<%/%>');
  }

  function testElementWithPreContent()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('startElement', array('br', array()));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('stuff<%br%>');
  }

  function testElementWithPostContent()
  {
    $this->listener->expectOnce('startElement', array('br', array()));
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('<%br%>stuff');
  }

  function testExpressionAfterTag()
  {
    $this->listener->expectOnce('emptyElement', array('br', array()));
    $this->listener->expectOnce('characters', array('{$str}'));
    $this->parser->parse('<%br/%>{$str}');
  }

  function testExpressionAfterTagWithArguments()
  {
    $this->listener->expectOnce('emptyElement', array('tag', array('str' => 'abcdefgh')));
    $this->listener->expectOnce('characters', array('{$str}'));
    $this->parser->parse('<%tag str="abcdefgh" /%>{$str}');
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
    $this->parser->parse('<%b%><%i%>stuff<%/b%><%/i%>');
  }

  function testAttributes()
  {
    $this->listener->expectOnce('startElement', array('tag', array("a" => "A", "b" => "B", "c" => "C")));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('<%tag a="A" b=\'B\' c = "C"%>');
  }

  function testEmptyAttributes()
  {
    $this->listener->expectOnce('startElement', array('tag', array("a" => NULL, "b" => NULL, "c" => NULL)));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('<%tag a b c%>');
  }

  function testNastyAttributes()
  {
    $this->listener->expectOnce('startElement', array('tag', array("a" => "&%$'?<>",
                                                                   "b" => "\r\n\t\"",
                                                                   "c" => "")));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse("<%tag a=\"&%$'?<>\" b='\r\n\t\"' c = ''%>");
  }

  function testAttributesPadding()
  {
    $this->listener->expectOnce('startElement', array('tag', array("a" => "A", "b" => "B", "c" => "C")));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse("<%tag\ta=\"A\"\rb='B'\nc = \"C\"\n%>");
  }
}
?>
