<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroNospaceTagTest extends lmbBaseMacroTest
{
  function testNospace()
  {
    $template = " Todd {{-
    
    }} Bob {{-
    
    }}Hey\n Tomm";

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEqual($page->render(), " Todd  Bob Hey\n Tomm"); 
  }

  function testTrimSpace()
  {
    $template = '{{trim}}   Bob    {{/trim}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEqual($page->render(), 'Bob');
  }

  function testMixTrimAndNoTrim()
  {
    $template = ' Todd {{trim}}   Bob    {{/trim}} Hey';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEqual($page->render(), ' Todd Bob Hey'); 
  }

  function testSpace()
  {
    $template = '{{trim}}{{sp}}Bob{{sp}}{{/trim}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEqual($page->render(), ' Bob '); 
  }

  function testNewline()
  {
    $template = '{{trim}}{{nl}}Bob{{nl}}{{/trim}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEqual($page->render(), "\nBob\n"); 
  }

  function testTab()
  {
    $template = '{{trim}}{{tab}}Bob{{tab}}{{/trim}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEqual($page->render(), "\tBob\t"); 
  }
}

