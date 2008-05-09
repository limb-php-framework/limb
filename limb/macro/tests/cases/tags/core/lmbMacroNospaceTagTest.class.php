<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroNospaceTagTest extends lmbBaseMacroTest
{
  function testTrimSpace()
  {
    $template = '{{nospace}}   Bob    {{/nospace}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEqual($page->render(), 'Bob');
  }

  function testMixTrimAndNoTrim()
  {
    $template = ' Todd {{nospace}}   Bob    {{/nospace}} Hey';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEqual($page->render(), ' Todd Bob Hey'); 
  }
}

