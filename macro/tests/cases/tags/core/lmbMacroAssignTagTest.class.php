<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroAssignTagTest extends lmbBaseMacroTest
{
  function testAssignTag()
  {
    $template = '{{assign value="$#buffer" var="$output"/}}{$output}';
    $page = $this->_createMacroTemplate($template, 'tpl.html');

    $page->set('buffer', 'baz');

    $this->assertEqual($page->render(), 'baz');
  }
}