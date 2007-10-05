<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once 'limb/wact/src/components/form/form.inc.php';

class WactSelectSingleComponentTest extends WactTemplateTestCase
{
  function testSetChoicesDontSelectByDefault()
  {
    $template = '<form id="testForm" runat="server">
                      <select id="test" name="mySelect" runat="server"></select>
                  </form>';
    $this->registerTestingTemplate('/tags/form/select_single/setchoiceswithindex.html', $template);

    $page = $this->initTemplate('/tags/form/select_single/setchoiceswithindex.html');

    $choices = array('red','green','blue');
    $Select = $page->getChild('test');
    $Select->setChoices($choices);

    $output = $page->capture();
    $this->assertWantedPattern('~<form[^>]+id="testForm"[^>]*>.*</form>$~ims', $output);
    $this->assertWantedPattern('~<select[^>]+id="test"[^>]*>(\s*<option\svalue="\d+"[^>]*>[^<]*</option>)+.*</select>~ims', $output);
    $this->assertNoPattern('~<option\s+value="0"(?U)[^>]*selected[^>]*>[^<]*</option>~ims', $output);
  }

  function testSetChoicesWithHash()
  {
    $template = '<form id="testForm" runat="server">
                      <select id="test" name="mySelect" runat="server"></select>
                  </form>';
    $this->registerTestingTemplate('/tags/form/select_single/setchoiceswithhash.html', $template);

    $page = $this->initTemplate('/tags/form/select_single/setchoiceswithhash.html');

    $choices = array('a'=>'red', 'b'=>'green', 'c'=>'blue');
    $Select = $page->getChild('test');
    $Select->setChoices($choices);

    $output = $page->capture();
    $this->assertWantedPattern('~<form[^>]+id="testForm"[^>]*>.*</form>$~ims', $output);
    $this->assertWantedPattern('~<select[^>]+id="test"[^>]*>(\s*<option\svalue="[a-c]"[^>]*>[^<]*</option>)+.*</select>~ims', $output);
  }

  function testSetSelectionWithIndex()
  {
    $template = '<form id="testForm" runat="server">
                      <select id="test" name="mySelect" runat="server"></select>
                  </form>';
    $this->registerTestingTemplate('/tags/form/select_single/setselectionwithindex.html', $template);

    $page = $this->initTemplate('/tags/form/select_single/setselectionwithindex.html');

    $choices = array('red','green','blue');
    $selectedKey = '1';
    $Select = $page->getChild('test');
    $Select->setChoices($choices);
    $Select->setSelection($selectedKey);

    $output = $page->capture();
    $this->assertWantedPattern('~<form[^>]+id="testForm"[^>]*>.*</form>$~ims', $output);
    $this->assertWantedPattern('~<select[^>]+id="test"[^>]*>(\s*<option\svalue="\d+"[^>]*>[^<]*</option>)+.*</select>~ims', $output);
    $this->assertWantedPattern('~<option[^>]+value="1"[^>]+selected[^>]*>green</option>~ims', $output);
  }

  function testSetSelectionWithIndexByForm()
  {
    $template = '<form id="testForm" runat="server">
                      <select id="test" name="mySelect" runat="server"></select>
                  </form>';
    $this->registerTestingTemplate('/tags/form/select_single/setselectionwithindexbyform.html', $template);

    $page = $this->initTemplate('/tags/form/select_single/setselectionwithindexbyform.html');

    $Form = $page->getChild('testForm');

    $choices = array('red','green','blue');
    $selectedKey = '1';
    $Select = $page->getChild('test');
    $Select->setChoices($choices);

    $data = new ArrayObject(array('mySelect' => $selectedKey));

    $Form->registerDataSource($data);

    $output = $page->capture();
    $this->assertWantedPattern('~<form[^>]+id="testForm"[^>]*>.*</form>$~ims', $output);
    $this->assertWantedPattern('~<select[^>]+id="test"[^>]*>(\s*<option\svalue="\d+"[^>]*>[^<]*</option>)+.*</select>~ims', $output);
    $this->assertWantedPattern('~<option[^>]+value="1"[^>]+selected[^>]*>green</option>~ims', $output);
  }

  function testSetSelectionWithGivenValue()
  {
    $template = '<select id="mySelect" given_value="{$#bar}" runat="server"></select>';;
    $this->registerTestingTemplate('/tags/form/select_single/set_selection_with_given_value.html', $template);

    $page = $this->initTemplate('/tags/form/select_single/set_selection_with_given_value.html');

    $choices = array('red','green','blue');
    $Select = $page->getChild('mySelect');
    $selectedKey = 1;
    $page->set('bar', $selectedKey);
    $Select->setChoices($choices);

    $output = $page->capture();
    $this->assertWantedPattern('~<select[^>]+id="mySelect"[^>]*>(\s*<option\svalue="\d+"[^>]*>[^<]*</option>)+.*</select>~ims', $output);
    $this->assertWantedPattern('~<option[^>]+value="1"[^>]+selected[^>]*>green</option>~ims', $output);
  }

  function testSetSelectionWithHash()
  {
    $template = '<form id="testForm" runat="server">
                      <select id="test" name="mySelect" runat="server"></select>
                  </form>';
    $this->registerTestingTemplate('/tags/form/select_single/setselectionwithhash.html', $template);

    $page = $this->initTemplate('/tags/form/select_single/setselectionwithhash.html');

    $choices = array('a'=>'red','b'=>'green','c'=>'blue');
    $selectedKey = 'b';
    $Select = $page->getChild('test');
    $Select->setChoices($choices);
    $Select->setSelection('b');

    $output = $page->capture();
    $this->assertWantedPattern('~<form[^>]+id="testForm"[^>]*>.*</form>$~ims', $output);
    $this->assertWantedPattern('~<select[^>]+id="test"[^>]*>(\s*<option\svalue="[a-c]"[^>]*>[^<]*</option>)+.*</select>~ims', $output);
    $this->assertWantedPattern('~<option[^>]+value="b"[^>]+selected[^>]*>green</option>~ims', $output);
  }

  function testSetSelectionWithFormValueAsObject()
  {
    $template = '<form id="testForm" runat="server">
                      <select id="test" name="mySelect" runat="server"></select>
                  </form>';
    $this->registerTestingTemplate('/tags/form/select_single/set_selection_with_form_value_as_object.html', $template);

    $page = $this->initTemplate('/tags/form/select_single/set_selection_with_form_value_as_object.html');

    $choices = array(1 => 'red',2 => 'green',3 => 'blue');
    $Select = $page->getChild('test');
    $Select->setChoices($choices);
    $object = new ArrayObject(array('id' => 2));
    $Select->setSelection($object);

    $output = $page->capture();
    $this->assertWantedPattern('~<form[^>]+id="testForm"[^>]*>.*</form>$~ims', $output);
    $this->assertWantedPattern('~<select[^>]+id="test"[^>]*>(\s*<option\svalue="[1-3]"[^>]*>[^<]*</option>)+.*</select>~ims', $output);
    $this->assertWantedPattern('~<option[^>]+value="2"[^>]+selected[^>]*>green</option>~ims', $output);
  }

  function testSetSelectionWithFormValueAsObjectWithSelectField()
  {
    $template = '<form id="testForm" runat="server">
                      <select id="test" name="mySelect" select_field="my_id" ></select>
                  </form>';
    $this->registerTestingTemplate('/tags/form/select_single/set_selection_with_form_value_as_object_with_select_field.html', $template);

    $page = $this->initTemplate('/tags/form/select_single/set_selection_with_form_value_as_object_with_select_field.html');

    $choices = array(1 => 'red',2 => 'green',3 => 'blue');
    $Select = $page->getChild('test');
    $Select->setChoices($choices);
    $object = new ArrayObject(array('my_id' => 2));
    $Select->setSelection($object);

    $output = $page->capture();
    $this->assertWantedPattern('~<form[^>]+id="testForm"[^>]*>.*</form>$~ims', $output);
    $this->assertWantedPattern('~<select[^>]+id="test"[^>]*>(\s*<option\svalue="[1-3]"[^>]*>[^<]*</option>)+.*</select>~ims', $output);
    $this->assertWantedPattern('~<option[^>]+value="2"[^>]+selected[^>]*>green</option>~ims', $output);
  }

  function testMixedOptionsWithZeroKey()
  {
    $template = '<form id="testForm" runat="server">
                      <select id="test" name="mySelect" select_field="my_id" ></select>
                  </form>';
    $this->registerTestingTemplate('/tags/form/select_single/set_selection_with_zero.html', $template);

    $page = $this->initTemplate('/tags/form/select_single/set_selection_with_zero.html');

    $choices = array(0 => '--', 'red' => 'R', 'green' => 'G', 'blue' => 'B');
    $Select = $page->getChild('test');
    $Select->setChoices($choices);
    $object = new ArrayObject(array('my_id' => 'green'));
    $Select->setSelection($object);

    $output = $page->capture();
    $this->assertWantedPattern('~<form[^>]+id="testForm"[^>]*>.*</form>$~ims', $output);
    $this->assertWantedPattern('~<option[^>]+value="green"[^>]+selected[^>]*>G</option>~ims', $output);
    $this->assertNoPattern('~<option[^>]+value="0"[^>]+selected[^>]*>--</option>~ims', $output);
  }


  /************************************************************
   Tests below use the API as it's expected to be used
   ************************************************************/

  function testSetChoicesWithKeys()
  {
    $template = '<form id="testForm" runat="server">
                      <select id="test" name="mySelect" runat="server"></select>
                  </form>';
    $this->registerTestingTemplate('/tags/form/select_single/setchoiceswithkeys.html', $template);

    $page = $this->initTemplate('/tags/form/select_single/setchoiceswithkeys.html');

    $choices = array('red'=>'','green'=>'','blue'=>'');

    $testOut = '';
    foreach ( $choices as $key => $choice ) {
      $testOut .= '<option value="'.$key.'"';
      $testOut .='>'.$key.'</option>';
    }

    $Select = $page->getChild('test');

    // Array flip?
    $Select->setChoices($choices);

    ob_start();
    $Select->renderContents();
    $out = ob_get_contents();
    ob_end_clean();

    $this->assertEqual($out,$testOut);

  }

  function testSetChoicesWithKeysSelection()
  {
    $template = '<form id="testForm" runat="server">
                      <select id="test" name="mySelect" runat="server"></select>
                  </form>';
    $this->registerTestingTemplate('/tags/form/select_single/setchoiceswithkeysselection.html', $template);

    $page = $this->initTemplate('/tags/form/select_single/setchoiceswithkeysselection.html');

    $choices = array('red'=>'','green'=>'','blue'=>'');
    $selected = 'green';

    $testOut = '';

    foreach ($choices as $key => $choice )
    {
      $testOut .= '<option value="'.$key.'"';
      if ( $key == $selected ) {
        $testOut .= ' selected="true"';
      }
      $testOut .='>'.$key.'</option>';
    }

    $Select = $page->getChild('test');

    $Select->setChoices($choices);

    $Select->setSelection($selected);

    ob_start();
    $Select->renderContents();
    $out = ob_get_contents();
    ob_end_clean();

    $this->assertEqual($out,$testOut);
  }

  function testSelectUseOptionsListWithDefaultSelectedOption()
  {
    $template = '<form runat="server">'.
                  '<select id="test" name="mySelect" runat="server">'.
                  '<option value="1">"test1"</option>'.
                  '<option value="2" selected="true">\'test2\'</option>'.
                  '</select>'.
                '</form>';
    $expected_template =
                '<form>'.
                  '<select id="test" name="mySelect">'.
                  '<option value="1">&quot;test1&quot;</option>'.
                  '<option value="2" selected="true">\&#039;test2\&#039;</option>'.
                  '</select>'.
                '</form>';
    $this->registerTestingTemplate('/tags/form/controls/select/select_with_options_default.html', $template);
    $page = $this->initTemplate('/tags/form/controls/select/select_with_options_default.html');

    $output = $page->capture();
    $this->assertEqual($output, $expected_template);
  }

  function testMergeOptionsListFromTagContentWithOtherOptions()
  {
    $template = '<form runat="server">'.
                  '<select id="test" name="mySelect" runat="server">'.
                  '<option value="1" prepend="true">"test1"</option>'.
                  '<option value="4" selected="true">\'test4\'</option>'.
                  '</select>'.
                '</form>';
    $expected_template =
                '<form>'.
                  '<select id="test" name="mySelect">'.
                  '<option value="1">&quot;test1&quot;</option>'.
                  '<option value="2">test2</option>'.
                  '<option value="3">test3</option>'.
                  '<option value="4" selected="true">\&#039;test4\&#039;</option>'.
                  '</select>'.
                '</form>';
    $this->registerTestingTemplate('/tags/form/controls/select/merge_select_options.html', $template);
    $page = $this->initTemplate('/tags/form/controls/select/merge_select_options.html');

    $select = $page->getChild('test');
    $select->setChoices(array('2' => 'test2', '3' => 'test3'));

    $output = $page->capture();
    $this->assertEqual($output, $expected_template);
  }

  function testSelectUseOptionsListWithSelectedOption()
  {
    $template = '<form name="my_form" runat="server">'.
                  '<select id="test" name="mySelect" runat="server">'.
                  '<option value="1">test1</option>'.
                  '<option value="2">test2</option>'.
                  '</select>'.
                '</form>';
    $expected_template =
                '<form name="my_form">'.
                  '<select id="test" name="mySelect">'.
                  '<option value="1">test1</option>'.
                  '<option value="2" selected="true">test2</option>'.
                  '</select>'.
                '</form>';
    $this->registerTestingTemplate('/tags/form/controls/select/select_with_options.html', $template);
    $page = $this->initTemplate('/tags/form/controls/select/select_with_options.html');

    $form = $page->getChild('my_form');
    $form->setValue('mySelect', 2);

    $output = $page->capture();
    $this->assertEqual($output, $expected_template);
  }
}

