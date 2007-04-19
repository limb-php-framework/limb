<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactInputPasswordComponentTest.class.php 5684 2007-04-19 08:34:48Z serega $
 * @package    wact
 */

require_once 'limb/wact/src/components/form/form.inc.php';

class WactInputPasswordComponentTest extends WactTemplateTestCase
{
  function testNotGenerateValue()
  {
    $template = '<form id="testForm" runat="server">'.
                  '<input type="password" id="test" name="myInput" runat="server"/>'.
                '</form>';

    $this->registerTestingTemplate('/components/form/inputpassword/not_generate_value.html', $template);

    $page = $this->initTemplate('/components/form/inputpassword/not_generate_value.html');

    $form = $page->getChild('testForm');
    $form->set('myInput', 'secret');

    $expected = '<form id="testForm"><input type="password" id="test" name="myInput" /></form>';
    $this->assertEqual($page->capture(), $expected);
  }
}
?>
