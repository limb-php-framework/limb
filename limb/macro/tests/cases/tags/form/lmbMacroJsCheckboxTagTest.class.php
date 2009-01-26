<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroJsCheckboxTagTest extends lmbBaseMacroTest
{
  function testRenderHiddenWithCheckbox()
  {
    $template = '{{js_checkbox name="my_checkbox" value="$#var"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 
    $page->set('var', 1);     
    
    $expected = '<input type="checkbox" value="1" onchange="this.form.elements[\'_my_checkbox\'].value = 1*this.checked" />'.
                '<input type="hidden" id="_my_checkbox" name="my_checkbox" value="0" />';
    $this->assertEqual($page->render(), $expected);
  }

  function testRenderHiddenWithCheckedCheckbox()
  {
    $template = '{{js_checkbox name="my_checkbox" value="$#var" checked="checked"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 
    $page->set('var', 1);     
    
    $expected = '<input checked="checked" type="checkbox" value="1" onchange="this.form.elements[\'_my_checkbox\'].value = 1*this.checked" />'.
                '<input type="hidden" id="_my_checkbox" name="my_checkbox" value="1" />';
    $this->assertEqual($page->render(), $expected);
  }
  
  function testChecked_With_CheckedValueAttribute()
  {
    $template = '{{js_checkbox name="my_checkbox" checked_value="$#var" checked="checked"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 
    $page->set('var', 1);     
    
    $expected = '<input checked="checked" type="checkbox" onchange="this.form.elements[\'_my_checkbox\'].value = 1*this.checked" />'.
                '<input type="hidden" id="_my_checkbox" name="my_checkbox" value="1" />';
    $this->assertEqual($page->render(), $expected);
    
  }

  function testNotChecked_With_CheckedValueAttribute_And_ValueAttribute()
  {
    $template = '{{js_checkbox name="my_checkbox" checked_value="$#var" value="1" checked="checked"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 
    $page->set('var', 2);     
    
    $expected = '<input value="1" type="checkbox" onchange="this.form.elements[\'_my_checkbox\'].value = 1*this.checked" />'.
                '<input type="hidden" id="_my_checkbox" name="my_checkbox" value="0" />';
    $this->assertEqual($page->render(), $expected);
  }
}

