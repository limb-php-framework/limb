<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroDefaultFilterTest extends lmbBaseMacroTest
{
  function testEmptyVariable()
  {
    $code = '{$#var|default:"val"}';
    $tpl = $this->_createMacroTemplate($code, 'empty');
    $tpl->set('var', '');
    $out = $tpl->render();
    $this->assertEqual($out, 'val');
  }

  function testNotDefinedVariable()
  {
    $code = '{$var|default:"val"}';
    $tpl = $this->_createMacroTemplate($code, 'not_defined');
    $out = $tpl->render();
    $this->assertEqual($out, 'val');
  }
}

