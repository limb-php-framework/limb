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

class WactInputCheckboxComponentTestCase extends WactTemplateTestCase
{
  function testIsChecked()
  {
    $template = '<form id="testForm" runat="server">
                    <input type="checkbox" id="test" name="myInput" runat="server"/>
                </form>';
    $this->registerTestingTemplate('/components/form/inputcheckbox/ischecked.html', $template);

    $page = $this->initTemplate('/components/form/inputcheckbox/ischecked.html');

    $form = $page->getChild('testForm');

    $data = new WactArrayObject(array('myInput' =>'foo'));

    $form->registerDataSource($data);

    $input = $page->getChild('test');
    $input->setAttribute('value','foo');
    ob_start();
    $input->renderAttributes();
    ob_end_clean();
    $this->assertTrue($input->hasAttribute('checked'));
  }

  function testIsUnChecked()
  {
    $template = '<form id="testForm" runat="server">
                    <input type="checkbox" id="test" name="myInput" runat="server" checked="true"/>
                </form>';
    $this->registerTestingTemplate('/components/form/inputcheckbox/isunchecked.html', $template);

    $page = $this->initTemplate('/components/form/inputcheckbox/isunchecked.html');

    $form = $page->getChild('testForm');

    $data = new WactArrayObject(array('myInput' =>'foo'));

    $form->registerDataSource($data);

    $input = $page->getChild('test');
    $input->setAttribute('value','bar');
    ob_start();
    $input->renderAttributes();
    ob_end_clean();
    $this->assertFalse($input->hasAttribute('checked'));
  }

  function testIsCheckedIfValuePresent()
  {
    $template = '<form id="testForm" runat="server">
                    <input type="checkbox" id="test" name="myInput" runat="server"/>
                </form>';
    $this->registerTestingTemplate('/components/form/inputcheckbox/isnotchecked.html', $template);

    $page = $this->initTemplate('/components/form/inputcheckbox/isnotchecked.html');

    $form = $page->getChild('testForm');

    $data = new WactArrayObject(array('myInput' =>'foo'));

    $form->registerDataSource($data);

    $input = $page->getChild('test');
    ob_start();
    $input->renderAttributes();
    ob_end_clean();
    $this->assertTrue($input->hasAttribute('checked'));
  }

  function testIsCheckedAsArray()
  {
    $template = '<form id="testForm" runat="server">
                    <input type="checkbox" id="test1" name="test[]" runat="server"/>
                    <input type="checkbox" id="test2" name="test[]" runat="server"/>
                    <input type="checkbox" id="test3" name="test[]" runat="server"/>
                </form>';
    $this->registerTestingTemplate('/components/form/inputcheckbox/ischeckedasarray.html', $template);

    $page = $this->initTemplate('/components/form/inputcheckbox/ischeckedasarray.html');

    $form = $page->getChild('testForm');

    $data = new WactArrayObject(array('test' => array('1','3')));

    $form->registerDataSource($data);

    $input1 = $page->getChild('test1');
    $input1->setAttribute('value','1');
    ob_start();
    $input1->renderAttributes();
    ob_end_clean();
    $this->assertTrue($input1->hasAttribute('checked'));

    $input2 = $page->getChild('test2');
    $input2->setAttribute('value','2');
    ob_start();
    $input2->renderAttributes();
    ob_end_clean();
    $this->assertFalse($input2->hasAttribute('checked'));

    $input3 = $page->getChild('test1');
    $input3->setAttribute('value','3');
    ob_start();
    $input3->renderAttributes();
    ob_end_clean();
    $this->assertTrue($input3->hasAttribute('checked'));
  }
}
?>
