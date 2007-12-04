<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroInputCheckboxTagTest extends lmbBaseMacroTest
{
  function testIsChecked_If_ValueAttribute_IsEqual_To_FormDatasourceFieldValue()
  {
    $template = '{{form id="my_form"}}'.
                '{{input type="checkbox" name="my_input" value="foo"/}}'.
                '{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 
    $page->set('form_my_form_datasource', array("my_input" => 'foo'));     
    
    $expected = '<form id="my_form"><input type="checkbox" name="my_input" value="foo" checked="true" /></form>';
    $this->assertEqual($page->render(), $expected);
  }

  function testRemoveCheckedIfNotChecked()
  {
    $template = '{{form id="my_form"}}'.
                '{{input type="checkbox" name="my_input" value="bar" checked="true"}}' .
                '{{/form}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html'); 
    $page->set('form_my_form_datasource', array("my_input" => 'foo'));     

    $expected = '<form id="my_form"><input type="checkbox" name="my_input" value="bar" /></form>';
    $this->assertEqual($page->render(), $expected);
  }

  function testIsChecked_With_CheckedValueAttribute()
  {
    $template = '{{input type="checkbox" id="test" name="my_input" checked_value="$#bar" /}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 

    $page->set('bar', '1');

    $expected = '<input type="checkbox" id="test" name="my_input" checked="true" />';
    $this->assertEqual($page->render(), $expected);
  }

  function testNotChecked_With_CheckedValueAttribute_And_ValueAttribute()
  {
    $template = '{{input type="checkbox" id="test" name="my_input" value="1" checked_value="{$#bar}" checked="true" /}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 

    $page->set('bar', '2');

    $expected = '<input type="checkbox" id="test" name="my_input" value="1" />';
    $this->assertEqual($page->render(), $expected);
  }
}

