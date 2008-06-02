<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2008 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroCommentTagTest extends lmbBaseMacroTest
{
  function testComment()
  {
    $template = "before{{comment}}comment{{/comment}}after";

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEqual($page->render(), "beforeafter"); 
  }
  
  function testAliasComment()
  {
    $template = "before{{*}}comment{{/*}}after";

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEqual($page->render(), "beforeafter"); 
  }
  
}
?>