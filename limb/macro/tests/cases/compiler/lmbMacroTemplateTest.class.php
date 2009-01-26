<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
    if(ini_get('short_open_tag') == 1)
       echo __METHOD__ . "() does not check anything, since short tags are ON\n";
     
    $view = $this->_createMacroTemplate('Hello, <?echo "Bob";?>');    
    $this->assertEqual($view->render(), 'Hello, Bob');
  }
  
  function testPreprocessor_LeaveXmlTagAsIs()
  {
    if(ini_get('short_open_tag') == 0)
       echo __METHOD__ . "() does not check anything, since short tags are OFF\n";
    
    $view = $this->_createMacroTemplate("<?xml version='1.0' encoding=\"utf-8\"?>");    
    $this->assertEqual($view->render(), "<?xml version='1.0' encoding=\"utf-8\"?>");    
  }  
  
  function testPreprocessor_ProcessPHPShortOutputTags()
  {
    if(ini_get('short_open_tag') == 1)
       echo __METHOD__ . "() does not check anything, since short tags are ON\n";
    
    $view = $this->_createMacroTemplate('Hello, <?=$this->name?>');
    $view->set('name', 'Bob');
    $this->assertEqual($view->render(), 'Hello, Bob');
  }  

  function testPreprocessor_ReplaceGlobalVars()
  {
    $view = $this->_createMacroTemplate('Hello, <?php echo $#name?>');
    $view->set('name', 'Bob');
    $this->assertEqual($view->render(), 'Hello, Bob');
  }  
}


