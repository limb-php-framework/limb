<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroRawFilterTest extends lmbBaseMacroTest
{
  function testSimple()
  {
    $code = '{$#var|raw}';
    $tpl = $this->_createMacroTemplate($code, 'tpl.html');
    $tpl->set('var', '<>');
    $out = $tpl->render();
    $this->assertEqual($out, '<>');
  }
}

