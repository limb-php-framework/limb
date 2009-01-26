<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroMultipleSelectTagTest extends lmbBaseMacroTest
{
  function testRenderOptionsInSimplestCase()
  {
    $template = '{{select id="my_select" options="$#options" multiple="true"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('options', array('red', 'green', 'blue'));
    
    $expected = '<select id="my_select" multiple="true" name="my_select[]"><option value="0">red</option><option value="1">green</option><option value="2">blue</option></select>';
    $this->assertEqual($page->render(), $expected);
  }

  function testWithSelectedOption()
  {
    $template = '{{select id="my_select" name="my_select[]" multiple="true" options="$#options" value="$#selected_value"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('options', array('ff0000' => 'red', '00ff00' => 'green', '0000ff' => 'blue'));
    $page->set('selected_value', array('00ff00', '0000ff'));
    
    $expected = '<select id="my_select" name="my_select[]" multiple="true">'.
                '<option value="ff0000">red</option><option value="00ff00" selected="selected">green</option><option value="0000ff" selected="selected">blue</option>'.
                '</select>';
    $this->assertEqual($page->render(), $expected);
  }

  function testWithSelectedOption_ByArrayFieldInValue()
  {
    $template = '{{select id="my_select" name="my_select[]" multiple="true" options="$#options" value="$#selected_value_object" value_field="my_color"/}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('options', array('ff0000' => 'red', '00ff00' => 'green', '0000ff' => 'blue'));
    $page->set('selected_value_object', new ArrayIterator(array(array('my_color' => '00ff00', 'id' => 'whatever'),
                                                                array('my_color' => '0000ff', 'id' => 'whatever'))));
    
    $expected = '<select id="my_select" name="my_select[]" multiple="true">'.
                '<option value="ff0000">red</option><option value="00ff00" selected="selected">green</option><option value="0000ff" selected="selected">blue</option>'.
                '</select>';
    $this->assertEqual($page->render(), $expected);
  }

  function testWithSelectedOption_ByFieldInFormDatasource()
  {
    $template = '{{form name="my_form" from="$#form_data"}}{{select id="my_select" name="my_select[]" multiple="true" options="$#options"/}}{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('options', array('ff0000' => 'red', '00ff00' => 'green', '0000ff' => 'blue'));
    $page->set('form_data', array('my_select' => array('00ff00', '0000ff')));
    
    $expected = '<form name="my_form">'.
                '<select id="my_select" name="my_select[]" multiple="true">'.
                '<option value="ff0000">red</option><option value="00ff00" selected="selected">green</option><option value="0000ff" selected="selected">blue</option>'.
                '</select>'.
                '</form>';
    $this->assertEqual($page->render(), $expected);
  }
}

