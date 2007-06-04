<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactConstantPropertyTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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

?>
