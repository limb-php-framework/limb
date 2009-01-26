<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroInputTagTest extends lmbBaseMacroTest
{
  function testTypeText()
  {
    $template = '{{input type="text" name="my_input" value="$#var"}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('var', 100);
 
    $out = $page->render();
    $this->assertEqual($out, '<input type="text" name="my_input" value="100" />');
  }

  function testTypeTextRendersValueAttributeInAnyCase()
  {
    $template = '{{input type="text" name="my_input"}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
 
    $out = $page->render();
    $this->assertEqual($out, '<input type="text" name="my_input" value="" />');
  }

  function testTypeTextTakesValueFromFormIfPossible()
  {
    $template = '{{form name="my_form" from="$#form_data"}}{{input type="text" name="my_input"}}{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('form_data', array('my_input' => 100));
 
    $out = $page->render();
    $this->assertEqual($out, '<form name="my_form"><input type="text" name="my_input" value="100" /></form>');
  }   
  
  function testTypesHiddenAndButtonAndImage()
  {
    $template = '{{input type="hidden" name="my_hidden" value="$#for_hidden"}}'.
                '{{input type="button" name="my_button" value="$#for_button"}}'.
                '{{input type="image" src="some_path" name="my_image" value="$#for_image"}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('for_hidden', 10);
    $page->set('for_button', 20);
    $page->set('for_image', 30);
 
    $out = $page->render();
    $expected = '<input type="hidden" name="my_hidden" value="10" />'.
                '<input type="button" name="my_button" value="20" />'.
                '<input type="image" src="some_path" name="my_image" value="30" />';
    $this->assertEqual($out, $expected);
  }
  
  function testTypesFileAndSubmitAndPasswordAndResetAndFile_DontTakeValueFromFormDatasource()
  {
    $template = '{{form name="my_form" from="$#form_data"}}'.
                '{{input type="file" name="my_file"}}'.
                '{{input type="submit" name="my_submit"}}'.
                '{{input type="password" name="my_password"}}'.
                '{{input type="reset" name="my_reset"}}'.
                '{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('form_data', array('my_file' => 10, 'my_submit' => 20, 'my_password' => 30, 'my_reset' => 40));
 
    $out = $page->render();
    $expected = '<form name="my_form"><input type="file" name="my_file" />'.
                '<input type="submit" name="my_submit" />'.
                '<input type="password" name="my_password" />'.
                '<input type="reset" name="my_reset" /></form>';
    $this->assertEqual($out, $expected);
  }
  
  function testTypesFileAndSubmitAndPasswordAndResetAndFile_MayRenderValueAttribute()
  {
    $template = '{{input type="file" name="my_file" value="$#my_file"}}'.
                '{{input type="submit" name="my_submit" value="title_{$#my_submit}"}}'.
                '{{input type="password" name="my_password" value="$#my_password"}}'.
                '{{input type="reset" name="my_reset" value="any_value"}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('my_file', 10);
    $page->set('my_submit', 20);
    $page->set('my_password', 30);
 
    $out = $page->render();
    $expected = '<input type="file" name="my_file" value="10" />'.
                '<input type="submit" name="my_submit" value="title_20" />'.
                '<input type="password" name="my_password" value="30" />'.
                '<input type="reset" name="my_reset" value="any_value" />';
    $this->assertEqual($out, $expected);
  }  
}
