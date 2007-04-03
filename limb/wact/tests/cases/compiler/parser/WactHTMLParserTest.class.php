<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactHTMLParserTest.class.php 5071 2007-02-16 09:09:35Z serega $
 * @package    wact
 */

require_once('limb/wact/src/compiler/templatecompiler.inc.php');

Mock::generate('WactParserListener', 'MockWactParserListener');

if(!class_exists('WactHTMLParserTest'))
{

class WactHTMLParserTest extends UnitTestCase
{
  protected $parser;
  protected $listener;

  function setUp()
  {
    $this->listener = new MockWactParserListener();
    $this->parser = new WactHTMLParser($this->listener);
  }

  function testEmpty()
  {
    $this->listener->expectNever('jasp');
    $this->listener->expectNever('processingInstruction');
    $this->listener->expectNever('escape');
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
    $this->parser->parse('<tag></tag>');
  }

  function testEmptyElementSelfClose()
  {
    $this->listener->expectOnce('emptyElement', array('br', array()));
    $this->listener->expectNever('startElement');
    $this->listener->expectNever('endElement');
    $this->parser->parse('<br/>');
  }

  function testElementWithContent()
  {
    $this->listener->expectOnce('startElement', array('tag', array()));
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('endElement', array('tag'));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('<tag>stuff</tag>');
  }

  function testEmptyComment()
  {
    $this->listener->expectOnce('comment', array(''));
    $this->listener->expectNever('characters');
    $this->parser->parse('<!---->');
  }

  function testElementNestedSingleQuote()
  {
    $this->listener->expectOnce('startElement', array('tag', array('attribute' => '\'')));
    $this->listener->expectNever('characters');
    $this->listener->expectNever('endElement');
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('<tag attribute="\'">');
  }

  function testElementNestedDoubleQuote()
  {
    $this->listener->expectOnce('startElement', array('tag', array('attribute' => '"')));
    $this->listener->expectNever('characters');
    $this->listener->expectNever('endElement');
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('<tag attribute=\'"\'>');
  }

  function testEmptyDocType()
  {
    $this->listener->expectOnce('doctype', array('DOCTYPE'));
    $this->listener->expectNever('characters');
    $this->listener->expectNever('escape');
    $this->parser->parse('<!DOCTYPE>');
  }

  function testEmptyClose()
  {
    $this->listener->expectOnce('endElement', array(''));
    $this->listener->expectNever('characters');
    $this->parser->parse('</>');
  }

  function testEmptyJasp()
  {
    $this->listener->expectOnce('jasp', array(''));
    $this->listener->expectNever('characters');
    $this->parser->parse('<%%>');
  }

  function testTargetOnlyProcessingInstruction()
  {
    $this->listener->expectOnce('processingInstruction', array('php', ''));
    $this->listener->expectNever('characters');
    $this->parser->parse('<?php ?>');
  }

  function testElementWithPreContent()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('startElement', array('br', array()));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('stuff<br>');
  }

  function testElementWithPostContent()
  {
    $this->listener->expectOnce('startElement', array('br', array()));
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('<br>stuff');
  }

  function testExpressionAfterTag()
  {
    $this->listener->expectOnce('emptyElement', array('br', array()));
    $this->listener->expectOnce('characters', array('{$str|clip:5}'));
    $this->parser->parse('<br/>{$str|clip:5}');
  }

  function testExpressionAfterTagWithArguments()
  {
    $this->listener->expectOnce('emptyElement', array('core:set', array('str' => 'abcdefgh')));
    $this->listener->expectOnce('characters', array('{$str|clip:5}'));
    $this->parser->parse('<core:set str="abcdefgh" />{$str|clip:5}');
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
    $this->parser->parse('<b><i>stuff</b></i>');
  }

  function testAttributes()
  {
    $this->listener->expectOnce('startElement', array('tag', array("a" => "A", "b" => "B", "c" => "C")));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('<tag a="A" b=\'B\' c = "C">');
  }

  function testEmptyAttributes()
  {
    $this->listener->expectOnce('startElement', array('tag', array("a" => NULL, "b" => NULL, "c" => NULL)));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse('<tag a b c>');
  }

  function testNastyAttributes()
  {
    $this->listener->expectOnce('startElement', array('tag', array("a" => "&%$'?<>",
                                                                   "b" => "\r\n\t\"",
                                                                   "c" => "")));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse("<tag a=\"&%$'?<>\" b='\r\n\t\"' c = ''>");
  }

  function testAttributesPadding()
  {
    $this->listener->expectOnce('startElement', array('tag', array("a" => "A", "b" => "B", "c" => "C")));
    $this->listener->expectNever('invalidAttributeSyntax');
    $this->parser->parse("<tag\ta=\"A\"\rb='B'\nc = \"C\"\n>");
  }

  function testAllProcessingInstruction()
  {
    $this->listener->expectOnce('processingInstruction', array('php', 'print "Hello"; '));
    $this->listener->expectNever('characters');
    $this->listener->expectNever('startElement');
    $this->listener->expectNever('endElement');
    $this->parser->parse('<?php print "Hello"; ?>');
  }

  function testNestedProcessingInstruction()
  {
    $this->listener->expectOnce('processingInstruction', array('php', 'print "Hello"; '));
    $this->listener->expectArgumentsAt(0, 'characters', array('a'));
    $this->listener->expectArgumentsAt(1, 'characters', array('b'));
    $this->listener->expectCallCount('characters', 2);
    $this->listener->expectNever('startElement');
    $this->listener->expectNever('endElement');
    $this->parser->parse('a<?php print "Hello"; ?>b');
  }

  function testEscapeDocTypeHandler()
  {
    $this->listener->expectOnce('doctype', array('doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN"'));
    $this->parser->parse('<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">');
  }

  function testNestedDoctypeHandler()
  {
    $this->listener->expectOnce('doctype', array('doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN"'));
    $this->listener->expectArgumentsAt(0, 'characters', array('a'));
    $this->listener->expectArgumentsAt(1, 'characters', array('b'));
    $this->listener->expectCallCount('characters', 2);
    $this->parser->parse('a<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">b');
  }

  function testEscapeCData()
  {
    $this->listener->expectOnce('cdata', array('string = \'A CDATA block\';'));
    $this->parser->parse('<![CDATA[string = \'A CDATA block\';]]>');
  }

  function testSimpleComment()
  {
    $this->listener->expectOnce('comment', array(' A comment '));
    $this->parser->parse('<!-- A comment -->');
  }

  function testNastyComment()
  {
    $this->listener->expectOnce('comment', array(' <tag></tag><?php ?><' . '% %> '));
    $this->parser->parse('<tag><!-- <tag></tag><?php ?><' . '% %> --></tag>');
  }

  function testSimpleJasp()
  {
    $this->listener->expectOnce('jasp', array(' document.write("Hello World");'));
    $this->listener->expectNever('processingInstruction');
    $this->listener->expectNever('comment');
    $this->listener->expectNever('escape');
    $this->listener->expectNever('characters');
    $this->listener->expectNever('startElement');
    $this->listener->expectNever('endElement');
    $this->parser->parse('<' . '% document.write("Hello World");%>');
  }

  function testNastyJasp()
  {
    $this->listener->expectOnce('jasp', array(' <tag a="A"><?php ?></tag><!-- comment --> '));
    $this->listener->expectNever('processingInstruction');
    $this->listener->expectNever('comment');
    $this->listener->expectNever('escape');
    $this->listener->expectNever('characters');
    $this->listener->expectNever('startElement');
    $this->listener->expectNever('endElement');
    $this->parser->parse('<' . '% <tag a="A"><?php ?></tag><!-- comment --> %>');
  }

  function testJaspInTag()
  {
    $this->listener->expectOnce( 'jasp', array(' document.write("Hello World");'));
    $this->listener->expectNever('processingInstruction');
    $this->listener->expectNever('escape');
    $this->listener->expectNever('characters');
    $this->listener->expectOnce('startElement');
    $this->listener->expectOnce('endElement');
    $this->parser->parse('<tag><' . '% document.write("Hello World");%></tag>');
  }

  /*
//  removed due to bug #1000806
//  see http://www.w3.org/TR/REC-html40/appendix/notes.html#notes-specifying-data
  function testScriptElement() {
      $this->listener->expectOnce('startElement', array('script', array('language'=>'Javascript')));
      $this->listener->expectOnce('endElement', array('script'));
      $this->listener->expectOnce('characters', array("document.write('<B>Test<\/B>');"));
      $this->listener->expectNever('invalidAttributeSyntax');
      $this->parser->parse('<script language="Javascript">document.write(\'<B>Test<\/B>\');</script>');
  }
  */

  function testScriptElementEmbedComment() {
      $this->listener->expectOnce('startElement', array('script', array('language'=>'Javascript')));
      $this->listener->expectOnce('endElement', array('script'));
      $this->listener->expectOnce('comment', array(" document.write('<B>Test<\/B>'); "));
      $this->listener->expectNever('invalidAttributeSyntax');
      $this->parser->parse('<script language="Javascript"><!-- document.write(\'<B>Test<\/B>\'); --></script>');
  }
}

}
?>