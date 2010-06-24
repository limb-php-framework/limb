<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/compiler/templatecompiler.inc.php');

Mock::generate('WactParserListener', 'MockWactParserListener');

class WactHTMLParserListenerStub implements WactHTMLParserListener
{
  public $events = array();

  function characters($text, $location)
  {
    $this->events[] = array('characters', $text, $location);
  }

  function startTag($tag, $attributes, $location)
  {
    $this->events[] = array('startTag', $tag, $attributes, $location);
  }

  function endTag($tag, $location)
  {
    $this->events[] = array('endTag', $tag, $location);
  }

  function emptyTag($tag, $attributes, $location)
  {
    $this->events[] = array('emptyTag', $tag, $attributes, $location);
  }

  function instruction($type, $code, $location)
  {
    $this->events[] = array('instruction', $type, $code, $location);
  }
}

class WactHTMLParserTest extends UnitTestCase
{
  protected $parser;
  protected $listener;

  function setUp()
  {
    $this->listener = new WactHTMLParserListenerStub();
    $this->parser = new WactHTMLParser($this->listener);
    $this->file_name = 'my_file';
  }

  protected function _createLocation($line = 1)
  {
    return new WactSourceLocation('my_file', $line);
  }

  function testEmpty()
  {
    $this->parser->parse('', $this->file_name);
    $this->assertEqual($this->listener->events, array());
  }

  function testSimpleDataIsCharacters()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff', $this->file_name);

    $this->checkEventsCount(1);
    $this->checkCharactersEvent(0, 'stuff', $location);
  }

  function testPreservingWhiteSpace()
  {
    $this->parser->parse(" stuff\t\r\n ", $this->file_name);
    $this->checkEventsCount(1);
    $this->checkCharactersEvent(0, " stuff\t\r\n ");
  }

  function testTagWithNoContentNoAttributes()
  {
    $location = $this->_createLocation();

    $this->parser->parse('<tag></tag>', $this->file_name);

    $this->checkEventsCount(2);
    $this->checkStartTagEvent(0, "tag", array(), $location);
    $this->checkEndTagEvent(1, "tag", $location);
  }

  function testEmptyTag()
  {
    $location = $this->_createLocation();

    $this->parser->parse('<br/>', $this->file_name);

    $this->checkEventsCount(1);
    $this->checkEmptyTagEvent(0, "br", array(), $location);
  }

  function testTagWithContent()
  {
    $this->parser->parse('<tag>stuff</tag>', $this->file_name);

    $this->checkEventsCount(3);
    $this->checkStartTagEvent(0, "tag", array());
    $this->checkCharactersEvent(1, "stuff");
    $this->checkEndTagEvent(2, "tag");
  }

  function testEmptyComment()
  {
    $this->parser->parse('<!---->', $this->file_name);

    $this->checkEventsCount(1);
    $this->checkCharactersEvent(0, "<!---->");
  }

  function testTruncatedComment()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<!--', $this->file_name);
    $this->checkEventsCount(2);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<!--', $location);
  }

  function testTruncatedCommentNoClose()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<!-- blah', $this->file_name);
    $this->checkEventsCount(2);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<!-- blah', $location);
  }

  function testMalformedComment()
  {
    $location = $this->_createLocation();
    $this->parser->parse('<!--x->', $this->file_name);
    $this->checkEventsCount(1);
    $this->checkCharactersEvent(0, '<!--x->', $location);
  }

  function testAttributes()
  {
    $this->parser->parse('<tag a="A" b=\'B\' c = "C">', $this->file_name);
    $this->checkEventsCount(1);
    $this->checkStartTagEvent(0, "tag", array("a" => "A", "b" => "B", "c" => "C"));
  }

  function testEmptyAttributes()
  {
    $this->parser->parse('<tag a b c>', $this->file_name);
    $this->checkEventsCount(1);
    $this->checkStartTagEvent(0, "tag", array("a" => NULL, "b" => NULL, "c" => NULL));
  }

  function testNastyAttributes()
  {
    $this->parser->parse("<tag a=\"&%$'?<>\" b='\r\n\t\"' c = ''>", $this->file_name);
    $this->checkEventsCount(1);
    $this->checkStartTagEvent(0, "tag", array("a" => "&%$'?<>",
                                              "b" => "\r\n\t\"",
                                              "c" => ""));
  }

  function testAttributesPadding()
  {
    $this->parser->parse("<tag\ta=\"A\"\rb='B'\nc = \"C\"\n>", $this->file_name);
    $this->checkEventsCount(1);
    $this->checkStartTagEvent(0, "tag", array("a" => "A", "b" => "B", "c" => "C"));
  }

  function testTagWithDoubleQuotedAttribute()
  {
    $this->parser->parse('<tag attribute="\'">', $this->file_name);

    $this->checkEventsCount(1);
    $this->checkStartTagEvent(0, "tag", array('attribute' => '\''));
  }

  function testTagWithSingleQuotedAttribute()
  {
    $this->parser->parse('<tag attribute=\'"\'>', $this->file_name);
    $this->checkEventsCount(1);
    $this->checkStartTagEvent(0, "tag", array('attribute' => '"'));
  }

  function testTagNestedSingleQuoteAttributeThrowsException()
  {
    try
    {
      $this->parser->parse('<tag attribute=\'\'\'>', $this->file_name);
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Invalid tag attribute syntax/', $e->getMessage());
      $this->assertEqual($e->getParam('file'), $this->file_name);
      $this->assertEqual($e->getParam('line'), 1);
    }
  }

  function testTagNestedDoubleQuoteAttributeThrowsException()
  {
    try
    {
      $this->parser->parse('<tag attribute=""">', $this->file_name);
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Invalid tag attribute syntax/', $e->getMessage());
      $this->assertEqual($e->getParam('file'), $this->file_name);
      $this->assertEqual($e->getParam('line'), 1);
    }
  }

  function testTagNoSpaceAfterAttributeThrowsException()
  {
    try
    {
      $this->parser->parse('<tag attribute="test"extra>', $this->file_name);
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Invalid tag attribute syntax/', $e->getMessage());
      $this->assertEqual($e->getParam('file'), $this->file_name);
      $this->assertEqual($e->getParam('line'), 1);
    }
  }

  function testOpenTagMalformedCloseThrowsException()
  {
    try
    {
      $this->parser->parse('stuff<tag attribute=\'value\'/morestuff', $this->file_name);
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Invalid tag syntax/', $e->getMessage());
      $this->assertEqual($e->getParam('file'), $this->file_name);
      $this->assertEqual($e->getParam('line'), 1);
    }
  }

  function testOpenTagMalformedCloseThrowsException2()
  {
    try
    {
      $this->parser->parse('stuff<tag attribute=\'value\'/morestuff>', $this->file_name);
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Invalid tag syntax/', $e->getMessage());
      $this->assertEqual($e->getParam('file'), $this->file_name);
      $this->assertEqual($e->getParam('line'), 1);
    }
  }

  function testElementWithPreContent()
  {
    $this->parser->parse('stuff<br>', $this->file_name);
    $this->checkEventsCount(2);
    $this->checkCharactersEvent(0, "stuff");
    $this->checkStartTagEvent(1, "br", array());
  }

  function testElementWithPostContent()
  {
    $this->parser->parse('<br>stuff', $this->file_name);
    $this->checkEventsCount(2);
    $this->checkStartTagEvent(0, "br", array());
    $this->checkCharactersEvent(1, "stuff");
  }

  function testExpressionAfterTag()
  {
    $this->parser->parse('<br/>{$str|clip:5}', $this->file_name);
    $this->checkEventsCount(2);
    $this->checkEmptyTagEvent(0, "br", array());
    $this->checkCharactersEvent(1, '{$str|clip:5}');
  }

  function testExpressionAfterTagWithArguments()
  {
    $this->parser->parse('<core:set str="abcdefgh" />{$str|clip:5}', $this->file_name);
    $this->checkEventsCount(2);
    $this->checkEmptyTagEvent(0, "core:set", array('str' => 'abcdefgh'));
    $this->checkCharactersEvent(1, '{$str|clip:5}');
  }

  function testMismatchedTags()
  {
    $this->parser->parse('<b><i>stuff</b></i>', $this->file_name);
    $this->checkEventsCount(5);
    $this->checkStartTagEvent(0, "b", array());
    $this->checkStartTagEvent(1, "i", array());
    $this->checkCharactersEvent(2, "stuff");
    $this->checkEndTagEvent(3, "b", array());
    $this->checkEndTagEvent(4, "i", array());
  }

  function testScriptTagWithTagsInsideGeneratedCode()
  {
    $this->parser->parse('<script language="Javascript">document.write(\'<B>Test</B>\');</script>');
    $this->checkEventsCount(7);
    $this->checkStartTagEvent(0, "script", array('language' => "Javascript"));
    $this->checkCharactersEvent(1, 'document.write(\'');
    $this->checkStartTagEvent(2, "B", array());
    $this->checkCharactersEvent(3, 'Test');
    $this->checkEndTagEvent(4, "B");
    $this->checkCharactersEvent(5, '\');');
    $this->checkEndTagEvent(6, "script");
  }

  function testIgnoreTagsInsideCommentsBlock()
  {
    $this->parser->parse('<script language="Javascript"><!-- document.write(\'<B>Test</B>\'); --></script>');
    $this->checkEventsCount(3);
    $this->checkStartTagEvent(0, "script", array('language' => "Javascript"));
    $this->checkCharactersEvent(1, '<!-- document.write(\'<B>Test</B>\'); -->');
    $this->checkEndTagEvent(2, "script");
  }

  function testTruncatedOpenTagChar()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<a', $this->file_name);
    $this->checkEventsCount(2);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<a', $location);
  }

  function testTruncatedOpenTag()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<tag', $this->file_name);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<tag', $location);
  }

  function testTruncatedOpenTagWithSpace()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<tag ', $this->file_name);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<tag ', $location);
  }

  function testTruncatedOpenTagMinimizedAttribute()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<tag attribute', $this->file_name);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<tag attribute', $location);
  }

  function testTruncatedOpenTagMinimizedAttributeSpace()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<tag attribute ', $this->file_name);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<tag attribute ', $location);
  }

  function testTruncatedOpenTagAttribute()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<tag attribute=', $this->file_name);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<tag attribute=', $location);
  }

  function testTruncatedOpenTagAttributeAndSpace()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<tag attribute= ', $this->file_name);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<tag attribute= ', $location);
  }

  function testTruncatedOpenTagAttributeNoQuote()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<tag attribute=value', $this->file_name);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<tag attribute=value', $location);
  }

  function testTruncatedOpenElementAttributeDoubleQuote()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<tag attribute="', $this->file_name);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<tag attribute="', $location);
  }

  function testTruncatedOpenTagAttributeDoubleQuoteNoClose()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<tag attribute="value', $this->file_name);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<tag attribute="value', $location);
  }

  function testTruncatedOpenTagAttributeDoubleQuoteValue()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<tag attribute="value"', $this->file_name);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<tag attribute="value"', $location);
  }

  function testTruncatedOpenTagAttributeSingleQuote()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<tag attribute=\'', $this->file_name);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<tag attribute=\'', $location);
  }

  function testTruncatedOpenTagAttributeSingleQuoteNoClose()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<tag attribute=\'value', $this->file_name);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<tag attribute=\'value', $location);
  }

  function testTruncatedOpenTagAttributeSingleQuoteValue()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<tag attribute=\'value\'', $this->file_name);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<tag attribute=\'value\'', $location);
  }

  function testTruncatedOpenTagClose()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<tag attribute=\'value\'/', $this->file_name);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<tag attribute=\'value\'/', $location);
  }

  function testProcessEmptyDoctypeAsCharacters()
  {
    $location = $this->_createLocation();
    $this->parser->parse('<!DOCTYPE>', $this->file_name);
    $this->checkEventsCount(1);
    $this->checkCharactersEvent(0, '<!DOCTYPE>', $location);
  }

  function testProcessDoctypeAsCharacters()
  {
    $this->parser->parse('<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">', $this->file_name);
    $this->checkEventsCount(1);
    $this->checkCharactersEvent(0, '<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">');
  }

  function testNestedDoctypeHandler()
  {
    $this->parser->parse('a<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">b');
    $this->checkEventsCount(2);
    $this->checkCharactersEvent(0, 'a');
    $this->checkCharactersEvent(1, '<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">b');
  }

  function testTruncatedDocType()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<!doctype', $this->file_name);
    $this->checkEventsCount(2);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<!doctype', $location);
  }

  function testTruncatedDocTypetNoClose()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<!doctype blah', $this->file_name);
    $this->checkEventsCount(2);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<!doctype blah', $location);
  }

  function testProcessCDataAsCharacters()
  {
    $location = $this->_createLocation();
    $this->parser->parse('<![CDATA[string = \'A CDATA block\';]]>', $this->file_name);
    $this->checkEventsCount(1);
    $this->checkCharactersEvent(0, '<![CDATA[string = \'A CDATA block\';]]>', $location);
  }

  function testCommentWithContent()
  {
    $location = $this->_createLocation();
    $this->parser->parse('<!-- A comment -->', $this->file_name);
    $this->checkEventsCount(1);
    $this->checkCharactersEvent(0, '<!-- A comment -->', $location);
  }

  function testCommentIgnoresAnyTagsOrInstructions()
  {
    $this->parser->parse('<tag><!-- <tag></tag><?php ?><' . '% %> --></tag>');
    $this->checkEventsCount(3);
    $this->checkStartTagEvent(0, 'tag', array());
    $this->checkCharactersEvent(1, '<!-- <tag></tag><?php ?><' . '% %> -->');
    $this->checkEndTagEvent(2, 'tag');
  }

  function testJSComparisonSymbolIsNotATag()
  {
    $location = $this->_createLocation();
    $this->parser->parse('<script> for(var i=0; i < 10; i++){ document.write(">");} </script>', $this->file_name);
    $this->checkEventsCount(4);
    $this->checkStartTagEvent(0, 'script', array(), $location);
    $this->checkCharactersEvent(1, ' for(var i=0; i ', $location);
    $this->checkCharactersEvent(2, '< 10; i++){ document.write(">");} ', $location);
    $this->checkEndTagEvent(3, 'script', $location);
  }

  function testJSComparisonSymbolIsNotATag2()
  {
    $location = $this->_createLocation();
    $this->parser->parse('<script> for(var i=0; i <= 10; i++){ document.write(">");} </script>', $this->file_name);
    $this->checkEventsCount(4);
    $this->checkStartTagEvent(0, 'script', array(), $location);
    $this->checkCharactersEvent(1, ' for(var i=0; i ', $location);
    $this->checkCharactersEvent(2, '<= 10; i++){ document.write(">");} ', $location);
    $this->checkEndTagEvent(3, 'script', $location);
  }

  function testEmptyJasp()
  {
    $location = $this->_createLocation();
    $this->parser->parse('<%%>', $this->file_name);
    $this->checkEventsCount(1);
    $this->checkCharactersEvent(0, '<%%>', $location);
  }

  function testMalformedJasp()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<%>', $this->file_name);
    $this->checkEventsCount(2);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<%>', $location);
  }

  function testSimpleJasp()
  {
    $this->parser->parse('<' . '% document.write("Hello World");%>');
    $this->checkEventsCount(1);
    $this->checkCharactersEvent(0, '<% document.write("Hello World");%>');
  }

  function testNastyJasp()
  {
    $this->parser->parse('<' . '% <tag a="A"><?php ?></tag><!-- comment --> %>');
    $this->checkEventsCount(6);
    $this->checkCharactersEvent(0, '<% ');
    $this->checkStartTagEvent(1, 'tag', array('a' => 'A'));
    $this->checkInstructionEvent(2, 'php', '');
    $this->checkEndTagEvent(3, 'tag');
    $this->checkCharactersEvent(4, '<!-- comment -->');
    $this->checkCharactersEvent(5, ' %>');
  }

  function testJaspInTag()
  {
    $this->parser->parse('<tag><' . '% document.write("Hello World");%></tag>');
    $this->checkStartTagEvent(0, 'tag', array());
    $this->checkCharactersEvent(1, '<% document.write("Hello World");%>');
    $this->checkEndTagEvent(2, 'tag');
  }

  function testTruncatedJasp()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<%', $this->file_name);
    $this->checkEventsCount(2);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<%', $location);
  }

  function testTruncatedJaspNoClose()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<% more stuff', $this->file_name);
    $this->checkEventsCount(2);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<% more stuff', $location);
  }

  function testEmptyCloseTag()
  {
    $this->parser->parse('</>', $this->file_name);
    $this->checkEventsCount(1);
    $this->checkEndTagEvent(0, '', array());
  }

  function testProcessingInstruction()
  {
    $location = $this->_createLocation();
    $this->parser->parse('<?php ?>', $this->file_name);
    $this->checkEventsCount(1);
    $this->checkInstructionEvent(0, 'php', '', $location);
  }

  function testAllProcessingInstruction()
  {
    $this->parser->parse('<?php print "Hello"; ?>', $this->file_name);
    $this->checkEventsCount(1);
    $this->checkInstructionEvent(0, 'php', 'print "Hello"; ');
  }

  function testNestedProcessingInstruction()
  {
    $this->parser->parse('a<?php print "Hello"; ?>b', $this->file_name);

    $this->checkEventsCount(3);
    $this->checkCharactersEvent(0, 'a');
    $this->checkInstructionEvent(1, 'php', 'print "Hello"; ');
    $this->checkCharactersEvent(2, 'b');
  }

  function testTruncatedOpen()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<', $this->file_name);
    $this->checkEventsCount(2);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<', $location);
  }

  function testTruncatedEmptyClose()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff</', $this->file_name);
    $this->checkEventsCount(2);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '</', $location);
  }

  function testTruncatedClose()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff</a', $this->file_name);
    $this->checkEventsCount(2);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '</a', $location);
  }

  function testTruncatedProcessingInstruction()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<?', $this->file_name);
    $this->checkEventsCount(2);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<?', $location);
  }

  function testTruncatedProcessingInstructionNoClose()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
 ', $this->file_name);

    $this->checkEventsCount(2);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
 ');
  }

  function testMalformedProcessingInstruction()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<?>', $this->file_name);

    $this->checkEventsCount(2);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<?>', $location);
  }

  function testMalformedProcessingInstruction2()
  {
    $location = $this->_createLocation();
    $this->parser->parse('stuff<??>', $this->file_name);

    $this->checkEventsCount(2);
    $this->checkCharactersEvent(0, 'stuff', $location);
    $this->checkCharactersEvent(1, '<??>', $location);
  }

  function checkEventsCount($count)
  {
    $this->assertEqual(sizeof($this->listener->events), $count);
  }

  function checkCharactersEvent($event_index, $characters, $location = null)
  {
    $event = $this->listener->events[$event_index];
    $this->assertEqual($event[0], 'characters');
    $this->assertEqual($event[1], $characters);
    if($location)
      $this->assertEqual($event[2], $location);
  }

  function checkStartTagEvent($event_index, $tag, $attributes, $location = null)
  {
    $event = $this->listener->events[$event_index];
    $this->assertEqual($event[0], 'startTag');
    $this->assertEqual($event[1], $tag);
    $this->assertEqual($event[2], $attributes);
    if($location)
      $this->assertEqual($event[3], $location);
  }

  function checkEndTagEvent($event_index, $tag, $location = null)
  {
    $event = $this->listener->events[$event_index];
    $this->assertEqual($event[0], 'endTag');
    $this->assertEqual($event[1], $tag);
    if($location)
      $this->assertEqual($event[2], $location);
  }

  function checkEmptyTagEvent($event_index, $tag, $attributes, $location = null)
  {
    $event = $this->listener->events[$event_index];
    $this->assertEqual($event[0], 'emptyTag');
    $this->assertEqual($event[1], $tag);
    $this->assertEqual($event[2], $attributes);
    if($location)
      $this->assertEqual($event[3], $location);
  }

  function checkInstructionEvent($event_index, $type, $code, $location = null)
  {
    $event = $this->listener->events[$event_index];
    $this->assertEqual($event[0], 'instruction');
    $this->assertEqual($event[1], $type);
    $this->assertEqual($event[2], $code);
    if($location)
      $this->assertEqual($event[3], $location);
  }
}

