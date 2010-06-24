<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once 'limb/wact/src/components/form/form.inc.php';

class WactPasswordInputTagTest extends WactTemplateTestCase
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

