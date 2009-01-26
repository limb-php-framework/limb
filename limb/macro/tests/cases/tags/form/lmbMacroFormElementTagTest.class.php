<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroFormElementTagTest extends lmbBaseMacroTest
{
  function testCheckWhatWidgetVarsAreEniqueForWidgetsWithSameIds()
  {
    $template = '{{form name="my_form1" from="$#form1_data"}}{{input type="text" name="my_input"}}{{/form}}'.
                '{{form name="my_form2" from="$#form2_data"}}{{input type="text" name="my_input"}}{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('form1_data', array('my_input' => 100));
    $page->set('form2_data', array('my_input' => 200));
 
    $out = $page->render();
    $expected = '<form name="my_form1"><input type="text" name="my_input" value="100" /></form>'.
                '<form name="my_form2"><input type="text" name="my_input" value="200" /></form>';
    $this->assertEqual($out, $expected);
  }
}
