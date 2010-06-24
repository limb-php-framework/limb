<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
    
    $expected = '<form id="my_form"><input type="checkbox" name="my_input" value="foo" checked="checked" /></form>';
    $this->assertEqual($page->render(), $expected);
  }

  function testRemoveCheckedIfNotChecked()
  {
    $template = '{{form id="my_form"}}'.
                '{{input type="checkbox" name="my_input" value="bar" checked="checked"}}' .
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

    $expected = '<input type="checkbox" id="test" name="my_input" checked="checked" />';
    $this->assertEqual($page->render(), $expected);
  }

  function testNotChecked_With_CheckedValueAttribute_And_ValueAttribute()
  {
    $template = '{{input type="checkbox" id="test" name="my_input" value="1" checked_value="{$#bar}" checked="checked" /}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 

    $page->set('bar', '2');

    $expected = '<input type="checkbox" id="test" name="my_input" value="1" />';
    $this->assertEqual($page->render(), $expected);
  }
  
function testNotCheckedInputs_When_FirstInputChecked()
  {
    $template = '{{form id="my_form"}}'.
    			'<?php $values = array(3 => "aa", 4 => "bb") ?>'. 
    			'<?php foreach($values as $id => $v) : ?>
					{{input type="checkbox" id="test_{$id}" name="test_{$id}" value="{$v}" /}}
				<?php endforeach; ?>'.
    			'{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 

    $page->set('form_my_form_datasource', array("test_3" => "aa"));

    $expected = '<form id="my_form"><input type="checkbox" id="test_3" name="test_3" value="aa" checked="checked" /><input type="checkbox" id="test_4" name="test_4" value="bb" /></form>';

    $this->assertEqual(preg_match('~\s{2,}~', '', $page->render()), $expected);
  }
}

