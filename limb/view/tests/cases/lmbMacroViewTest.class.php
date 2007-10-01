<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/view/src/lmbMacroView.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');

class lmbMacroViewTest extends UnitTestCase
{
  function setUp()
  {
    lmbFs :: rm(LIMB_VAR_DIR . '/tpl/');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/tpl/');
  }

  function testRenderSimpleVars()
  {
    $tpl = $this->_createTemplate('{{$#hello}}{{$#again}}', 'test.phtml');
    $view = $this->_createView($tpl);

    $view->set('hello', 'Hello message!');
    $view->set('again', 'Hello again!');

    $view->render();

    $this->assertEqual($view->render(), 'Hello message!Hello again!');
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


