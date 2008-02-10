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
  function testPreprocessor_LeavePHPFullTagsAsIs()
  {
    $view = $this->_createMacroTemplate('Hello, <?php echo $this->name;?>');
    $view->set('name', 'Bob');
    $this->assertEqual($view->render(), 'Hello, Bob');
  }

  function testPreprocessor_ProcessPHPShortTags()
  {
    $view = $this->_createMacroTemplate('Hello, <?echo "Bob";?>');    
    $this->assertEqual($view->render(), 'Hello, Bob');
  }
  
  function testPreprocessor_LeaveXmlTagAsIs()
  {
    $view = $this->_createMacroTemplate("<?xml version='1.0' encoding='utf-8'?>");    
    $this->assertEqual($view->render(), "<?xml version='1.0' encoding='utf-8'?>");
  }  
  
  function testPreprocessor_ProcessPHPShortOutputTags()
  {
    $view = $this->_createMacroTemplate('Hello, <?=$this->name?>');
    $view->set('name', 'Bob');
    $this->assertEqual($view->render(), 'Hello, Bob');
  }  

  function testPreprocessor_ReplaceGlobalVars()
  {
    $view = $this->_createMacroTemplate('Hello, <?=$#name?>');
    $view->set('name', 'Bob');
    $this->assertEqual($view->render(), 'Hello, Bob');
  }  
}


