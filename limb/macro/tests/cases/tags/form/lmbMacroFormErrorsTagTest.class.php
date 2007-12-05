<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
 
class lmbMacroFormErrorsTagTest extends lmbBaseMacroTest
{
  function testSimpleCase()
  {
    $template = '{{form name="my_form"}}'.
                '{{form:errors to="$form_errors"/}}'.
                '{{list using="$form_errors" as="$item"}}{{list:item}}{$item.message}{{/list:item}}{{/list}}'.
                '{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $error_list = new lmbMacroFormErrorList();
    $error_list->addError('Error in title field');
    $error_list->addError('Error in name field');
    
    $page->set('form_my_form_error_list', $error_list);
 
    $out = $page->render();
    $this->assertEqual($out, '<form name="my_form">Error in title fieldError in name field</form>');
  }

  function testFormKnowsAboutChildFields()
  {
    $template = '{{form name="my_form"}}'.
                '{{form:errors to="$form_errors"/}}'.
                '{{list using="$form_errors" as="$item"}}{{list:item}}{$item.message}{{/list:item}}{{/list}}'.
                '{{input type="text" name="text_field" title="My text field"/}}'.
                '{{select name="select_field" title="My select field"/}}'.
                '{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $error_list = new lmbMacroFormErrorList();
    $error_list->addError('Error in {field}', array('field' => 'text_field'));
    $error_list->addError('Other error in {field}', array('field' => 'select_field'));
    
    $page->set('form_my_form_error_list', $error_list);
 
    $expected = '<form name="my_form">Error in My text fieldOther error in My select field'.
                '<input type="text" name="text_field" title="My text field" value="" />'.
                '<select name="select_field" title="My select field"></select>'.
                '</form>';
    $this->assertEqual($page->render(), $expected);
  }   
  
}
