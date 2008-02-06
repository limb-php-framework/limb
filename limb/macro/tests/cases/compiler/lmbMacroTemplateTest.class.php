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
    $view = $this->_createMacroTemplate('Hello, <?php echo $this->name;?>');
    $view->set('name', 'Bob');
    $this->assertEqual($view->render(), 'Hello, Bob');
  }

  function testShortTagsPreprocessor()
  {
    if(ini_get('short_open_tag') == 1)
      echo __METHOD__ . " does not check anything, since short tags are On anyway\n";

    $view = $this->_createMacroTemplate('Hello, <?=$this->name?>');
    $view->set('name', 'Bob');
    $this->assertEqual($view->render(), 'Hello, Bob');
  }

  function testGlobalVarsPreprocessor()
  {
    $view = $this->_createMacroTemplate('Hello, <?=$#name?>');
    $view->set('name', 'Bob');
    $this->assertEqual($view->render(), 'Hello, Bob');
  }  
}


