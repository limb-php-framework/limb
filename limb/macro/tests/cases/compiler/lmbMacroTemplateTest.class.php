<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
 
class lmbMacroTemplateTest extends lmbBaseMacroTest
{  
  function testRenderTemplateVar()
  {
    $view = $this->_createView('Hello, <?php echo $this->name;?>');
    $view->set('name', 'Bob');
    $this->assertEqual($view->render(), 'Hello, Bob');
  }

  function testShortTagsPreprocessor()
  {
    if(ini_get('short_open_tag') == 1)
      echo __METHOD__ . " does not check anything, since short tags are On anyway\n";

    $view = $this->_createView('Hello, <?=$this->name?>');
    $view->set('name', 'Bob');
    $this->assertEqual($view->render(), 'Hello, Bob');
  }

  function testGlobalVarsPreprocessor()
  {
    $view = $this->_createView('Hello, <?=$#name?>');
    $view->set('name', 'Bob');
    $this->assertEqual($view->render(), 'Hello, Bob');
  }

  function _createView($tpl, $config = null)
  {
    $file = $this->_createTemplate($tpl);
    $view = new lmbMacroTemplate($file, $this->_createMacroConfig());
    return $view;
  }

  function _createTemplate($tpl)
  {
    $file = $this->tpl_dir . mt_rand() . '.phtml';
    file_put_contents($file, $tpl);
    return $file;
  }
}


