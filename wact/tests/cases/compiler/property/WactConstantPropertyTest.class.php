<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';

class WactConstantPropertyTest extends UnitTestCase
{
  function testGetValue()
  {
    $property = new WactConstantProperty('value');
    $this->assertIdentical($property->getValue(), 'value');
  }

  function testIsConnstant()
  {
    $property = new WactConstantProperty('value');
    $this->assertIdentical($property->isConstant(), TRUE);
  }
}


