<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroSingleSelectWidgetTest extends lmbBaseMacroTest
{
  function testGetValue_ReturnScalarValue_From_ValueAttribute()
  {
    $widget = new lmbMacroSingleSelectWidget('my_select');
    $widget->setAttribute('value', 10);
    $this->assertEqual($widget->getValue(), 10);
  }
  
  function testGetValue_ReturnScalarValue_From_FormDatasource()
  {
    $form = new lmbMacroFormWidget('my_form');
    $form->setDatasource(array('my_select' => 10));
    
    $widget = new lmbMacroSingleSelectWidget('my_select');
    $widget->setForm($form);
    
    $this->assertEqual($widget->getValue(), 10);
  }
  
  function testGetValue_ReturnDefaultValue()
  {
    $widget = new lmbMacroSingleSelectWidget('my_select');
    $widget->addToDefaultSelection(10);
    $widget->setAttribute('value', null);
    
    $this->assertEqual($widget->getValue(), 10);
  }
  
  function testGetValue_ReturnValueField_If_ActualValueIsArray()
  {
    $form = new lmbMacroFormWidget('my_form');
    $form->setDatasource(array('my_select' => array('id' => 20, 'my_id' => 10)));
    
    $widget = new lmbMacroSingleSelectWidget('my_select');
    $widget->setAttribute('value_field', 'my_id');
    $widget->setForm($form);
    
    $this->assertEqual($widget->getValue(), 10);
  }
  
  function testGetValue_ReturnDefaultValueField_If_ActualValueIsArray()
  {
    $form = new lmbMacroFormWidget('my_form');
    $form->setDatasource(array('my_select' => array('id' => 20, 'name' => 'whatever')));
    
    $widget = new lmbMacroSingleSelectWidget('my_select');
    $widget->setForm($form);
    
    $this->assertEqual($widget->getValue(), 20);
  }

  function testGetValue_ReturnValueField_If_ActualValueIsObject_With_ArrayAccess()
  {
    $form = new lmbMacroFormWidget('my_form');
    $form->setDatasource(array('my_select' => new lmbSet(array('id' => 20, 'my_id' => 10))));
    
    $widget = new lmbMacroSingleSelectWidget('my_select');
    $widget->setAttribute('value_field', 'my_id');
    $widget->setForm($form);
    
    $this->assertEqual($widget->getValue(), 10);
  }  
  
  function testSetGetAppendPrependOptions_ForSimpleArrays()
  {
    $widget = new lmbMacroSingleSelectWidget('my_select');
    $widget->setOptions(array('red', 'green'));
    $widget->addToOptions('blue');
    $widget->prependToOptions('black');
    $this->assertEqual($widget->getOptions(), array('black', 'red', 'green', 'blue'));
  }

  function testSetGetAppendPrependOptions_ForAssociativeArrays()
  {
    $widget = new lmbMacroSingleSelectWidget('my_select');
    $widget->setOptions(array('ff0000' => 'red', '00ff00' => 'green'));
    $widget->addToOptions('0000ff', 'blue');
    $widget->prependToOptions('000000', 'black');
    $this->assertEqual($widget->getOptions(), array('ff0000' => 'red', '00ff00' => 'green', '0000ff' => 'blue', '000000' => 'black'));
  }
}

