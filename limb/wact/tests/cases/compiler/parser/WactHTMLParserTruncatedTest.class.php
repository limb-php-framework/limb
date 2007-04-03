<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactHTMLParserTruncatedTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/wact/tests/cases/compiler/parser/WactHTMLParserTest.class.php');

class WactHTMLParserTruncatedTest extends UnitTestCase
{
  protected $parser;
  protected $listener;

  function setUp()
  {
    $this->listener = new MockWactParserListener();
    $this->parser = new WactHTMLParser($this->listener);
  }

  function testTruncatedOpen()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<'));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<');
  }

  function testTruncatedEmptyClose()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('</'));
    $this->listener->expectNever('endElement');
    $this->parser->parse('stuff</');
  }

  function testTruncatedClose()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('</a'));
    $this->parser->parse('stuff</a');
    $this->listener->expectNever('endElement');
  }

  function testTruncatedProcessingInstruction()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<?'));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<?');
  }

  function testTruncatedProcessingInstructionTarget()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactHTMLParserTruncatedTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */
'));
    $this->listener->expectNever('processingInstruction');
    $this->parser->parse('stuff<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactHTMLParserTruncatedTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */
');
  }

  function testTruncatedProcessingInstructionNoClose()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactHTMLParserTruncatedTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */
 '));
    $this->listener->expectNever('processingInstruction');
    $this->parser->parse('stuff<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactHTMLParserTruncatedTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */
 ');
  }

  function testTruncatedJasp()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<%'));
    $this->listener->expectNever('jasp');
    $this->parser->parse('stuff<%');
  }

  function testTruncatedJaspNoClose()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<% more stuff'));
    $this->listener->expectNever('jasp');
    $this->parser->parse('stuff<% more stuff');
  }

  function testTruncatedComment()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<!--'));
    $this->listener->expectNever('escape');
    $this->listener->expectNever('comment');
    $this->parser->parse('stuff<!--');
  }

  function testTruncatedCommentNoClose()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<!-- blah'));
    $this->listener->expectNever('escape');
    $this->listener->expectNever('comment');
    $this->parser->parse('stuff<!-- blah');
  }

  function testTruncatedDocType()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<!doctype'));
    $this->listener->expectNever('escape');
    $this->parser->parse('stuff<!doctype');
  }

  function testTruncatedDocTypetNoClose()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<!doctype blah'));
    $this->listener->expectNever('escape');
    $this->parser->parse('stuff<!doctype blah');
  }

  function testTruncatedOpenElementChar()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<a'));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<a');
  }

  function testTruncatedOpenElement()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<tag'));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<tag');
  }

  function testTruncatedOpenElementSpace()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<tag '));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<tag ');
  }

  function testTruncatedOpenElementMinimizedAttribute()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<tag attribute'));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<tag attribute');
  }

  function testTruncatedOpenElementMinimizedAttributeSpace()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<tag attribute '));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<tag attribute ');
  }

  function testTruncatedOpenElementAttribute()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<tag attribute='));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<tag attribute=');
  }

  function testTruncatedOpenElementAttributeSpace()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<tag attribute= '));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<tag attribute= ');
  }

  function testTruncatedOpenElementAttributeNoQuote()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<tag attribute=value'));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<tag attribute=value');
  }

  function testTruncatedOpenElementAttributeDoubleQuote()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<tag attribute="'));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<tag attribute="');
  }

  function testTruncatedOpenElementAttributeDoubleQuoteNoClose()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<tag attribute="value'));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<tag attribute="value');
  }

  function testTruncatedOpenElementAttributeDoubleQuoteValue()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<tag attribute="value"'));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<tag attribute="value"');
  }

  function testTruncatedOpenElementAttributeSingleQuote()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<tag attribute=\''));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<tag attribute=\'');
  }

  function testTruncatedOpenElementAttributeSingleQuoteNoClose()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<tag attribute=\'value'));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<tag attribute=\'value');
  }

  function testTruncatedOpenElementAttributeSingleQuoteValue()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<tag attribute=\'value\''));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<tag attribute=\'value\'');
  }

  function testTruncatedOpenElementClose()
  {
    $this->listener->expectOnce('characters', array('stuff'));
    $this->listener->expectOnce('unexpectedEOF', array('<tag attribute=\'value\'/'));
    $this->listener->expectNever('startElement');
    $this->parser->parse('stuff<tag attribute=\'value\'/');
  }
}
?>