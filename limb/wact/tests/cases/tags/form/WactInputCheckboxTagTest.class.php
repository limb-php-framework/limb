<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once 'limb/wact/src/components/form/form.inc.php';

class WactInputCheckboxTagTest extends WactTemplateTestCase
{
  // see also tests in WactCheckableInputComponentTest.class.php
  function testIsChecked()
  {
    $template = '<form id="testForm" runat="server">'.
                '<input type="checkbox" id="test" name="myInput" runat="server"/>'.
                '</form>';

    $this->registerTestingTemplate('/components/form/inputcheckbox/ischecked.html', $template);

    $page = $this->initTemplate('/components/form/inputcheckbox/ischecked.html');

    $form = $page->getChild('testForm');

    $data = new ArrayObject(array('myInput' =>'foo'));
    $form->registerDataSource($data);

    $input = $page->getChild('test');
    $input->setAttribute('value','foo');

    $expected = '<form id="testForm"><input type="checkbox" id="test" name="myInput" value="foo" checked="checked" /></form>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testRemoveCheckedIfNoChecked()
  {
    $template = '<form id="testForm" runat="server">'.
                '<input type="checkbox" id="test" name="myInput" runat="server" value="bar" checked />' .
                '</form>';
    $this->registerTestingTemplate('/components/form/inputcheckbox/isunchecked.html', $template);

    $page = $this->initTemplate('/components/form/inputcheckbox/isunchecked.html');

    $form = $page->getChild('testForm');

    $data = new ArrayObject(array('myInput' => 'foo')); // foo is not equal to bar
    $form->registerDataSource($data);

    $input = $page->getChild('test');
    $input->setAttribute('value','bar');

    $expected = '<form id="testForm"><input type="checkbox" id="test" name="myInput" value="bar" /></form>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testIsCheckedViaGivenValue()
  {
    $template = '<input type="checkbox" id="test" name="myInput" given_value="{$#bar}" runat="server" />';

    $this->registerTestingTemplate('/components/form/inputcheckbox/is_checked_via_given_value.html', $template);

    $page = $this->initTemplate('/components/form/inputcheckbox/is_checked_via_given_value.html');

    $page->set('bar', '1');

    $expected = '<input type="checkbox" id="test" name="myInput" checked="checked" />';
    $this->assertEqual($page->capture(), $expected);
  }

  function testNotCheckedViaGivenValueAndValueAttribute()
  {
    $template = '<input type="checkbox" id="test" name="myInput" value="1" given_value="{$#bar}" checked="true" runat="server" />';

    $this->registerTestingTemplate('/components/form/inputcheckbox/not_checked_via_given_value.html', $template);

    $page = $this->initTemplate('/components/form/inputcheckbox/not_checked_via_given_value.html');

    $page->set('bar', '2');

    $expected = '<input type="checkbox" id="test" name="myInput" value="1" />';
    $this->assertEqual($page->capture(), $expected);
  }
}

