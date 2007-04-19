<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: input.test.php 5189 2007-03-06 08:06:16Z serega $
 * @package    wact
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

  function testGetValueFromForm()
  {
    $template ='<form id="testForm" runat="server">'.
                '<input type="text" id="test" name="myInput" runat="server"/>'.
               '</form>';
    $this->registerTestingTemplate('/components/form/inputelement/value_from_form.html', $template);

    $page = $this->initTemplate('/components/form/inputelement/value_from_form.html');

    $form = $page->getChild('testForm');
    $form->registerDataSource(array('myInput' => 'foo'));

    $expected = '<form id="testForm">'.
                '<input type="text" id="test" name="myInput" value="foo" />'.
                '</form>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testGenerateEmptyValueIfNotValueInForm()
  {
    $template = '<form id="testForm" runat="server">'.
                 '<input type="text" id="test" name="myInput" runat="server"/>'.
                '</form>';
    $this->registerTestingTemplate('/components/form/inputelement/test_novalue.html', $template);

    $page = $this->initTemplate('/components/form/inputelement/test_novalue.html');

    $expected = '<form id="testForm">'.
                '<input type="text" id="test" name="myInput" value="" />'.
                '</form>';
    $this->assertEqual($page->capture(), $expected);
  }
}
?>