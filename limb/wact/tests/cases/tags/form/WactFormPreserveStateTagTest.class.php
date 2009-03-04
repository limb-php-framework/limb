<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/components/form/form.inc.php');

class WactFormPreserveStateTagTest extends WactTemplateTestCase
{
  function testPreserveState()
  {
    $template = '<form id="testForm" name="testForm" runat="server">' .
                '<form:PRESERVE_STATE name="id"/>' .
                '</form>';

    $this->registerTestingTemplate('/wact/form/preserve_state1.html', $template);

    $page = $this->initTemplate('/wact/form/preserve_state1.html');
    $form = $page->findChild('testForm');
    $form->registerDatasource(array('id' => 10000));

    $this->assertEqual($page->capture(),
                       '<form id="testForm" name="testForm">' .
                       '<input type="hidden" name="id" value="10000"/>' .
                       '</form>');
  }
}

