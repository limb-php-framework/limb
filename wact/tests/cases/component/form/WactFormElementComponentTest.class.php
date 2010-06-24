<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once 'limb/wact/src/components/form/form.inc.php';

class WactFormElementComponentTest extends WactTemplateTestCase
{
  protected $element;
  protected $form;

  function setUp()
  {
    parent :: setUp();

    $this->form = new WactFormComponent('test_form');
    $this->element = new WactFormElementComponent('my_element');
    $this->form->addChild($this->element);
  }

  function testGetNameReturnsIdIsNoNameAttribute()
  {
    $this->assertEqual($this->element->getName(), 'my_element');
  }

  function testGetNameReturnsNameAttributeIfExists()
  {
    $this->element->setAttribute('name', 'custom_name');
    $this->assertEqual($this->element->getName(), 'custom_name');
  }

  function testGetValueFromForm()
  {
    $this->form->set('my_element', 'whatever');
    $this->assertEqual($this->element->getValue(), 'whatever');
  }

  function testGetGivenValueOverridesFormValue()
  {
    $this->form->set('my_element', 'whatever');
    $this->element->setGivenValue('other value');
    $this->assertEqual($this->element->getValue(), 'other value');
  }

  function testGetDisplayName()
  {
    $form_element = new WactFormElementComponent('my_id');
    $this->assertEqual($form_element->getDisplayName(),'');

    $form_element = new WactFormElementComponent('my_id');
    $form_element->displayname = 'a';
    $form_element->setAttribute('title','b');
    $form_element->setAttribute('alt','c');
    $form_element->setAttribute('name','d');
    $this->assertEqual($form_element->getDisplayName(),'a');

    $form_element = new WactFormElementComponent('my_id');
    $form_element->setAttribute('title','b');
    $form_element->setAttribute('alt','c');
    $form_element->setAttribute('name','d');
    $this->assertEqual($form_element->getDisplayName(),'b');

    $form_element = new WactFormElementComponent('my_id');
    $form_element->setAttribute('alt','c');
    $form_element->setAttribute('name','d');
    $this->assertEqual($form_element->getDisplayName(),'c');

    $form_element = new WactFormElementComponent('my_id');
    $form_element->setAttribute('name','foo_Bar');
    $this->assertEqual($form_element->getDisplayName(),'foo Bar');
  }

  function testHasErrorsNone()
  {
    $form_element = new WactFormElementComponent('my_id');
    $this->assertFalse($form_element->hasErrors());
  }

  function testHasErrors()
  {
    $form_element = new WactFormElementComponent('my_id');
    $form_element->errorclass = 'ErrorClass';
    $form_element->errorstyle = 'ErrorStyle';

    $form_element->setError();

    $this->assertTrue($form_element->hasErrors());
    $this->assertEqual($form_element->getAttribute('class'),'ErrorClass');
    $this->assertEqual($form_element->getAttribute('style'),'ErrorStyle');
  }
}

