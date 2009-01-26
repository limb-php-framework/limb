<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroNumberFormatFilterTest extends lmbBaseMacroTest
{
  function testNoParams()
  {
    $code = '{$#var|number_format}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', 1234.56);
    $out = $tpl->render();
    $this->assertEqual($out, '1,235');
  }
  
  function testWithParams()
  {
    $code = '{$#var|number_format:2,","," "}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', 1234.56);
    $out = $tpl->render();
    $this->assertEqual($out, '1 234,56');
  }

  function testAlias()
  {
    $code = '{$#var|number:2,","," "}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', 1234.56);
    $out = $tpl->render();
    $this->assertEqual($out, '1 234,56');
  }
}

