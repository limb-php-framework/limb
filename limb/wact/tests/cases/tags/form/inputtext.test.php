<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: inputtext.test.php 5189 2007-03-06 08:06:16Z serega $
 * @package    wact
 */

require_once 'limb/wact/src/components/form/form.inc.php';

class WactInputTextComponentTestCase extends WactTemplateTestCase
{
  function testValue()
  {

    $template = '<form id="testForm" runat="server">
                        <input type="text" id="test" name="myInput" value="Hello World" runat="server"/>
                    </form>';
    $this->registerTestingTemplate('/components/form/inputtext/value.html', $template);

    $page = $this->initTemplate('/components/form/inputtext/value.html');

    $form = $page->getChild('testForm');
    $input = $page->getChild('test');

    $this->assertEqual($input->getAttribute('value'),'Hello World');

  }

  function testForbidEndPag()
  {
    $template = '<form id="testForm" runat="server">
                      <input type="text" id="test" name="myInput" value="Hello World"/>
                  </form>';
    $this->registerTestingTemplate('/components/form/inputtext/forbid_end_tag.html', $template);

    $page = $this->initTemplate('/components/form/inputtext/forbid_end_tag.html');
  }

  function testSetValueByName()
  {
    $template = '<form id="testForm" runat="server">
                        <input type="text" id="test" name="myInput" runat="server"/>
                    </form>';
    $this->registerTestingTemplate('/components/form/inputtext/setvaluebyname.html', $template);

    $page = $this->initTemplate('/components/form/inputtext/setvaluebyname.html');

    $form = $page->getChild('testForm');

    $data = new WactArrayObject(array('myInput' => 'Hello World'));

    $form->registerDataSource($data);

    $input = $page->getChild('test');

    $this->assertEqual($input->getValue(),'Hello World');
  }
}
?>
