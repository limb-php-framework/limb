<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactBaseParsingStateTestCase.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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

?>