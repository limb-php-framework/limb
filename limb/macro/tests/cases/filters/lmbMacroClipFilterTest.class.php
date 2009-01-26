<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroClipFilterTest extends lmbBaseMacroTest
{
  function testStatic()
  {
    $code = '{$#str|clip:1}{$#str|clip:2}{$#str|clip:3}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('str', '12');
    $out = $tpl->render();
    $this->assertEqual($out, '11212');        
  }  
  
  function testDinamic()
  {
    $code = '{$#str|clip:$#cnt}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('str', 'foo');
    $tpl->set('cnt', '1');
    $out = $tpl->render();
    $this->assertEqual($out, 'f');        
  }  
  
}

