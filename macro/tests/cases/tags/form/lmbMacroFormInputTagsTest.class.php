<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2012 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMacroFormInputTagsTest extends lmbBaseMacroTest
{
  function testTypeText()
  {
    $template = '{{form:text name="my_input" value="$#var"}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('var', 100);

    $out = $page->render();
    $this->assertEqual($out, '<input name="my_input" type="text" value="100" />');
  }

  function testTypeTextRendersValueAttributeInAnyCase()
  {
    $template = '{{form:text name="my_input"}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');

    $out = $page->render();
    $this->assertEqual($out, '<input name="my_input" type="text" value="" />');
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
    $template = '{{form:hidden name="my_hidden" value="$#for_hidden"}}'.
                '{{form:button name="my_button" value="$#for_button"}}'.
                '{{form:image src="some_path" name="my_image" value="$#for_image"}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('for_hidden', 10);
    $page->set('for_button', 20);
    $page->set('for_image', 30);

    $out = $page->render();
    $expected = '<input name="my_hidden" type="hidden" value="10" />'.
                '<input name="my_button" type="button" value="20" />'.
                '<input src="some_path" name="my_image" type="image" value="30" />';
    $this->assertEqual($out, $expected);
  }

  function testTypesFileAndSubmitAndPasswordAndResetAndFile_DontTakeValueFromFormDatasource()
  {
    $template = '{{form name="my_form" from="$#form_data"}}'.
                '{{form:file name="my_file"}}'.
                '{{form:submit name="my_submit"}}'.
                '{{form:password name="my_password"}}'.
                '{{form:reset name="my_reset"}}'.
                '{{/form}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('form_data', array('my_file' => 10, 'my_submit' => 20, 'my_password' => 30, 'my_reset' => 40));

    $out = $page->render();
    $expected = '<form name="my_form"><input name="my_file" type="file" />'.
                '<input name="my_submit" type="submit" />'.
                '<input name="my_password" type="password" />'.
                '<input name="my_reset" type="reset" /></form>';
    $this->assertEqual($out, $expected);
  }

  function testTypesFileAndSubmitAndPasswordAndResetAndFile_MayRenderValueAttribute()
  {
    $template = '{{form:file name="my_file" value="$#my_file"}}'.
                '{{form:submit name="my_submit" value="title_{$#my_submit}"}}'.
                '{{form:password name="my_password" value="$#my_password"}}'.
                '{{form:reset name="my_reset" value="any_value"}}';

    $page = $this->_createMacroTemplate($template, 'tpl.html');
    $page->set('my_file', 10);
    $page->set('my_submit', 20);
    $page->set('my_password', 30);

    $out = $page->render();
    $expected = '<input name="my_file" type="file" value="10" />'.
                '<input name="my_submit" type="submit" value="title_20" />'.
                '<input name="my_password" type="password" value="30" />'.
                '<input name="my_reset" value="any_value" type="reset" />';
    $this->assertEqual($out, $expected);
  }
}
