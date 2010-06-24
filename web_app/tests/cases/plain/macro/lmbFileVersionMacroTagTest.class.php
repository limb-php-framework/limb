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

  function testToVar()
  {
    lmb_env_set('LIMB_DOCUMENT_ROOT', lmb_env_get('LIMB_VAR_DIR').'/www');
    lmbFs :: safeWrite(lmb_env_get('LIMB_VAR_DIR') . '/www/index.html', '<html>Hello!</html>');

    $template = '{{file:version src="index.html" to_var="$one" }} -{$one}-';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 

    $this->assertEqual('-'.$this->toolkit->addVersionToUrl('index.html').'-', trim($page->render()));
  }

  function testGzipStatic()
  {
    if(!function_exists('gzencode'))
      return print("Skip: function gzencode not exists.\n");

    lmb_env_set('LIMB_DOCUMENT_ROOT', lmb_env_get('LIMB_VAR_DIR').'/www/');
    lmbFs :: safeWrite(lmb_env_get('LIMB_VAR_DIR') . '/www/one.js', 'var window = {};');
    $doc_root = lmb_env_get('LIMB_DOCUMENT_ROOT');

    $template = '{{file:version src="one.js" gzip_static_dir="media/var/gz" gzip_level="9" }}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $content = $page->render();
    $file = 'media/var/gz/one.js';
    $this->assertEqual($content, $this->toolkit->addVersionToUrl($file, false));
    $this->assertEqual('var window = {};', file_get_contents($doc_root . $file));
    $gz_file = $doc_root . $file . '.gz';
    $this->assertTrue(file_exists($gz_file));
    $this->assertEqual(gzencode('var window = {};', 9, FORCE_DEFLATE), file_get_contents($gz_file));
  }
}
