<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
 
class lmbMacroFormFieldErrorsTagTest extends lmbBaseMacroTest
{
  function testSimpleCase()
  {
    $template = '{{form name="my_form"}}'.
                '{{form:field_errors to="$form_errors"/}}'.
                '{{list using="$form_errors" as="$item"}}{{list:item}}{$item.id}-{$item.message}|{{/list:item}}{{/list}}'.
                '{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $error_list = new lmbMacroFormErrorList();
    $error_list->addError('Error1', array('first' => 'field1', 'second' => 'field2'));
    $error_list->addError('Error2', array('first' => 'field1'));
    
    $page->set('form_my_form_error_list', $error_list);
 
    $out = $page->render();
    $this->assertEqual($out, '<form name="my_form">field1-Error1|field2-Error1|field1-Error2|</form>');
  }
  
  function testGetErrorsForParticularField()
  {
    $template = '{{form name="my_form"}}'.
                '{{form:field_errors to="$form_errors" for="field2"/}}'.
                '{{list using="$form_errors" as="$item"}}{{list:item}}{$item.id}-{$item.message}|{{/list:item}}{{/list}}'.
                '{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $error_list = new lmbMacroFormErrorList();
    $error_list->addError('Error1', array('first' => 'field1', 'second' => 'field2'));
    $error_list->addError('Error2', array('first' => 'field1'));
    
    $page->set('form_my_form_error_list', $error_list);
 
    $out = $page->render();
    $this->assertEqual($out, '<form name="my_form">field2-Error1|</form>');
  }  
}
