<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroFormTagTest extends lmbBaseMacroTest
{
  function testSimpleForm()
  {
    $template = '{{form name="my_form"}}Hi{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
 
    $out = $page->render();
    $this->assertEqual($out, '<form name="my_form">Hi</form>');
  }   
  
  function testFormGeneratesWidgetVar()
  {
    $template = '{{form name="my_form"}}<?php if(isset($this->form_my_form)) echo 1111; ?>{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
 
    $out = $page->render();
    $this->assertEqual($out, '<form name="my_form">1111</form>');
  }
  
  function testFormTakesErrorListFromTemplateVariable()
  {
    $template = '{{form name="my_form"}}'.
                '<?php $error_list = $this->form_my_form->getErrorList(); '.
                'if($error_list[0]["message"] == $this->error_message) echo 1111;'.
                '?>'.
                '{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $error_list = new lmbMacroFormErrorList();
    $error_list->addError($message = 'Any error message');
    
    $page->set('error_message', $message);
    $page->set('form_my_form_error_list', $error_list);
 
    $out = $page->render();
    $this->assertEqual($out, '<form name="my_form">1111</form>');
  }
  
  function testFormTakesDatasourceFromTemplateVariable()
  {
    $template = '{{form name="my_form"}}'.
                '<?php $ds = $this->form_my_form->getDatasource(); '.
                'if(isset($ds["value"])) echo $ds["value"];'.
                '?>'.
                '{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $page->set('form_my_form_datasource', array("value" => 1111));
 
    $out = $page->render();
    $this->assertEqual($out, '<form name="my_form">1111</form>');
  }
  
  function testFormTakesDatasourceByFromAttribute()
  {
    $template = '{{form name="my_form" from="$#form_data"}}'.
                '<?php $ds = $this->form_my_form->getDatasource(); '.
                ' echo $ds["value"];'.
                '?>'.
                '{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $page->set('form_data', array('value' => 1111));
 
    $out = $page->render();
    $this->assertEqual($out, '<form name="my_form">1111</form>');
  }
}
