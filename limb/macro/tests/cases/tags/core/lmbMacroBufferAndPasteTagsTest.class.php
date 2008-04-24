<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroBufferAndPasteTagsTest extends lmbBaseMacroTest
{
  function testCopyAndPasteFromBuffer()
  {
    $template = '{{buffer into="my_buffer"}}F{{/buffer}}N|{{paste from="my_buffer"}}|{{paste from="my_buffer"}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    
    $this->assertEqual($page->render(), 'N|F|F');
  }
}

