<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/view/src/lmbMacroView.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');
lmb_require('limb/validation/src/lmbErrorList.class.php');

class lmbMacroViewTest extends UnitTestCase
{
  function setUp()
  {
    lmbFs :: rm(LIMB_VAR_DIR . '/tpl/');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/tpl/');
  }

  function testRenderSimpleVars()
  {
    $tpl = $this->_createTemplate('{$#hello}{$#again}', 'test.phtml');
    $view = $this->_createView($tpl);

    $view->set('hello', 'Hello message!');
    $view->set('again', 'Hello again!');

    $this->assertEqual($view->render(), 'Hello message!Hello again!');
  }
  
  function testRenderForms()
  {
    $template = '{{form id="form1" name="form1"}}'.
                '{{form:errors to="$form_errors"/}}'.
                '{{list using="$form_errors" as="$item"}}{{list:item}}{$item.message}|{{/list:item}}{{/list}}'.     
                '{{input type="text" name="title" title="Title" /}}'.
                '{{/form}}';

    $tpl = $this->_createTemplate($template, 'test.phtml');
    $view = $this->_createView($tpl);

    $error_list = new lmbErrorList();
    $error_list->addError('An error in {Field} with {Value}', array('Field' => 'title'), array('Value' => 'value1'));

    $view->setFormDatasource('form1', new lmbSet(array('title' => 'My title')));
    $view->setFormErrors('form1', $error_list);

    $expected = '<form id="form1" name="form1">An error in &quot;Title&quot; with value1|'.
                '<input type="text" name="title" title="Title" value="My title" />'.
                '</form>';
                
    $this->assertEqual($view->render(), $expected);
  }   

  protected function _createView($file)
  {
    $view = new lmbMacroView($file);
    return $view;
  }

  protected function _createTemplate($code, $name)
  {
    $file = LIMB_VAR_DIR . '/tpl/' . $name;
    file_put_contents($file, $code);
    return $file;
  }
}


