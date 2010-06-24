<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once 'limb/wact/src/components/form/form.inc.php';

class WactRadioInputTagTest extends WactTemplateTestCase
{
  function testGenerateChecked()
  {
    // Note value is almost required for input type radio elements.
    $template = '<form id="testForm" runat="server">'.
                  '<input type="radio" id="test1" name="myInput" value="1" runat="server"/>'.
                  '<input type="radio" id="test2" name="myInput" value="2" runat="server"/>'.
                '</form>';

    $this->registerTestingTemplate('/tags/form/radio_input/generate_checked.html', $template);
    $page = $this->initTemplate('/tags/form/radio_input/generate_checked.html');

    $form = $page->getChild('testForm');
    $form->registerDataSource(array('myInput' => 2));

    $expected = '<form id="testForm">'.
                 '<input type="radio" id="test1" name="myInput" value="1" />'.
                 '<input type="radio" id="test2" name="myInput" value="2" checked="checked" />'.
                '</form>';

    $this->assertEqual($page->capture(), $expected);
  }

  function testGenerateOtherChecked()
  {
    $template = '<form id="testForm" runat="server">'.
                  '<input type="radio" id="test1" name="myInput" value="1" checked="true" runat="server"/>'.
                  '<input type="radio" id="test2" name="myInput" value="2" runat="server"/>'.
                '</form>';

    $this->registerTestingTemplate('/tags/form/radio_input/generate_unchecked.html', $template);
    $page = $this->initTemplate('/tags/form/radio_input/generate_unchecked.html');

    $form = $page->getChild('testForm');
    $form->registerDataSource(array('myInput' => 2));

    $expected = '<form id="testForm">'.
                 '<input type="radio" id="test1" name="myInput" value="1" />'.
                 '<input type="radio" id="test2" name="myInput" value="2" checked="checked" />'.
                '</form>';

    $this->assertEqual($page->capture(), $expected);
  }
}

