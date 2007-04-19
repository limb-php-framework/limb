<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactInputElementTest.class.php 5684 2007-04-19 08:34:48Z serega $
 * @package    wact
 */

require_once 'limb/wact/src/components/form/form.inc.php';

class WactInputElementTest extends WactTemplateTestCase
{
  function testGetValueFromForm()
  {
    $template ='<form id="testForm" runat="server">'.
                '<input type="text" id="test" name="myInput" runat="server"/>'.
               '</form>';
    $this->registerTestingTemplate('/components/form/inputelement/value_from_form.html', $template);

    $page = $this->initTemplate('/components/form/inputelement/value_from_form.html');

    $form = $page->getChild('testForm');
    $form->registerDataSource(array('myInput' => 'foo'));

    $expected = '<form id="testForm">'.
                '<input type="text" id="test" name="myInput" value="foo" />'.
                '</form>';
    $this->assertEqual($page->capture(), $expected);
  }

  function testGenerateEmptyValueIfNotValueInForm()
  {
    $template = '<form id="testForm" runat="server">'.
                 '<input type="text" id="test" name="myInput" runat="server"/>'.
                '</form>';
    $this->registerTestingTemplate('/components/form/inputelement/test_novalue.html', $template);

    $page = $this->initTemplate('/components/form/inputelement/test_novalue.html');

    $expected = '<form id="testForm">'.
                '<input type="text" id="test" name="myInput" value="" />'.
                '</form>';
    $this->assertEqual($page->capture(), $expected);
  }

}
?>
