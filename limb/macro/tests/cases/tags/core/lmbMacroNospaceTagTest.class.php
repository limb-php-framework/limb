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
  function testNospace()
  {
    $template = " Todd {{-
    
    }} Bob {{-
    
    }}Hey\n Tomm";

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEqual($page->render(), " Todd  Bob Hey\n Tomm"); 
  }
}

