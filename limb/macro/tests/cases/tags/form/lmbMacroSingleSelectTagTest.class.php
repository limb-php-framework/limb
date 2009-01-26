<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroSingleSelectTagTest extends lmbBaseMacroTest
{
  function testRenderOptionsInSimplestCase()
  {
    $template = '{{select name="my_select" options="$#options" /}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('options', array('red', 'green', 'blue'));
    
    $expected = '<select name="my_select"><option value="0">red</option><option value="1">green</option><option value="2">blue</option></select>';
    $this->assertEqual($page->render(), $expected);
  }

  function testWithSelectedOption()
  {
    $template = '{{select name="my_select" options="$#options" value="$#selected_value"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('options', array('ff0000' => 'red', '00ff00' => 'green'));
    $page->set('selected_value', '00ff00');
    
    $expected = '<select name="my_select"><option value="ff0000">red</option><option value="00ff00" selected="selected">green</option></select>';
    $this->assertEqual($page->render(), $expected);
  }

  function testSpecialCharsInOptions()
  {
    $template = '{{select name="my_select" options="$#options" value="$#selected_value"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('options', array('>>' => '<<', '"' => '""'));
    $page->set('selected_value', '"');
    
    $expected = '<select name="my_select"><option value="&gt;&gt;">&lt;&lt;</option><option value="&quot;" selected="selected">&quot;&quot;</option></select>';
    $this->assertEqual($page->render(), $expected);
  }
  
  function testWithSelectedOption_ByArrayFieldInValue()
  {
    $template = '{{select name="my_select" options="$#options" value="$#selected_value_object" value_field="my_color"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('options', array('ff0000' => 'red', '00ff00' => 'green'));
    $page->set('selected_value_object', new lmbObject(array('my_color' => '00ff00', 'id' => 'ff0000')));
    
    $expected = '<select name="my_select"><option value="ff0000">red</option><option value="00ff00" selected="selected">green</option></select>';
    $this->assertEqual($page->render(), $expected);
  }

  function testWithSelectedOption_ByFieldInFormDatasource()
  {
    $template = '{{form name="my_form" from="$#form_data"}}{{select name="my_select" options="$#options"/}}{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('options', array('ff0000' => 'red', '00ff00' => 'green'));
    $page->set('form_data', array('my_select' => '00ff00'));
    
    $expected = '<form name="my_form">'.
                '<select name="my_select">'.
                '<option value="ff0000">red</option><option value="00ff00" selected="selected">green</option>'.
                '</select>'.
                '</form>';
    $this->assertEqual($page->render(), $expected);
  }

  function testSelectUseChildOptionsList_WithDefaultSelectedOption()
  {
    $template = '{{select name="my_select"}}'.
                '{{option value="1"}}test1{{/option}}'.
                '{{option value="2" selected="selected"}}test2{{/option}}'.
                '{{/select}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');

    $expected = '<select name="my_select">'.
                '<option value="1">test1</option>'.
                '<option value="2" selected="selected">test2</option>'.
                '</select>';
    $this->assertEqual($page->render(), $expected);
  }

  function testMergeOptionsListFromTagContentWithOtherOptions()
  {
    $template = '{{select name="my_select" options="$#options"}}'.
                '{{option value="1" prepend="true"}}test1{{/option}}'.
                '{{option value="4" selected="selected"}}test4{{/option}}'.
                '{{/select}}';
                
    $page = $this->_createMacroTemplate($template, 'tpl.html');

    $page->set('options', array('2' => 'test2', '3' => 'test3'));

    $expected = '<select name="my_select">'.
                '<option value="1">test1</option>'.
                '<option value="2">test2</option>'.
                '<option value="3">test3</option>'.
                '<option value="4" selected="selected">test4</option>'.
                '</select>';
    
    $this->assertEqual($page->render(), $expected);
  }
}

