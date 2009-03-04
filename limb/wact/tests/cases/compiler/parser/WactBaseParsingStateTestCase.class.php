<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';

Mock::generate('WactSourceFileParser','MockWactSourceFileParser');
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

  function testGetAttributeStringRunat()
  {
    $attrs = array('foo'=>'bar', 'runat'=>'client');
    $this->assertIdentical($this->state->getAttributeString($attrs), ' foo="bar"');
  }
}

SimpleTestOptions :: ignore('WactBaseParsingStateTestCase');


