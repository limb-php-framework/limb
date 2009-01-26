<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroTagIncludeBCTest extends lmbBaseMacroTest
{
  function testSimpleStaticInclude()
  {
    $bar = '<body>{{include file="foo.html"/}}</body>';
    $foo = '<p>Hello, Bob</p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Hello, Bob</p></body>');
  }

  function testNestedStaticInclude()
  {
    $bar = '<body>{{include file="foo.html"/}}</body>';
    $foo = '<p>Hello, {{include file="name.html"/}}</p>';
    $name = "Bob";

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');
    $name_tpl = $this->_createTemplate($name, 'name.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Hello, Bob</p></body>');
  }

  function testStaticIncludePassVariables()
  {
    $bar = '<body><?php $var2=2;?>{{include file="foo.html" var1="1" var2="$var2"/}}</body>';
    $foo = '<p>Numbers: <?php echo $var1;?> <?php echo $var2;?></p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Numbers: 1 2</p></body>');
  }

  function testStaticInlineInclude()
  {
    $bar = '<body><?php $var2=2;?>{{include file="foo.html" inline="true"/}}</body>';
    $foo = '<p>Number: <?php echo $var2;?></p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Number: 2</p></body>');
  }

  function testStaticIncludeMixLocalAndTemplateVariables()
  {
    $bar = '<body><?php $var2=2;?>{{include file="foo.html" var1="1" var2="$var2"/}}</body>';
    $foo = '<p>Numbers: <?php echo $var1;?> <?php echo $var2;?> <?php echo $this->var3;?></p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $macro->set('var3', 3);
    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Numbers: 1 2 3</p></body>');
  }

  function testDynamicInclude()
  {
    $bar = '<body>{{include file="$this->file"/}}</body>';
    $foo = '<p>Hello!</p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('file', 'foo.html');

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Hello!</p></body>');
  }

  function testDynamicIncludePassLocalVars()
  {
    $bar = '<body><?php $name = "Fred";?>{{include file="$this->file" name="$name"/}}</body>';
    $foo = '<p>Hello, <?php echo $name;?>!</p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('file', 'foo.html');

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Hello, Fred!</p></body>');
  }

  function testDynamicIncludeMixLocalAndTemplateVars()
  {
    $bar = '<body><?php $name = "Fred";?>{{include file="$this->file" name="$name"/}}</body>';
    $foo = '<p>Hello, <?php echo $name . " " . $this->lastname;?>!</p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('file', 'foo.html');
    $macro->set('lastname', 'Atkins');

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Hello, Fred Atkins!</p></body>');
  }

  function testStaticIncludeWithChildIntoTagsAndVariables()
  {
    $bar = '<body><?php $var2=2;?>'.
           '{{include file="foo.html" var1="1" var2="$var2"}}'.
           '{{include:into slot="slot1"}}<b><?php echo $varA;?></b>{{/include:into}}'.
           '{{include:into slot="slot2"}}<u><?php echo $varB;?></u>{{/include:into}}'.
           '{{/include}}'.
           '</body>';
    $foo = '<p>Numbers: {{slot id="slot1" varA="$var1"/}} {{slot id="slot2" varB="$var2"/}}</p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Numbers: <b>1</b> <u>2</u></p></body>');
  }

  function testDynamicIncludeWithChildIntoTagsAndVariables()
  {
    $bar = '<body><?php $var2=2;?>'.
           '{{include file="$#included" var1="1" var2="$var2"}}'.
           '{{include:into slot="slot1"}}<b><?php echo $varA;?></b>{{/include:into}}'.
           '{{include:into slot="slot2"}}<u><?php echo $varB;?></u>{{/include:into}}'.
           '{{/include}}'.
           '</body>';
    $foo = '<p>Numbers: {{slot id="slot1" varA="$var1"/}} {{slot id="slot2" varB="$var2"/}}</p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('included', 'foo.html');

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Numbers: <b>1</b> <u>2</u></p></body>');
  }
}

