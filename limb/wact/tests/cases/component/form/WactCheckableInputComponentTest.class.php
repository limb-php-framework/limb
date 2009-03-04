<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once 'limb/wact/src/components/form/form.inc.php';

class WactCheckableInputComponentTest extends WactTemplateTestCase
{
  protected $checkbox;
  protected $form;

  function setUp()
  {
    parent :: setUp();

    $this->form = new WactFormComponent('test_form');
    $this->checkbox = new WactCheckableInputComponent('my_checkbox');
    $this->form->addChild($this->checkbox);
  }

  function testGetValue()
  {
    $this->form->set('my_checkbox', 'whatever');
    $this->assertEqual($this->checkbox->getValue(), 'whatever');
  }

  function testCheckedIfNotValueAndCheckedAttribute()
  {
    $this->checkbox->setAttribute('checked', true);
    $this->assertTrue($this->checkbox->isChecked());
  }

  function testCheckedIfNoValueAttributeAndFormValue()
  {
    $this->form->set('my_checkbox', 3);

    $this->assertTrue($this->checkbox->isChecked());
  }

  function testCheckedIfValueAttributeEqualFormValue()
  {
    $this->form->set('my_checkbox', 3);
    $this->checkbox->setAttribute('value', 3);

    $this->assertTrue($this->checkbox->isChecked());
  }

  function testNotCheckedIfValueAttributeNotEqualFormValue()
  {
    $this->form->set('my_checkbox', 3);
    $this->checkbox->setAttribute('value', 2);

    $this->assertFalse($this->checkbox->isChecked());
  }

  function testCheckedIfValueAttributeInFormValueArray()
  {
    $this->form->set('my_checkbox', array(1,3));
    $this->checkbox->setAttribute('value', 3);

    $this->assertTrue($this->checkbox->isChecked());
  }

  function testNotCheckedIfValueAttributeNotInFormValueArray()
  {
    $this->form->set('my_checkbox', array(1,3));
    $this->checkbox->setAttribute('value', 2);

    $this->assertFalse($this->checkbox->isChecked());
  }
  
  function testNotCheckedIfValueAttributeIsZero()
  {
    $this->form->set('my_checkbox', 3);
    $this->checkbox->setAttribute('value', 0);

    $this->assertFalse($this->checkbox->isChecked());
  }  
  
  function testCheckedIfValueAndValueAttributeAreZero()
  {
    $this->form->set('my_checkbox', 0);
    $this->checkbox->setAttribute('value', 0);

    $this->assertTrue($this->checkbox->isChecked());
  }    
}

