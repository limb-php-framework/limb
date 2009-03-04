<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');

class WactInputTagTest extends WactTemplateTestCase
{
  function testInputTypes()
  {
    $tplStart = '<form runat="server"><input id="test" type="';
    $tplEnd = '" runat="server"/></form>';

    $types = array (
        'text' => 'WactInputComponent',
        'password' => 'WactFormElementComponent',
        'checkbox' => 'WactCheckableInputComponent',
        'submit' => 'WactFormElementComponent',
        'radio' => 'WactCheckableInputComponent',
        'reset' => 'WactFormElementComponent',
        'file' => 'WactFileInputComponent',
        'hidden' => 'WactInputComponent',
        'button' => 'WactInputComponent',
    );

    foreach ($types as $type => $component )
    {
      $template = $tplStart.$type.$tplEnd;
      $this->registerTestingTemplate('/tags/form/controls/input/'.$type.'.html', $template);
      $page =  $this->initTemplate('/tags/form/controls/input/'.$type.'.html');
      $Input =  $page->getChild('test');
      $this->assertIsA($Input,$component);
      $this->default_locator->clearTestingTemplates();
    }
  }

  function testUnknownType()
  {
    $template = '<form runat="server"><input id="test" type="unknown" runat="server"/></form>';
    $this->registerTestingTemplate('/tags/form/controls/input/unknown.html', $template);
    try
    {
      $page =  $this->initTemplate('/tags/form/controls/input/unknown.html');
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Unrecognized type attribute for input tag/', $e->getMessage());
    }
  }

  function testGetValueFromFormRegadrlessOfValueAttribute()
  {
    $template ='<form id="testForm" runat="server">'.
                '<input type="text" id="test" name="myInput" value="my_value" runat="server" />'.
               '</form>';
    $this->registerTestingTemplate('/tags/form/input/value_from_form.html', $template);

    $page = $this->initTemplate('/tags/form/input/value_from_form.html');

    $form = $page->getChild('testForm');
    $form->registerDataSource(array('myInput' => 'foo'));

    $expected = '<form id="testForm">'.
                '<input type="text" id="test" name="myInput" value="foo" />'.
                '</form>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testUseGivenValueRegardlessOfFormValue()
  {
    $template ='<form id="testForm" runat="server">'.
               '<input type="text" id="test" name="myInput" runat="server" given_value="{$#bar.var1}" />'.
               '</form>';
    $this->registerTestingTemplate('/tags/form/input/use_given_value.html', $template);

    $page = $this->initTemplate('/tags/form/input/use_given_value.html');
    $page->set('bar', array('var1' => 'other_value'));

    $form = $page->getChild('testForm');
    $form->registerDataSource(array('myInput' => 'foo'));

    $expected = '<form id="testForm">'.
                '<input type="text" id="test" name="myInput" value="other_value" />'.
                '</form>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testUseGivenValueWithoutForm()
  {
    $template = '<input type="text" id="test" name="myInput" runat="server" given_value="{$#bar}" />';
    $this->registerTestingTemplate('/tags/form/input/use_given_value_without_form.html', $template);

    $page = $this->initTemplate('/tags/form/input/use_given_value_without_form.html');
    $page->set('bar', 'other_value');

    $expected = '<input type="text" id="test" name="myInput" value="other_value" />';
    $this->assertEqual($page->capture(), $expected);
  }

  function testGenerateEmptyValueIfNotValueInForm()
  {
    $template = '<form id="testForm" runat="server">'.
                 '<input type="text" id="test" name="myInput" runat="server"/>'.
                '</form>';
    $this->registerTestingTemplate('/tags/form/input/test_novalue.html', $template);

    $page = $this->initTemplate('/tags/form/input/test_novalue.html');

    $expected = '<form id="testForm">'.
                '<input type="text" id="test" name="myInput" value="" />'.
                '</form>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testAllowToUseDynamicIdAttribute()
  {
    $template = '<form id="testForm" runat="server">'.
                '<input type="text" id="{$test_value}" name="myInput" runat="server"/>'.
                '</form>';
    $this->registerTestingTemplate('/tags/form/input/dynamic_attribute.html', $template);

    $page = $this->initTemplate('/tags/form/input/dynamic_attribute.html');
    $page->setChildDatasource('testForm', array('test_value' => 'my_value'));

    $expected = '<form id="testForm">'.
                '<input type="text" name="myInput" value="" id="my_value" />'.
                '</form>';
    $this->assertEqual($page->capture(), $expected);
  }
}

