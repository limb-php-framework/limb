<?php

/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class lmbFileVersionMacroTagTest extends lmbMacroTestCase
{
  function testRender()
  {
    lmb_env_set('LIMB_DOCUMENT_ROOT', lmb_env_get('LIMB_VAR_DIR').'/www');
    lmbFs :: safeWrite(lmb_env_get('LIMB_VAR_DIR') . '/www/index.html', '<html>Hello!</html>');

    $template = '{{file:version src="index.html" }}';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 

    $content = $this->toolkit->addVersionToUrl('index.html');
    $this->assertEqual($content, $page->render());
  }
  
  function testSafeAttribute()
  {
    lmb_env_set('LIMB_DOCUMENT_ROOT', lmb_env_get('LIMB_VAR_DIR').'/www/');
    lmbFs :: rm(lmb_env_get('LIMB_DOCUMENT_ROOT').'not_found.html');

    $template = '{{file:version src="not_found.html" }}';
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
    
    $template = '{{file:version src="not_found.html" safe="1" }}';
    $page = $this->_createMacroTemplate($template, 'tpl.html'); 

    $content = $this->toolkit->addVersionToUrl('not_found.html', true);
    $this->assertEqual($content, $page->render());
  }
}
