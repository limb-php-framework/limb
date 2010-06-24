<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroStrToUpperFilterTest extends lmbBaseMacroTest
{
  function testSimple()
  {
    $code = '{$#var|strtoupper}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', 'hello');
    $out = $tpl->render();
    $this->assertEqual($out, 'HELLO');
  }
}

