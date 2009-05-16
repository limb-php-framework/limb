<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroTemplateTagTest extends lmbBaseMacroTest
{
  function testApplyTemplate()
  {
    $content = '{{template name="tpl1"}}' .  
               '{$bar}' .                   
               '{{/template}}' .              
               '{{template name="tpl2"}}' .  
               '{$foo}{$hey}' .                   
               '{{/template}}' .
               '{{apply template="tpl2" hey="$#hey" foo="$#foo"/}}' .
               '{{apply template="tpl1" bar="$#bar"/}}' 
               ;

    $tpl = $this->_createTemplate($content, 'tree.html');

    $macro = $this->_createMacro($tpl);
    $macro->set('hey', 'HEY');
    $macro->set('bar', 'BAR');
    $macro->set('foo', 'FOO');

    $out = $macro->render();
    $this->assertEqual($out, 'FOOHEYBAR');
  }

  function testApplyTemplateInline()
  {
    $content = '{{template name="tpl1"}}' .  
               '{$bar}' .                   
               '{{/template}}' .              
               '{{template name="tpl2"}}' .  
               '{$foo}{$hey}' .                   
               '{{/template}}' .
               '<?php $hey = "HEY"; $foo = "FOO"; $bar = "BAR"; ?>'.
               '{{apply template="tpl2" inline="true"/}}' .
               '{{apply template="tpl1" inline="true"/}}';

    $tpl = $this->_createTemplate($content, 'tree.html');

    $macro = $this->_createMacro($tpl);

    $out = $macro->render();
    $this->assertEqual($out, 'FOOHEYBAR');
  }

  function testApplyTemplateWithIntoTags()
  {
    $content = '{{template name="tpl1"}}' .  
               '{{template:slot id="slotB"/}}{$bar}{{template:slot id="slotA"/}}' .                   
               '{{/template}}' .              
               '<?php $hey = "HEY"; ?>'.
               '{{apply template="tpl1" bar="$hey"}}{{apply:into slot="slotA"}}Hello!{{/apply:into}}{{/apply}}'.
               '{{apply template="tpl1" bar="AAA"}}{{apply:into slot="slotB"}}Wow!{{/apply:into}}{{/apply}}';

    $tpl = $this->_createTemplate($content, 'tree.html');

    $macro = $this->_createMacro($tpl);

    $out = $macro->render();
    $this->assertEqual($out, 'HEYHello!Wow!AAA');
  }

  function testApplyTemplateDynamic()
  {
    $content = '{{template name="tpl1"}}' .  
               '{$bar}' .                   
               '{{/template}}' .              
               '{{template name="tpl2"}}' .  
               '{$foo}{$hey}' .                   
               '{{/template}}' .
               '<?php $t=2;?>'.
               '{{apply template="tpl{$t}" hey="$#hey" foo="$#foo"/}}' .
               '<?php $t=1;?>'.
               '{{apply template="tpl{$t}" bar="$#bar"/}}' 
               ;

    $tpl = $this->_createTemplate($content, 'tree.html');

    $macro = $this->_createMacro($tpl);
    $macro->set('hey', 'HEY');
    $macro->set('bar', 'BAR');
    $macro->set('foo', 'FOO');

    $out = $macro->render();
    $this->assertEqual($out, 'FOOHEYBAR');
  }

}

