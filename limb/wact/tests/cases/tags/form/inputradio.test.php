<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: inputradio.test.php 5339 2007-03-23 14:12:48Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/components/form/form.inc.php';

class WactInputRadioComponentTestCase extends WactTemplateTestCase {

  function testIsChecked() {

    $template = '<form id="testForm" runat="server">
                        <input type="radio" id="test" name="myInput" runat="server"/>
                    </form>';
    $this->registerTestingTemplate('/components/form/inputradio/ischecked.html', $template);

    $page = $this->initTemplate('/components/form/inputradio/ischecked.html');

    $form = $page->getChild('testForm');

    $data = new WactArrayObject(array('myInput' => 'foo'));

    $form->registerDataSource($data);

    $input = $page->getChild('test');
    $input->setAttribute('value','foo');
    ob_start();
    $input->renderAttributes();
    ob_end_clean();
    $this->assertTrue($input->hasAttribute('checked'));

  }

  function testIsUnChecked() {

    $template = '<form id="testForm" runat="server">
                        <input type="radio" id="test" name="myInput" runat="server" checked="true"/>
                    </form>';
    $this->registerTestingTemplate('/components/form/inputradio/isunchecked.html', $template);

    $page = $this->initTemplate('/components/form/inputradio/isunchecked.html');

    $form = $page->getChild('testForm');

    $data = new WactArrayObject(array('myInput' => 'foo'));

    $form->registerDataSource($data);

    $input = $page->getChild('test');
    $input->setAttribute('value','bar');
    ob_start();
    $input->renderAttributes();
    ob_end_clean();
    $this->assertFalse($input->hasAttribute('checked'));

  }

  function testIsCheckedIfValuePresent() {

    $template = '<form id="testForm" runat="server">
                        <input type="radio" id="test" name="myInput" runat="server"/>
                    </form>';
    $this->registerTestingTemplate('/components/form/inputradio/isnotchecked.html', $template);

    $page = $this->initTemplate('/components/form/inputradio/isnotchecked.html');

    $form = $page->getChild('testForm');

    $data = new WactArrayObject(array('myInput' => 'foo'));

    $form->registerDataSource($data);

    $input = $page->getChild('test');
    ob_start();
    $input->renderAttributes();
    ob_end_clean();
    $this->assertTrue($input->hasAttribute('checked'));
  }
}
?>
