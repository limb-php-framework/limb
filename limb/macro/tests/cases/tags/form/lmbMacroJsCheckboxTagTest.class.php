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

    $html = new SimpleXMLElement('<foo>'.$page->render().'</foo>');

    $this->assertEqual($html->input[0]['type'], 'checkbox');

    $this->assertEqual($html->input[1]['type'], 'hidden');
    $this->assertEqual($html->input[1]['name'], 'my_checkbox');
    $this->assertEqual($html->input[1]['value'], '0');
  }

  function testRenderHiddenWithCheckedCheckbox()
  {
    $template = '{{js_checkbox name="my_checkbox" value="$#var" checked="checked"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('var', 1);

    $html = new SimpleXMLElement('<foo>'.$page->render().'</foo>');

    $this->assertEqual($html->input[1]['type'], 'hidden');
    $this->assertEqual($html->input[1]['value'], 1);
  }

  function testChecked_With_CheckedValueAttribute()
  {
    $template = '{{js_checkbox name="my_checkbox" checked_value="$#var" checked="checked"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('var', 1);

    $html = new SimpleXMLElement('<foo>'.$page->render().'</foo>');

    $this->assertEqual($html->input[0]['checked'], 'checked');
    $this->assertEqual($html->input[1]['value'], 1);
  }

  function testNotChecked_With_CheckedValueAttribute_And_ValueAttribute()
  {
    $template = '{{js_checkbox name="my_checkbox" checked_value="$#var" value="1" checked="checked"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('var', 2);

    $html = new SimpleXMLElement('<foo>'.$page->render().'</foo>');

    $this->assertEqual($html->input[0]['type'], 'checkbox');
    $this->assertEqual($html->input[0]['value'], 1);

    $this->assertEqual($html->input[1]['type'], 'hidden');
    $this->assertEqual($html->input[1]['value'], 0);
  }

  function testIdConformsW3C()
  {
    $template = '{{js_checkbox name="my_checkbox" value="$#var" checked="checked"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('var', 1);

    $html = new SimpleXMLElement('<foo>'.$page->render().'</foo>');
    $error_message = 'Id must start from letter, that must be followed by letters, digits, underscores, colons and dots';
    $this->assertPattern('~[a-z][a-z\d_:.]~i', $html->input[1]['id'], $error_message);
  }
}

