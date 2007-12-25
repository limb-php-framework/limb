<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
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

    $view = new lmbWactView($path);
    $view->setCacheDir(LIMB_VAR_DIR . '/compiled');
    $view->set('hello', 'Hello message!');
    $view->set('again', 'Hello again!');

    $view->render();

    $this->assertEqual($view->render(), 'Hello message!Hello again!');
  }

  function testRenderForms()
  {
    $template = '{$hello}'.
                '<form id="form1" name="form1" runat="server"><input type="text" name="title" title="Title" /></form>'.
                '<form id="form2" name="form2" runat="server"></form>';

    $this->registerTestingTemplate($path = '/limb/form_view.html', $template);

    $view = new lmbWactView($path);
    $view->setCacheDir(LIMB_VAR_DIR . '/compiled');
    $view->set('hello', 'Hello world!');

    $error_list = new lmbErrorList();
    $error_list->addError('An error in {Field} with {Value}', array('Field' => 'title'), array('Value' => 'value'));

    $view->setFormDatasource('form1', $form1 = new lmbSet());
    $view->setFormErrors('form1', $error_list);

    $view->setFormDatasource('form2', $form2 = new lmbSet());

    $this->assertEqual($view->render(),
                       'Hello world!'.
                       '<form id="form1" name="form1"><input type="text" name="title" title="Title" value="" /></form>'.
                       '<form id="form2" name="form2"></form>');

    $template = $view->getWACTTemplate();
    $form1_component = $template->findChild('form1');
    $this->assertReference($form1_component->getDataSource(), $form1);
    // lmbErrorList is converted into lmbWactErrorList
    $errors_dataset = $form1_component->getErrorsDataSet();
    $errors_dataset->rewind();
    $error = $errors_dataset->current();
    $this->assertEqual($error['message'], 'An error in "Title" with value');

    $form2_component = $template->findChild('form2');
    $this->assertReference($form2_component->getDataSource(), $form2);
  }
}


