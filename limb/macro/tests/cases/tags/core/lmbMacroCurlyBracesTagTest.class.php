<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroCurlyBracesTagTest extends lmbBaseMacroTest
{
  function testBraces()
  {
    $template = "{{cbo}}{{cbo}}macro{{cbc}}{{cbc}}";

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEqual($page->render(), "{{macro}}"); 
  }
}

