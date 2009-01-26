<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

Mock :: generate('lmbMacroFormWidget', 'MockMacroFormWidget');
Mock :: generate('lmbMacroFormElementWidget', 'MockMacroFormFieldWidget');

class lmbMacroFormErrorListTest extends lmbBaseMacroTest
{
  function testAddSimpleError()
  {
    $error_list = new lmbMacroFormErrorList();
    $error_list->addError($message ='Error in some field');
    
    $this->assertErrorsInList($error_list, array(array('message' => $message,
                                                       'fields' => array(),
                                                        'values' => array())));
  }
  
  function testAddErrorWithFieldPlaceholder()
  {
    $error_list = new lmbMacroFormErrorList();
    $error_list->addError('Error in {Field}', array('Field' => 'title'));
    
    $this->assertErrorsInList($error_list, array(array('message' => 'Error in "title"',
                                                        'fields' => array('Field' => 'title'),
                                                        'values' => array())));
  }

  function testErrorsIfFormWidgetIsSet()
  {
    $form = new MockMacroFormWidget();
    $field1 = new MockMacroFormFieldWidget();
    $field2 = new MockMacroFormFieldWidget();
    
    $error_list = new lmbMacroFormErrorList();
    $error_list->addError('Error in {Field}', array('Field' => 'title'));
    $error_list->addError('Other error in {Field}', array('Field' => 'name'));
    
    $form->setReturnValue('getChild', $field1, array('title'));
    $form->setReturnValue('getChild', $field2, array('name'));

    $field1->setReturnValue('getDisplayName', 'TitleField');
    $field2->setReturnValue('getDisplayName', 'NameField');
    
    $error_list->setForm($form);
    
    $this->assertErrorsInList($error_list, array(array('message' => 'Error in "TitleField"',
                                                        'fields' => array('Field' => 'title'),
                                                        'values' => array()),
                                                 array('message' => 'Other error in "NameField"',
                                                        'fields' => array('Field' => 'name'),
                                                        'values' => array())));
  }
  
  function testAddErrorWithFieldAndValuePlaceholders()
  {
    $error_list = new lmbMacroFormErrorList();
    $error_list->addError('{Field} should be between {value_min} and {value_max}', 
                          array('Field' => 'title'),
                          array('value_min' => 10, 'value_max' => 100));
    
    $this->assertErrorsInList($error_list, array(array('message' => '"title" should be between 10 and 100',
                                                       'fields' => array('Field' => 'title'),
                                                       'values' => array('value_min' => 10, 'value_max' => 100))));
  }
  
  
  function assertErrorsInList($error_list, $etalon)
  {
    $errors = array();
    foreach($error_list as $error)
      $errors[] = $error;

    $this->assertEqual($errors, $etalon);
  }
}

