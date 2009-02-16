<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroInsertTagTest extends lmbBaseMacroTest
{
  function testSimpleStaticWrap()
  {
    $bar = '{{insert into="slot1" file="foo.html"}}Bob{{/insert}}';
    $foo = '<p>Hello, {{slot id="slot1"/}}</p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<p>Hello, Bob</p>');
  }

  function testStaticWrapWithVariables()
  {
    $bar = '{{insert into="slot1" file="foo.html"}}<?php echo $this->bob?>{{/insert}}';
    $foo = '<p>Hello, {{slot id="slot1"/}}</p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('bob', 'Bob');

    $out = $macro->render();
    $this->assertEqual($out, '<p>Hello, Bob</p>');
  }

  function testNestedStaticWrap()
  {
    $bar = '{{insert into="slot1" file="foo.html"}}<?php echo $this->bob?>{{/insert}}';
    $foo = '{{insert into="slot2" file="zoo.html"}}<p>Hello, {{slot id="slot1"/}}</p>{{/insert}}';
    $zoo = '<body>{{slot id="slot2"/}}</body>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');
    $zoo_tpl = $this->_createTemplate($zoo, 'zoo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('bob', 'Bob');

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Hello, Bob</p></body>');
  }
  
  function testSimpleStaticIntoRoot()
  {
    $included = '{{insert:into slot="slot1"}}Bob{{/insert:into}}';
    $main = '<p>Hello, {{slot id="slot1"/}}</p>{{insert file="included.html"/}}';

    $included_tpl = $this->_createTemplate($included, 'included.html');
    $main_tpl = $this->_createTemplate($main, 'main.html');

    $macro = $this->_createMacro($main_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<p>Hello, Bob</p>');
  }

  function testMultiStaticWrap()
  {
    $bar = '{{insert file="foo.html"}}'.
           '{{insert:into slot="slot1"}}Bob{{/insert:into}}'.
           '{{insert:into slot="slot2"}}Thorton{{/insert:into}}'.
           '{{/insert}}';
    $foo = '<p>Hello, {{slot id="slot2"/}} {{slot id="slot1"/}}</p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<p>Hello, Thorton Bob</p>');
  }

  function testSimpleDynamicWrap()
  {
    $bar = '{{insert into="slot1" file="$this->layout"}}Bob{{/insert}}';
    $foo = '<p>Hello, {{slot id="slot1"/}}</p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('layout', 'foo.html');

    $out = $macro->render();
    $this->assertEqual($out, '<p>Hello, Bob</p>');
  }

  function testMultiDynamicWrap()
  {
    $bar = '{{insert file="$this->layout"}}'.
           '{{insert:into slot="slot1"}}Bob{{/insert:into}}'.
           '{{insert:into slot="slot2"}}Thorton{{/insert:into}}'.
           '{{/insert}}';
    $foo = '<p>Hello, {{slot id="slot2"/}} {{slot id="slot1"/}}</p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('layout', 'foo.html');

    $out = $macro->render();
    $this->assertEqual($out, '<p>Hello, Thorton Bob</p>');
  }

  function testMixStaticAndDynamicWrap()
  {
    $bar = '{{insert into="slot1" file="$this->layout"}}<?php echo $this->bob?>{{/insert}}';
    $foo = '{{insert into="slot2" file="zoo.html"}}<p>Hello, {{slot id="slot1"/}}</p>{{/insert}}';
    $zoo = '<body>{{slot id="slot2"/}}</body>';

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
    $bar = '{{insert into="slot1" file="$this->layout1"}}<?php echo $this->bob?>{{/insert}}';
    $foo = '{{insert into="slot2" file="$this->layout2"}}<p>Hello, {{slot id="slot1"/}}</p>{{/insert}}';
    $zoo = '<body>{{slot id="slot2"/}}</body>';

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
  
  function testIntoTagIncludedByTemplateWithDynamicWrap()
  {
    $bar = '|begin|slot1:{{slot id="slot1"/}}|slot2:{{slot id="slot2"/}}|end|';
    $foo = '{{wrap file="$this->layout1"}}'.
            '{{into slot="slot1"}}Hi{{/into}}'.
            '{{into slot="slot2"}}Hello{{/into}}'.
            '{{insert file="zoo.html"/}}'.
            '{{/wrap}}';
    $zoo = '{{into slot="slot2"}}Bye{{/into}}';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');
    $zoo_tpl = $this->_createTemplate($zoo, 'zoo.html');

    $macro = $this->_createMacro($foo_tpl);
    $macro->set('layout1', 'bar.html');

    $out = $macro->render();
    $this->assertEqual($out, '|begin|slot1:Hi|slot2:HelloBye|end|');
  }

  function testStaticallyWrappedChildAccessesParentData()
  {
    $bar = '{{insert into="slot1" file="foo.html"}}<?php echo $this->bob?>{{/insert}}';
    $foo = '<?php $this->bob = "Bob";?><p>Hello, {{slot id="slot1"/}}</p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<p>Hello, Bob</p>');
  }

  function testDynamicallyWrappedChildAccessesParentData()
  {
    $bar = '{{insert file="$this->layout" into="slot1"}}<?php echo $this->bob?>{{/insert}}';
    $foo = '<?php $this->bob = "Bob";?><p>Hello, {{slot id="slot1"/}}</p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('layout', 'foo.html');

    $out = $macro->render();
    $this->assertEqual($out, '<p>Hello, Bob</p>');
  }

  function testStaticallyWrappedChildLocalVarsAreIsolated()
  {
    $bar = '{{insert file="foo.html" into="slot1"}}<?php $foo = "Todd";?>{{/insert}}';
    $foo = '<?php $foo = "Bob";?>{{slot id="slot1"/}}<?php echo $foo;?>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, 'Bob');
  }

  function testDynamicallyWrappedChildLocalVarsAreIsolated()
  {
    $bar = '{{insert file="$this->layout" into="slot1"}}<?php $foo = "Todd";?>{{/insert}}';
    $foo = '<?php $foo = "Bob";?>{{slot id="slot1"/}}<?php echo $foo;?>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('layout', 'foo.html');

    $out = $macro->render();
    $this->assertEqual($out, 'Bob');
  }
  
  function testMultiStaticWrapFromIncludedFile()
  {
    $child = '{{into slot="slot1"}}Bob{{/into}}{{into slot="slot2"}}Thorton{{/into}}';
    $main = '{{insert file="base.html"}}{{insert file="child.html"/}}{{/insert}}';
    $base = '<p>Hello, {{slot id="slot2"/}} {{slot id="slot1"/}}</p>';

    $child_tpl = $this->_createTemplate($child, 'child.html');
    $base_tpl = $this->_createTemplate($base, 'base.html');
    $main_tpl = $this->_createTemplate($main, 'main.html');

    $macro = $this->_createMacro($main_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<p>Hello, Thorton Bob</p>');
  }

  function testPassVariablesIntoLocalContextOfSlotTag()
  {
    $bar = '{{insert file="foo.html" into="slot1"}}<?php echo $foo;?>{{/insert}}';
    $foo = '<?php $foo = "Bob";?>{{slot id="slot1" foo="$foo"/}}';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, 'Bob');
  }

  function testSlotWithInlineAttributeDoesNotCreateAMethodAround()
  {
    $bar = '{{insert file="foo.html" into="slot1"}}<?php $foo = "Tedd";?>{{/insert}}';
    $foo = '<?php $foo = "Bob";?>{{slot id="slot1" inline="true"/}}<?php echo $foo;?>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, 'Tedd');
  }

  function testSimpleStaticInclude()
  {
    $bar = '<body>{{insert file="foo.html"/}}</body>';
    $foo = '<p>Hello, Bob</p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Hello, Bob</p></body>');
  }

  function testNestedStaticInclude()
  {
    $bar = '<body>{{insert file="foo.html"/}}</body>';
    $foo = '<p>Hello, {{insert file="name.html"/}}</p>';
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
    $bar = '<body><?php $var2=2;?>{{insert file="foo.html" var1="1" var2="$var2"/}}</body>';
    $foo = '<p>Numbers: <?php echo $var1;?> <?php echo $var2;?></p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Numbers: 1 2</p></body>');
  }

  function testStaticInlineInclude()
  {
    $bar = '<body><?php $var2=2;?>{{insert file="foo.html" inline="true"/}}</body>';
    $foo = '<p>Number: <?php echo $var2;?></p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Number: 2</p></body>');
  }

  function testDynamicInlineIncludeThrowsException()
  {
    $bar = '<body><?php $var2=2;?>{{insert file="$#foo" inline="true"/}}</body>';
    $foo = '<p>Number: <?php echo $var2;?></p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('foo', "foo.html");

    try
    {
      $out = $macro->render();
      $this->assertTrue(false);
    }
    catch(lmbMacroException $e)
    {
    }
  }
  
  function testStaticIncludeMixLocalAndTemplateVariables()
  {
    $bar = '<body><?php $var2=2;?>{{insert file="foo.html" var1="1" var2="$var2"/}}</body>';
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
    $bar = '<body>{{insert file="$this->file"/}}</body>';
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
    $bar = '<body><?php $name = "Fred";?>{{insert file="$this->file" name="$name"/}}</body>';
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
    $bar = '<body><?php $name = "Fred";?>{{insert file="$this->file" name="$name"/}}</body>';
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
           '{{insert file="foo.html" var1="1" var2="$var2"}}'.
           '{{insert:into slot="slot1"}}<b><?php echo $varA;?></b>{{/insert:into}}'.
           '{{insert:into slot="slot2"}}<u><?php echo $varB;?></u>{{/insert:into}}'.
           '{{/insert}}'.
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
           '{{insert file="$#included" var1="1" var2="$var2"}}'.
           '{{insert:into slot="slot1"}}<b><?php echo $varA;?></b>{{/insert:into}}'.
           '{{insert:into slot="slot2"}}<u><?php echo $varB;?></u>{{/insert:into}}'.
           '{{/insert}}'.
           '</body>';
    $foo = '<p>Numbers: {{slot id="slot1" varA="$var1"/}} {{slot id="slot2" varB="$var2"/}}</p>';

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('included', 'foo.html');

    $out = $macro->render();
    $this->assertEqual($out, '<body><p>Numbers: <b>1</b> <u>2</u></p></body>');
  }

  function testMixDynamicWrapWithStaticInsertWithChildIntoTags()
  {
    $layout = '<body>Main: {{slot id="slot_main"/}} Extra: {{slot id="slot_extra"/}}</body>';
    
    $bar = '{{insert file="$#layout"}}'.
           '{{insert:into slot="slot_main"}}'.
             '<?php $var2 = 2; ?>'.
             '{{insert file="foo.html" var1="1" var2="$var2"}}'.
               '{{insert:into slot="slot1"}}<b><?php echo $varA;?></b>{{/insert:into}}'.
               '{{insert:into slot="slot2"}}<u><?php echo $varB;?></u>{{/insert:into}}'.
             '{{/insert}}'.
           '{{/insert:into}}'.
           '{{/insert}}';
           
    $foo = '<p>Numbers: {{slot id="slot1" varA="$var1"/}} {{slot id="slot2" varB="$var2"/}}</p> '.
           '{{insert:into slot="slot_extra"}}Wow!{{/insert:into}}'; // !!!Note this wrap:into tag

    $bar_tpl = $this->_createTemplate($bar, 'bar.html');
    $foo_tpl = $this->_createTemplate($foo, 'foo.html');
    $layout_tpl = $this->_createTemplate($layout, 'layout.html');

    $macro = $this->_createMacro($bar_tpl);
    $macro->set('layout', 'layout.html');

    $out = $macro->render();
    $this->assertEqual($out, '<body>Main: <p>Numbers: <b>1</b> <u>2</u></p>  Extra: Wow!</body>');
  }
}

