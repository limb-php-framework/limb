<?php

/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class lmbJsRequireOnceMacroTagTest extends lmbMacroTestCase
{
  function testOnceRender()
  {
    lmb_env_set('LIMB_DOCUMENT_ROOT', lmb_env_get('LIMB_VAR_DIR').'/www');
    lmbFs :: safeWrite(lmb_env_get('LIMB_VAR_DIR') . '/www/js/main.js', 'function() { alert(1); }');
    $template = '{{js:require_once src="js/main.js" }}{{js_once src="js/main.js" }}';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 

    $content = '<script type="text/javascript" src="'.$this->toolkit->addVersionToUrl('js/main.js').'" ></script>';
    $this->assertEqual($content, $page->render());
  }

  function testNotFoundFile()
  {
    lmb_env_set('LIMB_DOCUMENT_ROOT', lmb_env_get('LIMB_VAR_DIR'));
    
    $template = '{{js:require_once src="js/main.js" }}';
    $page = $this->_createMacroTemplate($template, 'tpl.html'); 
   
    try
    {
      $page->render();
      $this->assertTrue(false);
    } 
    catch(lmbException $e)
    {
      $this->assertTrue(true);
    }
    
    $template = '{{js:require_once src="js/main.js" safe="true" }}';
    $page = $this->_createMacroTemplate($template, 'tpl.html'); 
   
    try
    {
      $page->render();
      $this->assertTrue(true);
    } 
    catch(lmbException $e)
    {
      $this->assertTrue(false);
    }
  }
}

