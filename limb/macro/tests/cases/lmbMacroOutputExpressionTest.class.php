<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroOutputExpressionsTest extends lmbBaseMacroTest
{
  function testSimpleOutput()
  {
    $content = '{$#var}';

    $macro = $this->_createMacroTemplate($content, 'tpl.html');

    $macro->set('var', 'Foo');

    $out = $macro->render();
    $this->assertEqual($out, 'Foo');
  }

  function testSimpleChainedOutputForArray()
  {
    $content = '{$#var.foo.bar}';

    $macro = $this->_createMacroTemplate($content, 'tpl.html');

    $macro->set('var', array('foo' => array('bar' => 'Hey')));

    $out = $macro->render();
    $this->assertEqual($out, 'Hey');
  }

  function testBrokenChainOutputForArray()
  {
    $content = '{$#var.foo.bar.baz}';

    $macro = $this->_createMacroTemplate($content, 'tpl.html');

    $macro->set('var', null);
    $out = $macro->render();
    $this->assertEqual($out, '');

    $macro->set('var', array('foo' => null));
    $out = $macro->render();
    $this->assertEqual($out, '');

    $macro->set('var', array('foo' => array('bar' => null)));
    $out = $macro->render();
    $this->assertEqual($out, '');
  }

  function testSimpleChainedOutputForObject()
  {
    $content = '{$#var.foo.bar}';

    $macro = $this->_createMacroTemplate($content, 'tpl.html');

    $macro->set('var', new lmbObject(array('foo' => new lmbObject(array('bar' => 'Hey')))));

    $out = $macro->render();
    $this->assertEqual($out, 'Hey');
  }

  function testBrokenChainOutputForObject()
  {
    $content = '{$#var.foo.bar.baz}';

    $macro = $this->_createMacroTemplate($content, 'tpl.html');

    $macro->set('var', null);
    $out = $macro->render();
    $this->assertEqual($out, '');

    $macro->set('var', new lmbObject(array('foo' => null)));
    $out = $macro->render();
    $this->assertEqual($out, '');

    $macro->set('var', new lmbObject(array('foo' => new lmbObject(array('bar' => null)))));
    $out = $macro->render();
    $this->assertEqual($out, '');
  }

  function testChainedOutputForMixedArraysAndObjects()
  {
    $content = '{$#var.foo.bar}';

    $macro = $this->_createMacroTemplate($content, 'tpl.html');

    $macro->set('var', new lmbObject(array('foo' => array('bar' => 'Hey'))));

    $out = $macro->render();
    $this->assertEqual($out, 'Hey');
  }

  function testBrokenChainOutputForMixedArraysAndObjects()
  {
    $content = '{$#var.foo.bar.baz}';

    $macro = $this->_createMacroTemplate($content, 'tpl.html');

    $macro->set('var', new lmbObject(array('foo' => array('bar' => null))));
    $out = $macro->render();
    $this->assertEqual($out, '');
  }

  function testTemplateWithOutputExpression()
  {
    $code = '<h1>{$#bar}</h1>';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('bar', "foo");
    $out = $tpl->render();
    $this->assertEqual($out, '<h1>foo</h1>');
  }
}

