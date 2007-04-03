<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactHTMLParserMalformedTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/wact/tests/cases/compiler/parser/WactHTMLParserTest.class.php');

class WactHTMLParserMalformedTest extends UnitTestCase
{
  protected $parser;
  protected $listener;

  function setUp()
  {
    $this->listener = new MockWactParserListener();
    $this->parser = new WactHTMLParser($this->listener);
  }

  function testMalformedJasp()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<%>'));
    $this->listener->expectNever('jasp');
    $this->parser->parse('stuff<%>');
  }

  function testMalformedProcessingInstruction()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<?>'));
    $this->listener->expectNever('processingInstruction');
    $this->parser->parse('stuff<?>');
  }

  function testMalformedProcessingInstruction2()
  {
    $this->listener->expectOnce('unexpectedEOF', array('<??>'));
    $this->listener->expectNever('processingInstruction');
    $this->listener->expectNever('characters');
    $this->parser->parse('<??>');
  }

  function testMalformedComment()
  {
    $this->listener->expectOnce('unexpectedEOF', array('<!--x->'));
    $this->listener->expectNever('escape');
    $this->listener->expectNever('comment');
    $this->listener->expectNever('characters');
    $this->parser->parse('<!--x->');
  }

  function testOpenElementMalformedClose()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('invalidEntitySyntax', array('<tag attribute=\'value\'/morestuff'));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<tag attribute=\'value\'/morestuff');
  }

  function testOpenElementMalformedClose2()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('invalidEntitySyntax', array('<tag attribute=\'value\'/morestuff>'));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<tag attribute=\'value\'/morestuff>');
  }

  function testElementNestedSingleQuote()
  {
    $this->listener->expectOnce('startElement', array('tag', array('attribute' => '', "'" => NULL)));
    $this->listener->expectOnce('invalidAttributeSyntax');
    $this->listener->expectNever('characters');
    $this->listener->expectNever('endElement');
    $this->parser->parse('<tag attribute=\'\'\'>');
  }

  function testElementNestedDoubleQuote()
  {
    $this->listener->expectOnce('startElement', array('tag', array('attribute' => '', '"' => NULL)));
    $this->listener->expectOnce('invalidAttributeSyntax');
    $this->listener->expectNever('characters');
    $this->listener->expectNever('endElement');
    $this->parser->parse('<tag attribute=""">');
  }

  function testElementMalformedAttribute()
  {
    $this->listener->expectOnce('startElement', array('tag', array('attribute' => 'test', 'extra' => NULL)));
    $this->listener->expectOnce('invalidAttributeSyntax');
    $this->listener->expectNever('characters');
    $this->listener->expectNever('endElement');
    $this->parser->parse('<tag attribute="test"extra>');
  }
}

?>