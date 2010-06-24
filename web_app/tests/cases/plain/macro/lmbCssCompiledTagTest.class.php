<?php

/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class lmbCssCompiledTagTest extends lmbMacroTestCase
{
  function testOnceRender()
  {
    $root = lmb_env_get('LIMB_VAR_DIR').'/www/';
    lmb_env_set('LIMB_DOCUMENT_ROOT', $root);
    lmbFs :: safeWrite($root . 'style/main.css', 'body {background-url: url("../images/one.jpg");}');
    lmbFs :: safeWrite($root . 'images/one.jpg', 'simple content');
    
    $template = '{{css_compiled src="style/main.css" dir="media/css" /}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html'); 
    
    $content = $page->render();
    $src = $this->toolkit->addVersionToUrl('media/css/style-main.css');
    $this->assertEqual('<link rel="stylesheet" type="text/css" href="'.$src.'" />', $content);

    $compiled_file = $root . 'media/css/style-main.css';
    $src = $this->toolkit->addVersionToUrl('images/one.jpg');
    $this->assertEqual('body {background-url: url('.$src.');}', file_get_contents($compiled_file));
  }
}

