<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactRuntimeTagComponentTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/tests/cases/component/WactRuntimeComponentTest.class.php';

class WactRuntimeTagComponentTest extends WactRuntimeComponentTest
{
  function setUp()
  {
    parent :: setUp();
    $this->component = new WactRuntimeTagComponent('TestId');
  }

  function testGetAttribute()
  {
    $this->component->setAttribute('foo', 'bar');
    $this->assertEqual($this->component->getAttribute('foo'), 'bar');
    $this->assertEqual($this->component->getAttribute('FOO'), 'bar');
  }

  function testGetUnsetAttribute()
  {
    $this->assertNull($this->component->getAttribute('class'));
  }

  function testHasAttributeUnset()
  {
    $this->assertFalse($this->component->hasAttribute('class'));
  }

  function testRenderAttributes()
  {
    $this->component->setAttribute('a','red');
    $this->component->setAttribute('b','blue');
    $this->component->setAttribute('c','green');
    ob_start();
    $this->component->renderAttributes();
    $output = ob_get_contents();
    ob_end_clean();
    $this->assertEqual(' a="red" b="blue" c="green"',$output);
  }

  function testRemoveAttribute()
  {
    $this->component->setAttribute('foo', 'bar');
    $this->component->setAttribute('untouched', 'value');
    $this->assertTrue( $this->component->hasAttribute('foo'));
    $this->component->removeAttribute('FOO');
    $this->assertFalse( $this->component->hasAttribute('foo'));
  }

  function testHasAttribute()
  {
    $this->component->setAttribute('foo', 'bar');
    $this->component->setAttribute('tricky', NULL);
    $this->assertTrue( $this->component->hasAttribute('foo'));
    $this->assertTrue( $this->component->hasAttribute('tricky'));
    $this->assertFalse( $this->component->hasAttribute('missing'));
    $this->assertTrue( $this->component->hasAttribute('FOO'));
    $this->assertTrue( $this->component->hasAttribute('TRICKY'));
    $this->assertFalse( $this->component->hasAttribute('MISSING'));
  }

  function testGetAttributeUnset()
  {
    $this->assertNull($this->component->getAttribute('foo'));
  }
}

?>