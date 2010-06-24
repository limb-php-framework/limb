<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */


class WactFormFieldErrorsTagTest extends WactTemplateTestCase
{
  function testProperNesting()
  {
    $template = '<form:field_errors><list:list id="errors"></list></form:field_errors>';

    $this->registerTestingTemplate('/form/form_field_errors/proper_nesting.html', $template);

    try
    {
      $page = $this->initTemplate('/form/form_field_errors/proper_nesting.html');
      $this->assertTrue(false);
    }
    catch(WactException $e){}
  }

  function testNestingListTagIsRequired()
  {
    $template = '<form runat="server"><form:field_errors></form:field_errors></form>';

    $this->registerTestingTemplate('/form/form_field_errors/nesting_list_tag_required.html', $template);

    try
    {
      $page = $this->initTemplate('/form/form_field_errors/nesting_list_tag_required.html');
      $this->assertTrue(false);
    }
    catch(WactException $e){}
  }

  function testErrorsPassedToListListTag()
  {
    $template = '<form id="my_form" runat="server"><form:field_errors>'.
                '<list:list id="errors"><list:item>{$id}-{$message}|</list:item></list:list>'.
                '</form:field_errors></form>';

    $this->registerTestingTemplate('/form/form_field_errors/passed_to_list.html', $template);

    $page = $this->initTemplate('/form/form_field_errors/passed_to_list.html');

    $error_list = new WactFormErrorList();
    $error_list->addError('Error1 text', array('FIRST' => 'field1', 'SECOND' => 'field2'));
    $error_list->addError('Error2 text', array('FIELD' => 'field1'));

    $form = $page->getChild("my_form");
    $form->setErrors($error_list);

    $this->assertEqual($page->capture(), '<form id="my_form">field1-Error1 text|field2-Error1 text|field1-Error2 text|</form>');
  }

  function testErrorsForSpecifiedFormElement()
  {
    $template = '<form id="my_form" runat="server"><form:field_errors for="field2">'.
                '<list:list id="errors"><list:item>{$message}|</list:item></list:list>'.
                '</form:field_errors></form>';

    $this->registerTestingTemplate('/form/form_field_errors/for_element_passed_to_list.html', $template);

    $page = $this->initTemplate('/form/form_field_errors/for_element_passed_to_list.html');

    $error_list = new WactFormErrorList();
    $error_list->addError('Error1 text', array('FIRST' => 'field1', 'SECOND' => 'field2'));
    $error_list->addError('Error2 text', array('FIELD' => 'field1'));
    $error_list->addError('Error3 text', array('FIELD' => 'field2'));

    $form = $page->getChild("my_form");
    $form->setErrors($error_list);

    $this->assertEqual($page->capture(), '<form id="my_form">Error1 text|Error3 text|</form>');
  }
}

