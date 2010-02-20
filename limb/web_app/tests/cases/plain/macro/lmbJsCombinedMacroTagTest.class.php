<?php

/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class lmbJsCombinedMacroTagTest extends lmbMacroTestCase
{
  function testRender()
  {
    $root = lmb_env_get('LIMB_VAR_DIR').'/www/';
    lmb_env_set('LIMB_DOCUMENT_ROOT', $root); 
    lmbFs :: safeWrite($root . 'js/main.js', 'content main.js');
    lmbFs :: safeWrite($root . 'js/blog.js', 'is blog.js');

    lmbFs :: rm($root . '/media/var');

    $template = '
    {{js:combined dir="media/var"}}
      {{js_once src="js/main.js" }}
      {{js_once src="js/blog.js" }}
    {{/js:combined}}
    ';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 
    $content = trim($page->render());
    $file = array_shift(lmbFs :: ls($root.'/media/var/'));

    $this->assertEqual('<script type="text/javascript" src="'.$this->toolkit->addVersionToUrl('media/var/'.$file).'"></script>', $content);

    $js_content = 
      "/* main.js */\n".
      "content main.js\n".
      "/* blog.js */\n".
      "is blog.js";
    $this->assertEqual(file_get_contents($root . 'media/var/'.$file), $js_content);
  }

  function testNotFoundFile()
  {
    $root = lmb_env_get('LIMB_VAR_DIR').'/www';
    lmb_env_set('LIMB_DOCUMENT_ROOT', $root);
    lmbFs :: rm($root);

    $template = '{{js_combined dir="media/"}}{{js_once src="js/main.js" }}{{/js_combined}}';
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
    
    lmbFs :: safeWrite($root . '/js/blog.js', 'function blog() {};');
    
    $template = '{{js_combined dir="media"}}{{js_once src="js/main.js" safe="true" }}{{js_once src="js/blog.js" }}{{/js_combined}}';
    $page = $this->_createMacroTemplate($template, 'tpl.html'); 
    $page->render();
    
    $file = array_shift(lmbFs :: ls($root.'/media/'));

    $js_content = "/* main.js - NOT FOUND */\n\n/* blog.js */\nfunction blog() {};";
    $this->assertEqual(file_get_contents($root . '/media/'.$file), $js_content);
  }
}

