<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactFormPreserveStateTagTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
?>
