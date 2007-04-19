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

    $data = new WactArrayObject(array('myInput' =>'foo'));

    $form->registerDataSource($data);

    $input = $page->getChild('test');
    $input->setAttribute('value','foo');

    $expected = '<form id="testForm"><input type="checkbox" id="test" name="myInput" value="foo" checked="checked" /></form>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testRemoveCheckedIfNoChecked()
  {
    $template = '<form id="testForm" runat="server">'.
                '<input type="checkbox" id="test" name="myInput" runat="server" value="bar" checked="true"/>' .
                '</form>';
    $this->registerTestingTemplate('/components/form/inputcheckbox/isunchecked.html', $template);

    $page = $this->initTemplate('/components/form/inputcheckbox/isunchecked.html');

    $form = $page->getChild('testForm');

    $data = new WactArrayObject(array('myInput' => 'foo')); // foo is not equal to bar

    $form->registerDataSource($data);

    $input = $page->getChild('test');
    $input->setAttribute('value','bar');

    $expected = '<form id="testForm"><input type="checkbox" id="test" name="myInput" value="bar" /></form>';
    $this->assertEqual($page->capture(), $expected);
  }
}
?>
