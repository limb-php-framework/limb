<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/lmbMacroTemplate.class.php');
lmb_require('limb/core/src/lmbObject.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');
lmb_require('limb/macro/src/lmbMacroTagDictionary.class.php');

//output tag is a special case, it's always registered in lmbMacroTagDictionary

class lmbMacroOutputTagTest extends UnitTestCase
{
  function setUp()
  {
    lmbFs :: rm(LIMB_VAR_DIR . '/tpl');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/tpl/compiled');
  }

  function testSimpleOutput()
  {
    $content = '<%$#var%>';

    $tpl = $this->_createTemplate($content, 'tpl.html');

    $macro = $this->_createMacro($tpl);
    $macro->set('var', 'Foo');

    $out = $macro->render();
    $this->assertEqual($out, 'Foo');
  }

  function testSimpleChainedOutputForArray()
  {
    $content = '<%$#var.foo.bar%>';

    $tpl = $this->_createTemplate($content, 'tpl.html');

    $macro = $this->_createMacro($tpl);
    $macro->set('var', array('foo' => array('bar' => 'Hey')));

    $out = $macro->render();
    $this->assertEqual($out, 'Hey');
  }

  function testBrokenChainOutputForArray()
  {
    $content = '<%$#var.foo.bar.baz%>';

    $tpl = $this->_createTemplate($content, 'tpl.html');

    $macro = $this->_createMacro($tpl);

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
    $content = '<%$#var.foo.bar%>';

    $tpl = $this->_createTemplate($content, 'tpl.html');

    $macro = $this->_createMacro($tpl);
    $macro->set('var', new lmbObject(array('foo' => new lmbObject(array('bar' => 'Hey')))));

    $out = $macro->render();
    $this->assertEqual($out, 'Hey');
  }

  function testBrokenChainOutputForObject()
  {
    $content = '<%$#var.foo.bar.baz%>';

    $tpl = $this->_createTemplate($content, 'tpl.html');

    $macro = $this->_createMacro($tpl);

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
    $content = '<%$#var.foo.bar%>';

    $tpl = $this->_createTemplate($content, 'tpl.html');

    $macro = $this->_createMacro($tpl);
    $macro->set('var', new lmbObject(array('foo' => array('bar' => 'Hey'))));

    $out = $macro->render();
    $this->assertEqual($out, 'Hey'); 
  }

  function testBrokenChainOutputForMixedArraysAndObjects()
  {
    $content = '<%$#var.foo.bar.baz%>';

    $tpl = $this->_createTemplate($content, 'tpl.html');

    $macro = $this->_createMacro($tpl);

    $macro->set('var', new lmbObject(array('foo' => array('bar' => null))));
    $out = $macro->render();
    $this->assertEqual($out, '');
  }

  protected function _createMacro($file)
  {
    $base_dir = LIMB_VAR_DIR . '/tpl';
    $cache_dir = LIMB_VAR_DIR . '/tpl/compiled';
    $macro = new lmbMacroTemplate($file,
                                  $cache_dir,
                                  new lmbMacroTemplateLocator($base_dir, $cache_dir));
    return $macro;
  }

  protected function _createTemplate($code, $name)
  {
    $file = LIMB_VAR_DIR . '/tpl/' . $name;
    file_put_contents($file, $code);
    return $file;
  }
}

