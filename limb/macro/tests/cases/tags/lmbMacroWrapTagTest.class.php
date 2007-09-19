<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/fs/src/lmbFs.class.php');
lmb_require('limb/macro/src/lmbMacroTemplate.class.php');
lmb_require('limb/macro/src/lmbMacroTagDictionary.class.php');

lmbMacroTagDictionary :: instance()->registerFromFile(dirname(__FILE__) . '/../../../src/tags/wrap.tag.php');
lmbMacroTagDictionary :: instance()->registerFromFile(dirname(__FILE__) . '/../../../src/tags/slot.tag.php');
lmbMacroTagDictionary :: instance()->registerFromFile(dirname(__FILE__) . '/../../../src/tags/into.tag.php');

class lmbMacroWrapTagTest extends UnitTestCase
{
  function setUp()
  {
    lmbFs :: rm(LIMB_VAR_DIR . '/tpl');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/tpl/compiled');
  }

  function testSimpleStaticWrap()
  {
    $bar = '<%wrap with="foo.html" into="slot1"%>Bob<%/wrap%>';
    $foo = '<p>Hello, <%slot id="slot1"/%></p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<p>Hello, Bob</p>');
  }

  function testStaticWrapWithVariables()
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

  function testNestedStaticWrap()
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

  function testMultiStaticWrap()
  {
    $bar = '<%wrap with="foo.html"%><%into slot="slot1"%>Bob<%/into%><%into slot="slot2"%>Thorton<%/into%><%/wrap%>';
    $foo = '<p>Hello, <%slot id="slot2"/%> <%slot id="slot1"/%></p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<p>Hello, Thorton Bob</p>');
  }

  function testSimpleDynamicWrap()
  {    
    $bar = '<%wrap with="$this->layout" into="slot1"%>Bob<%/wrap%>';
    $foo = '<p>Hello, <%slot id="slot1"/%></p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('layout', 'foo.html');

    $out = $macro->render();
    $this->assertEqual($out, '<p>Hello, Bob</p>'); 
  }

  function testMultiDynamicWrap()
  {
    $bar = '<%wrap with="$this->layout"%><%into slot="slot1"%>Bob<%/into%><%into slot="slot2"%>Thorton<%/into%><%/wrap%>';
    $foo = '<p>Hello, <%slot id="slot2"/%> <%slot id="slot1"/%></p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('layout', 'foo.html');

    $out = $macro->render();
    $this->assertEqual($out, '<p>Hello, Thorton Bob</p>');
  }

  function testMixStaticAndDynamicWrap()
  {
    $bar = '<%wrap with="$this->layout" into="slot1"%><?php echo $this->bob?><%/wrap%>';
    $foo = '<%wrap with="zoo.html" into="slot2"%><p>Hello, <%slot id="slot1"/%></p><%/wrap%>';
    $zoo = '<body><%slot id="slot2"/%></body>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');
    $zoo_tpl = $this->_createTemplate($zoo, 'zoo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('layout', 'foo.html');
    $macro->set('bob', 'Bob');

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Hello, Bob</p></body>');
  }

  function testNestedDynamicWrap()
  {
    $bar = '<%wrap with="$this->layout1" into="slot1"%><?php echo $this->bob?><%/wrap%>';
    $foo = '<%wrap with="$this->layout2" into="slot2"%><p>Hello, <%slot id="slot1"/%></p><%/wrap%>';
    $zoo = '<body><%slot id="slot2"/%></body>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');
    $zoo_tpl = $this->_createTemplate($zoo, 'zoo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('layout1', 'foo.html');
    $macro->set('layout2', 'zoo.html');
    $macro->set('bob', 'Bob');

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Hello, Bob</p></body>');
  }

  function testStaticallyWrappedChildAccessesParentData()
  {
    $bar = '<%wrap with="foo.html" into="slot1"%><?php echo $this->bob?><%/wrap%>';
    $foo = '<?php $this->bob = "Bob";?><p>Hello, <%slot id="slot1"/%></p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<p>Hello, Bob</p>');
  }

  function testDynamicallyWrappedChildAccessesParentData()
  {
    $bar = '<%wrap with="$this->layout" into="slot1"%><?php echo $this->bob?><%/wrap%>';
    $foo = '<?php $this->bob = "Bob";?><p>Hello, <%slot id="slot1"/%></p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('layout', 'foo.html');

    $out = $macro->render();
    $this->assertEqual($out, '<p>Hello, Bob</p>');
  }

  function testStaticallyWrappedChildLocalVarsAreIsolated()
  {
    $bar = '<%wrap with="foo.html" into="slot1"%><?php $foo = "Todd";?><%/wrap%>';
    $foo = '<?php $foo = "Bob";?><%slot id="slot1"/%><?php echo $foo;?>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, 'Bob'); 
  }

  function testDynamicallyWrappedChildLocalVarsAreIsolated()
  {
    $bar = '<%wrap with="$this->layout" into="slot1"%><?php $foo = "Todd";?><%/wrap%>';
    $foo = '<?php $foo = "Bob";?><%slot id="slot1"/%><?php echo $foo;?>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('layout', 'foo.html');

    $out = $macro->render();
    $this->assertEqual($out, 'Bob'); 
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

