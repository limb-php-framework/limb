<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class lmbMacroHtmlTagWidgetTest extends UnitTestCase
{
  function setUp()
  {
    $this->widget = new lmbMacroHtmlTagWidget('TestId');
  }

  function testGetAttribute()
  {
    $this->widget->setAttribute('foo', 'bar');
    $this->assertEqual($this->widget->getAttribute('foo'), 'bar');
    $this->assertEqual($this->widget->getAttribute('FOO'), 'bar');//case insensitive
  }

  function testGetBoolAttributeFalse()
  {
    $this->assertFalse($this->widget->getBoolAttribute('foo'));//by default

    $this->widget->setAttribute('foo', 'false');
    $this->assertFalse($this->widget->getBoolAttribute('foo'));

    $this->widget->setAttribute('foo', '0');
    $this->assertFalse($this->widget->getBoolAttribute('foo'));

    $this->widget->setAttribute('foo', 'no');
    $this->assertFalse($this->widget->getBoolAttribute('foo'));

    $this->widget->setAttribute('foo', 'none');
    $this->assertFalse($this->widget->getBoolAttribute('foo'));

    $this->widget->setAttribute('foo', false);
    $this->assertFalse($this->widget->getBoolAttribute('foo'));
  }

  function testGetBoolAttributeTrue()
  {
    $this->widget->setAttribute('foo', 'true');
    $this->assertTrue($this->widget->getBoolAttribute('foo'));

    $this->widget->setAttribute('foo', '1');
    $this->assertTrue($this->widget->getBoolAttribute('foo'));
  }

  function testGetBoolAttributeCaseInsensitive()
  {
    $this->widget->setAttribute('foo', 'true');
    $this->assertTrue($this->widget->getBoolAttribute('FOO'));
  }

  function testGetUnsetAttribute()
  {
    $this->assertNull($this->widget->getAttribute('class'));
  }

  function testHasAttributeUnset()
  {
    $this->assertFalse($this->widget->hasAttribute('class'));
  }

  function testRenderAttributes()
  {
    $this->widget->setAttribute('a','red');
    $this->widget->setAttribute('b','blue');
    $this->widget->setAttribute('c','green');
    ob_start();
    $this->widget->renderAttributes();
    $output = ob_get_contents();
    ob_end_clean();
    $this->assertEqual(' a="red" b="blue" c="green"',$output);
  }

  function testRemoveAttribute()
  {
    $this->widget->setAttribute('foo', 'bar');
    $this->widget->setAttribute('untouched', 'value');
    $this->assertTrue( $this->widget->hasAttribute('foo'));
    $this->widget->removeAttribute('FOO');
    $this->assertFalse( $this->widget->hasAttribute('foo'));
  }

  function testHasAttribute()
  {
    $this->widget->setAttribute('foo', 'bar');
    $this->widget->setAttribute('tricky', NULL);
    $this->assertTrue( $this->widget->hasAttribute('foo'));
    $this->assertTrue( $this->widget->hasAttribute('tricky'));
    $this->assertFalse( $this->widget->hasAttribute('missing'));
    $this->assertTrue( $this->widget->hasAttribute('FOO'));
    $this->assertTrue( $this->widget->hasAttribute('TRICKY'));
    $this->assertFalse( $this->widget->hasAttribute('MISSING'));
  }

  function testGetAttributeUnset()
  {
    $this->assertNull($this->widget->getAttribute('foo'));
  }
}


