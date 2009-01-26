<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');

class WactTextAreaTagTest extends WactTemplateTestCase
{
  function testTextArea()
  {
    $template = '<form id="my_form" runat="server">'.
                '<textarea id="test" name="foo" runat="server">Some text</textarea>'.
                '</form>';

    $this->registerTestingTemplate('/tags/form/control/textarea/textarea.html', $template);

    $page = $this->initTemplate('/tags/form/control/textarea/textarea.html');
    $this->assertIsA($page->findChild('test'),'WactTextAreaComponent');

    $form = $page->getChild('my_form');
    $form->set('foo', 'other text');

    $expected = '<form id="my_form">'.
                 '<textarea id="test" name="foo">other text</textarea>'.
                '</form>';
    $this->assertEqual($page->capture(), $expected);
  }
}

