<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroStrToLowerFilterTest extends lmbBaseMacroTest
{
  function testSimple()
  {
    $code = '{$#var|strtolower}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', 'HELLO');
    $out = $tpl->render();
    $this->assertEqual($out, 'hello');
  }

  function testAlias()
  {
    $code = '{$#var|lowercase}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', 'HELLO');
    $out = $tpl->render();
    $this->assertEqual($out, 'hello');
  }  
}

