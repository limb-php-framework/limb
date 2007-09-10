<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/lmbMacroTemplate.class.php');
lmb_require('limb/macro/src/tags/wrap.tag.php');
lmb_require('limb/macro/src/tags/slot.tag.php');
lmb_require('limb/fs/src/lmbFs.class.php');

class lmbMacroWrapTagTest extends UnitTestCase
{
  function setUp()
  {
    lmbFs :: rm(LIMB_VAR_DIR . '/tpl');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/tpl/compiled');
  }

  function testSimpleWrap()
  {
    $bar = '<%wrap with="foo.html" into="slot1"%>Bob<%/wrap%>';
    $foo = '<p>Hello, <%slot id="slot1"/%></p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<p>Hello, Bob</p>');
  }

  function testWrapWithVariables()
  {
    $bar = '<%wrap with="foo.html" into="slot1"%><?php echo $this->bob?><%/wrap%>';
    $foo = '<p>Hello, <%slot id="slot1"/%></p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('bob', 'Bob');

    $out = $macro->render();
    $this->assertEqual($out, '<p>Hello, Bob</p>');
  }

  function testNestedWrap()
  {
    $bar = '<%wrap with="foo.html" into="slot1"%><?php echo $this->bob?><%/wrap%>';
    $foo = '<%wrap with="zoo.html" into="slot2"%><p>Hello, <%slot id="slot1"/%></p><%/wrap%>';
    $zoo = '<body><%slot id="slot2"/%></body>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');
    $zoo_tpl = $this->_createTemplate($zoo, 'zoo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('bob', 'Bob');

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Hello, Bob</p></body>');
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

