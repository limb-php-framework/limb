<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class lmbMacroCheckableInputWidgetTest extends lmbBaseMacroTest
{
  protected $checkbox;
  protected $form;
  protected $datasource;

  function setUp()
  {
    parent :: setUp();

    $this->form = new lmbMacroFormWidget('test_form');
    $this->datasource = new lmbSet();
    $this->form->setDatasource($this->datasource);
    
    $this->checkbox = new lmbMacroCheckableInputWidget('my_checkbox');
    $this->form->addChild($this->checkbox);
    $this->checkbox->setForm($this->form);
  }

  function testGetValue_ByCheckedValueAttribute()
  {
    $this->checkbox->setAttribute('checked_value', 1111);
    $this->assertEqual($this->checkbox->getValue(), 1111);

    // checked_value has bigger priority
    $this->datasource->set('my_checkbox', 'whatever');
    $this->assertEqual($this->checkbox->getValue(), 1111);
  }
  
  function testGetValue_FromFormDatasource()
  {
    $this->datasource->set('my_checkbox', 'whatever');
    $this->assertEqual($this->checkbox->getValue(), 'whatever');
  }

  function testGetValue_FromFormDatasource_ByCleanedNameAttribute()
  {
    $this->checkbox->setAttribute('name', 'items[]');
    $this->datasource->set('items', 'whatever');
    $this->assertEqual($this->checkbox->getValue(), 'whatever');
  }
  
  function testCheckedIfNotValueAndCheckedAttribute()
  {
    $this->checkbox->setAttribute('checked', true);
    $this->assertTrue($this->checkbox->isChecked());
  }

  function testCheckedIfNoValueAttributeAndFormValue()
  {
    $this->datasource->set('my_checkbox', 3);

    $this->assertTrue($this->checkbox->isChecked());
  }

  function testCheckedIfValueAttributeEqualFormValue()
  {
    $this->datasource->set('my_checkbox', 3);
    $this->checkbox->setAttribute('value', 3);

    $this->assertTrue($this->checkbox->isChecked());
  }

  function testNotCheckedIfValueAttributeNotEqualFormValue()
  {
    $this->datasource->set('my_checkbox', 3);
    $this->checkbox->setAttribute('value', 2);

    $this->assertFalse($this->checkbox->isChecked());
  }

  function testCheckedIfValueAttributeInFormValueArray()
  {
    $this->datasource->set('my_checkbox', array(1,3));
    $this->checkbox->setAttribute('value', 3);

    $this->assertTrue($this->checkbox->isChecked());
  }

  function testNotCheckedIfValueAttributeNotInFormValueArray()
  {
    $this->datasource->set('my_checkbox', array(1,3));
    $this->checkbox->setAttribute('value', 2);

    $this->assertFalse($this->checkbox->isChecked());
  }
  
  function testNotCheckedIfValueAttributeIsZero()
  {
    $this->datasource->set('my_checkbox', 3);
    $this->checkbox->setAttribute('value', 0);

    $this->assertFalse($this->checkbox->isChecked());
  }  
  
  function testCheckedIfValueAndValueAttributeAreZero()
  {
    $this->datasource->set('my_checkbox', 0);
    $this->checkbox->setAttribute('value', 0);

    $this->assertTrue($this->checkbox->isChecked());
  }    
}

