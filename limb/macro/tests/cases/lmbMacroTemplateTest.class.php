<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/macro/src/lmbMacroTemplate.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');

class lmbMacroTemplateTest extends UnitTestCase
{
  function setUp()
  {
    lmbFs :: rm(LIMB_VAR_DIR . '/view');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/view');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/view/tpl');
    lmbFs :: mkdir(LIMB_VAR_DIR . '/view/compiled');
  }

  function testRenderTemplateVar()
  {
    $view = $this->_createView('Hello, <?=$this->name?>');
    $view->set('name', 'Bob');
    $this->assertEqual($view->render(), 'Hello, Bob');
  }

  function _createView($tpl)
  {
    $file = $this->_createTemplate($tpl);
    $view = new lmbMacroTemplate($file, LIMB_VAR_DIR . '/view/compiled');
    return $view;
  }

  function _createTemplate($tpl)
  {
    $file = LIMB_VAR_DIR . '/view/tpl/' . mt_rand() . '.phtml';
    file_put_contents($file, $tpl);
    return $file;
  }
}


