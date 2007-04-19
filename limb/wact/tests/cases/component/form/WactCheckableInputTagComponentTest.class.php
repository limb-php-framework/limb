<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: inputcheckbox.test.php 5339 2007-03-23 14:12:48Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/components/form/form.inc.php';

class WactCheckableInputTagComponentTest extends WactTemplateTestCase
{
  protected $checkbox;
  protected $form;

  function setUp()
  {
    parent :: setUp();

    $this->form = new WactFormComponent('test_form');
    $this->checkbox = new WactCheckableInputTagComponent('my_checkbox');
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

  function testNotCheckedIfNotValueAndFalseCheckedAttribute()
  {
    $this->checkbox->setAttribute('checked', false);
    $this->assertFalse($this->checkbox->isChecked());
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
}
?>
