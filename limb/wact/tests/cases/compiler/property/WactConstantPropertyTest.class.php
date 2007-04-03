<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactConstantPropertyTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
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
