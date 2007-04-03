<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbWactViewTest.class.php 5226 2007-03-13 14:12:29Z serega $
 * @package    view
 */
lmb_require('limb/view/src/lmbWactView.class.php');

class lmbWactViewTest extends lmbWactTestCase
{
  function setUp()
  {
    parent :: setUp();
    $this->toolkit->setWactLocator($this->locator);
  }

  function testRenderSimpleVars()
  {
    $template = '{$hello}{$again}';

    $this->registerTestingTemplate($path = '/limb/simple_view.html', $template);

    $view = new lmbWactView();
    $view->setTemplate($path);
    $view->set('hello', 'Hello message!');
    $view->set('again', 'Hello again!');

    $view->render();

    $this->assertEqual($view->render(), 'Hello message!Hello again!');
  }

  function testRenderForms()
  {
    $template = '{$hello}'.
                '<form id="form1" name="form1" runat="server"></form>'.
                '<form id="form2" name="form2" runat="server"></form>';

    $this->registerTestingTemplate($path = '/limb/form_view.html', $template);

    $view = new lmbWactView();
    $view->setTemplate($path);
    $view->set('hello', 'Hello world!');

    $error_list1 = new lmbErrorList();
    $error_list1->addError('An error');
    $form1->error_list = $error_list1;
    $view->setFormDatasource('form1', $form1 = new lmbDataspace());
    $view->setFormErrors('form1', $error_list1);

    $view->setFormDatasource('form2', $form2 = new lmbDataspace());

    $this->assertEqual($view->render(),
                       'Hello world!'.
                       '<form id="form1" name="form1"></form>'.
                       '<form id="form2" name="form2"></form>');

    $template = $view->getWACTTemplate();
    $form1_component = $template->findChild('form1');
    $this->assertEqual($form1_component->getDatasource(), $form1);
    $this->assertEqual($form1_component->getErrorsDataSet(), $error_list1);

    $form2_component = $template->findChild('form2');
    $this->assertEqual($form2_component->getDatasource(), $form2);
  }
}

?>