<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroTextAreaTagTest extends lmbBaseMacroTest
{
  function testRenderEscapedValue()
  {
    $template = '{{textarea name="my_textarea" value="$#value" /}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('value', "<< super >>");
    
    $expected = '<textarea name="my_textarea">&lt;&lt; super &gt;&gt;</textarea>';
    $this->assertEqual($page->render(), $expected);
  }

  function testTakeValueFromFormIfPossible()
  {
    $template = '{{form name="my_form" from="$#form_data"}}{{textarea name="my_textarea"/}}{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('form_data', array('my_textarea' => "<>"));
 
    $out = $page->render();
    $this->assertEqual($out, '<form name="my_form"><textarea name="my_textarea">&lt;&gt;</textarea></form>');
  }    
}

