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

class WactInputCheckboxComponentTest extends WactTemplateTestCase
{
  function testIsChecked()
  {
    $template = '<form id="testForm" runat="server">'.
                '<input type="checkbox" id="test" name="myInput" runat="server"/>'.
                '</form>';

    $this->registerTestingTemplate('/components/form/inputcheckbox/ischecked.html', $template);

    $page = $this->initTemplate('/components/form/inputcheckbox/ischecked.html');

    $form = $page->getChild('testForm');

    $data = new WactArrayObject(array('myInput' =>'foo'));

    $form->registerDataSource($data);

    $input = $page->getChild('test');
    $input->setAttribute('value','foo');

    $expected = '<form id="testForm"><input type="checkbox" id="test" name="myInput" value="foo" checked="checked" /></form>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testIsUnChecked()
  {
    $template = '<form id="testForm" runat="server">'.
                '<input type="checkbox" id="test" name="myInput" runat="server" checked="true"/>' .
                '</form>';
    $this->registerTestingTemplate('/components/form/inputcheckbox/isunchecked.html', $template);

    $page = $this->initTemplate('/components/form/inputcheckbox/isunchecked.html');

    $form = $page->getChild('testForm');

    $data = new WactArrayObject(array('myInput' =>'foo'));

    $form->registerDataSource($data);

    $input = $page->getChild('test');
    $input->setAttribute('value','bar');

    $expected = '<form id="testForm"><input type="checkbox" id="test" name="myInput" value="bar" /></form>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testIsCheckedIfValuePresent()
  {
    $template = '<form id="testForm" runat="server">'.
                '<input type="checkbox" id="test" name="myInput" runat="server"/>'.
                '</form>';
    $this->registerTestingTemplate('/components/form/inputcheckbox/isnotchecked.html', $template);

    $page = $this->initTemplate('/components/form/inputcheckbox/isnotchecked.html');

    $form = $page->getChild('testForm');

    $data = new WactArrayObject(array('myInput' =>'foo'));

    $form->registerDataSource($data);

    $expected = '<form id="testForm"><input type="checkbox" id="test" name="myInput" checked="checked" /></form>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testIsCheckedAsArray()
  {
    $template = '<form id="testForm" runat="server">'.
                    '<input type="checkbox" id="test1" name="test[]" runat="server"/>'.
                    '<input type="checkbox" id="test2" name="test[]" runat="server"/>'.
                    '<input type="checkbox" id="test3" name="test[]" runat="server"/>'.
                '</form>';
    $this->registerTestingTemplate('/components/form/inputcheckbox/ischeckedasarray.html', $template);

    $page = $this->initTemplate('/components/form/inputcheckbox/ischeckedasarray.html');

    $form = $page->getChild('testForm');

    $data = new WactArrayObject(array('test' => array('1','3')));
    $form->registerDataSource($data);

    $input1 = $page->getChild('test1');
    $input1->setAttribute('value','1');

    $input2 = $page->getChild('test2');
    $input2->setAttribute('value','2');

    $input3 = $page->getChild('test3');
    $input3->setAttribute('value','3');

    $expected = '<form id="testForm">'.
                  '<input type="checkbox" id="test1" name="test[]" value="1" checked="checked" />'.
                  '<input type="checkbox" id="test2" name="test[]" value="2" />'.
                  '<input type="checkbox" id="test3" name="test[]" value="3" checked="checked" />'.
                '</form>';
    $this->assertEqual($page->capture(), $expected);
  }
}
?>
