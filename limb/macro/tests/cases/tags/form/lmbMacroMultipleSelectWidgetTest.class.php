<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroMultipleSelectWidgetTest extends lmbBaseMacroTest
{
  function testGetValue_ReturnValue_From_ValueAttribute()
  {
    $widget = new lmbMacroMultipleSelectWidget('my_select');
    $widget->setAttribute('value', array(10));
    $this->assertEqual($widget->getValue(), array(10));
  }
  
  function testGetValue_ReturnValue_From_FormDatasource()
  {
    $form = new lmbMacroFormWidget('my_form');
    $form->setDatasource(array('my_select' => array(10)));
    
    $widget = new lmbMacroMultipleSelectWidget('my_select');
    $widget->setForm($form);
    
    $this->assertEqual($widget->getValue(), array(10));
  }
  
  function testGetValue_ReturnDefaultSelection_IfValueInValueAttributeIsScalar()
  {
    $widget = new lmbMacroMultipleSelectWidget('my_select');
    $widget->addToDefaultSelection(20);
    $widget->setAttribute('value', 10); // scalar not array
    $this->assertEqual($widget->getValue(), array(20));
  }
  
  function testGetValue_ReturnDefaultSelection_IfValueInFormDatasourceIsScalar()
  {
    $form = new lmbMacroFormWidget('my_form');
    $form->setDatasource(array('my_select' => 10)); // scalar not array

    $widget = new lmbMacroMultipleSelectWidget('my_select');
    $widget->addToDefaultSelection(20);
    $widget->setForm($form);
    $this->assertEqual($widget->getValue(), array(20));
  }
  
  function testGetValue_ReturnDefaultValue()
  {
    $widget = new lmbMacroMultipleSelectWidget('my_select');
    $widget->addToDefaultSelection(10);
    $widget->addToDefaultSelection(20);
    $widget->setAttribute('value', null);
    
    $this->assertEqual($widget->getValue(), array(10, 20));
  }
  
  function testGetValue_ReturnValueField_If_ActualValueContainArrays()
  {
    $form = new lmbMacroFormWidget('my_form');
    $form->setDatasource(array('my_select' => array(array('id' => 10, 'my_id' => 50), array('id' => 20, 'my_id' => 100))));
    
    $widget = new lmbMacroMultipleSelectWidget('my_select');
    $widget->setAttribute('value_field', 'my_id');
    $widget->setForm($form);
    
    $this->assertEqual($widget->getValue(), array(50, 100));
  }
  
  function testGetValue_ReturnDefaultValueFieldValues_If_ActualValueIsArray()
  {
    $form = new lmbMacroFormWidget('my_form');
    $form->setDatasource(array('my_select' => array(array('id' => 10, 'name' => 'Ivan'), array('id' => 20, 'name' => 'Peter'))));
    
    $widget = new lmbMacroMultipleSelectWidget('my_select');
    $widget->setForm($form);
    
    $this->assertEqual($widget->getValue(), array(10, 20));
  }

  function testGetValue_ReturnValueFieldValues_If_ActualValueIsIterator()
  {
    $form = new lmbMacroFormWidget('my_form');
    $form->setDatasource(array('my_select' => new ArrayIterator(array(array('id' => 10, 'my_id' => 50), 
                                                                      array('id' => 20, 'my_id' => 100)))));
    
    $widget = new lmbMacroMultipleSelectWidget('my_select');
    $widget->setAttribute('value_field', 'my_id');
    $widget->setForm($form);
    
    $this->assertEqual($widget->getValue(), array(50, 100));
  }
  
  function testGetValue_ReturnValueFieldValues_If_ActualValueIsIterator_WithObjectsOfArrayAccessInterface()
  {
    $form = new lmbMacroFormWidget('my_form');
    $form->setDatasource(array('my_select' => new ArrayIterator(array(new ArrayObject(array('id' => 10, 'my_id' => 50)), 
                                                                      new ArrayObject(array('id' => 20, 'my_id' => 100))))));
    
    $widget = new lmbMacroMultipleSelectWidget('my_select');
    $widget->setAttribute('value_field', 'my_id');
    $widget->setForm($form);
    
    $this->assertEqual($widget->getValue(), array(50, 100));
  }  
}

