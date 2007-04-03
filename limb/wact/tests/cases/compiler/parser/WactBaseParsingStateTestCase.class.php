<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactBaseParsingStateTestCase.class.php 5334 2007-03-23 11:48:20Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';

Mock::generate('WactHTMLParser','MockWactHTMLParser');
Mock::generate('WactSourceFileParser','MockWactSourceFileParser');
Mock::generate('WactNodeBuilder', 'MockNodeBuilder');
Mock::generate('WactTreeBuilder','MockTreeBuilder');
Mock::generate('WactTagDictionary','MockWactTagDictionary');
Mock::generate('WactRuntimeComponentTag','MockWactRuntimeComponentTag');
Mock::generate('WactRuntimeComponentHTMLTag','MockWactRuntimeComponentHTMLTag');

class WactBaseParsingStateTestCase extends UnitTestCase
{
  function testGetAttributeString()
  {
    $attrs = array('foo'=>'bar');
    $this->assertIdentical($this->state->getAttributeString($attrs), ' foo="bar"');
    $attrs = array();
    $this->assertIdentical($this->state->getAttributeString($attrs), '');
  }

  function testInvalidAttributeSyntax()
  {
    $Parser = new MockWactHTMLParser();
    $Parser->expectOnce('getPublicId');
    $Parser->expectOnce('getLineNumber');
    $this->state->setDocumentLocator($Parser);

    try
    {
      $this->state->invalidAttributeSyntax();
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Attribute syntax error/', $e->getMessage());
    }
  }
}

SimpleTestOptions :: ignore('WactBaseParsingStateTestCase');

?>