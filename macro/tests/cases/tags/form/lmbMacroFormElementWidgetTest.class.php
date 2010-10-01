<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroFormElementWidgetTest extends lmbBaseMacroTagTest
{
  function testGetValue_FromValueAttribute()
  {
    $form = new lmbMacroFormWidget('my_id');
    $widget = new lmbMacroFormElementWidget('any_field');
    $widget->setAttribute('value', 10);
    $widget->setForm($form);

    $this->assertEqual($widget->getValue(), 10);
  }

  function testGetValue_FromFormDatasource()
  {
    $form = new lmbMacroFormWidget('my_id');
    $form->setDatasource(array('any_field' => 10));
    $widget = new lmbMacroFormElementWidget('any_field');
    $widget->setForm($form);

    $this->assertEqual($widget->getValue(), 10);
  }

  function testGetValue_FromFormDatasource_ByNameAttribute()
  {
    $form = new lmbMacroFormWidget('my_id');
    $form->setDatasource(array('any_field' => 'wrong_value', 'field_name' => 10));
    $widget = new lmbMacroFormElementWidget('any_field');
    $widget->setAttribute('name', 'field_name');
    $widget->setForm($form);

    $this->assertEqual($widget->getValue(), 10);
  }

  function testGetDisplayName_ReturnIdByDefault()
  {
    $widget = new lmbMacroFormElementWidget('any_field');
    $this->assertEqual($widget->getDisplayname(), 'any_field');
  }

  function testGetDisplayName_ReturnTitleAttribute()
  {
    $widget = new lmbMacroFormElementWidget('any_field');
    $widget->setAttribute('title', 'My Field');
    $this->assertEqual($widget->getDisplayname(), 'My Field');
  }

  function testGetDisplayName_ReturnAltAttribute()
  {
    $widget = new lmbMacroFormElementWidget('any_field');
    $widget->setAttribute('alt', 'My Super Field');
    $this->assertEqual($widget->getDisplayname(), 'My Super Field');
  }

  function testSetErrorState_SetErrorStateClassAndStyle()
  {
    $widget = new lmbMacroFormElementWidget('any_field');
    $widget->setAttribute('error_style', 'my_error_style');
    $widget->setAttribute('error_class', 'my_error_class');
    $widget->setErrorState(true);

    $this->assertEqual($widget->getAttribute('class'), 'my_error_class');
    $this->assertEqual($widget->getAttribute('style'), 'my_error_style');
  }

  function testGetName_ReturnIdByDefault()
  {
  	$widget = new lmbMacroFormElementWidget('testGetName_ReturnIdByDefault');
    $this->assertEqual($widget->getName(), 'testGetName_ReturnIdByDefault');
  }

  function testGetName_ReturnNameAttribute()
  {
    $widget = new lmbMacroFormElementWidget('testGetName_ReturnNameAttribute');
    $widget->setAttribute('name', 'My_Super_Field');
    $this->assertEqual($widget->getName(), 'My_Super_Field');
  }

  function testRenderAttributes_NameAttribute()
  {
  	$widget = new lmbMacroFormElementWidget('testRenderAttributes_NameAttribute');
    $this->assertEqual(null, $this->_getRenderedWidgetAttributeValue($widget, 'name'));

    $widget->setAttribute('id', 'my_id');
    $this->assertEqual('my_id', $this->_getRenderedWidgetAttributeValue($widget, 'name'));

    $widget->setAttribute('name', 'my_name');
    $this->assertEqual('my_name', $this->_getRenderedWidgetAttributeValue($widget, 'name'));
  }
}
