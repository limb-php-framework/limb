<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once 'limb/wact/src/components/form/form.inc.php';

class WactSelectMultipleComponentTest extends WactTemplateTestCase
{
  function testGetValue()
  {
    $template = '<form id="testForm" runat="server">
                        <select id="test" name="multiple[]" runat="server" multiple="true"></select>
                    </form>';
    $this->registerTestingTemplate('/components/form/selectmultiple/getvalue.html', $template);

    $page = $this->initTemplate('/components/form/selectmultiple/getvalue.html');

    $choices = array('red'=>'','green'=>'','blue'=>'');

    $select = $page->getChild('test');

    $select->setSelection($choices);

    $this->assertEqual($select->getValue(),$choices);
  }

  function testSetChoicesWithIndex()
  {
    $template = '<form id="testForm" runat="server">
                      <select id="test" name="multiple[]" runat="server" multiple="true"></select>
                  </form>';
    $this->registerTestingTemplate('/components/form/selectmultiple/setchoiceswithindex.html', $template);

    $page = $this->initTemplate('/components/form/selectmultiple/setchoiceswithindex.html');

    $choices = array('red','green','blue');

    /**
    * Note this test must fail right now
    * Reflects problem with array key 0 in choices get automatically
    * selected
    */
    $testOut = '';
    foreach ( $choices as $key => $choice ) {
      $testOut .= '<option value="'.$key.'"';
      $testOut .='>'.$choice.'</option>';
    }

    $select = $page->getChild('test');

    $select->setChoices($choices);

    ob_start();
    $select->renderContents();
    $out = ob_get_contents();
    ob_end_clean();

    $this->assertEqual($out,$testOut);
  }

  function testSetChoicesWithIndexSelected()
  {
    $template = '<form id="testForm" runat="server">
                      <select id="test" name="multiple[]" runat="server" multiple="true"></select>
                  </form>';
    $this->registerTestingTemplate('/components/form/selectmultiple/setchoiceswithindexselected.html', $template);

    $page = $this->initTemplate('/components/form/selectmultiple/setchoiceswithindexselected.html');

    $choices = array('red','green','blue');
    $selected = array(0,1,2);

    $testOut = '';
    foreach ( $choices as $key => $choice ) {
      $testOut .= '<option value="'.$key.'"';
      if ( in_array($key,$selected) ) {
        $testOut .= ' selected="true"';
      }
      $testOut .='>'.$choice.'</option>';
    }

    $select = $page->getChild('test');

    $select->setChoices($choices);
    $select->setSelection($selected);

    ob_start();
    $select->renderContents();
    $out = ob_get_contents();
    ob_end_clean();

    $this->assertEqual($out,$testOut);
  }

  /**
  * This test is included to "simulate" a selection received via POST - remember
  * PHP will convert integers to strings so strict type checking in the select components
  * for selected values will fail
  */
  function testSetChoicesWithIndexSelectedByForm()
  {
    $template = '<form id="testForm" runat="server">
                      <select id="test" name="multiple[]" runat="server" multiple="true"></select>
                  </form>';
    $this->registerTestingTemplate('/components/form/selectmultiple/setchoiceswithindexselectedbyform.html', $template);

    $page = $this->initTemplate('/components/form/selectmultiple/setchoiceswithindexselectedbyform.html');

    $form = $page->getChild('testForm');

    $choices = array('red','green','blue');
    $selected = array(0,1,2);

    // The selected options will be string values, when a form is actually submitted!
    $selectedToString = array('0','1','2');

    $testOut = '';
    foreach ( $choices as $key => $choice ) {
      $testOut .= '<option value="'.$key.'"';
      if ( in_array($key,$selected) ) {
        $testOut .= ' selected="true"';
      }
      $testOut .='>'.$choice.'</option>';
    }

    $select = $page->getChild('test');

    $select->setChoices($choices);

    // The selected values typed as strings
    $data = new ArrayObject(array('multiple' => $selectedToString));
    $form->registerDataSource($data);

    ob_start();
    $select->renderContents();
    $out = ob_get_contents();
    ob_end_clean();

    $this->assertEqual($out,$testOut);
  }

  function testSetSelectionWithFormValueAsObject()
  {
    $template = '<form id="testForm" runat="server">
                      <select id="test" name="mySelect[]" multiple="true" runat="server"></select>
                  </form>';
    $this->registerTestingTemplate('/components/form/selectmultiple/set_selection_with_form_value_as_object.html', $template);

    $page = $this->initTemplate('/components/form/selectmultiple/set_selection_with_form_value_as_object.html');

    $choices = array(1 => 'red',2 => 'green',3 => 'blue');
    $select = $page->getChild('test');
    $select->setChoices($choices);
    $object1 = new ArrayObject(array('id' => 2));
    $object2 = new ArrayObject(array('id' => 3));
    $select->setSelection(array($object1, $object2));

    $output = $page->capture();
    $this->assertWantedPattern('~<form[^>]+id="testForm"[^>]*>.*</form>$~ims', $output);
    $this->assertWantedPattern('~<select[^>]+id="test"[^>]*>(\s*<option\svalue="[1-3]"[^>]*>[^<]*</option>)+.*</select>~ims', $output);
    $this->assertWantedPattern('~<option[^>]+value="2"[^>]+selected[^>]*>green</option>~ims', $output);
    $this->assertWantedPattern('~<option[^>]+value="3"[^>]+selected[^>]*>blue</option>~ims', $output);
  }

  function testSetSelectionWithFormValueAsObjectWithSelectField()
  {
    $template = '<form id="testForm" runat="server">
                      <select id="test" name="mySelect[]" multiple="true" select_field="my_id" ></select>
                  </form>';
    $this->registerTestingTemplate('/components/form/selectmultiple/set_selection_with_form_value_as_object_with_select_field.html', $template);

    $page = $this->initTemplate('/components/form/selectmultiple/set_selection_with_form_value_as_object_with_select_field.html');

    $choices = array(1 => 'red',2 => 'green',3 => 'blue');
    $select = $page->getChild('test');
    $select->setChoices($choices);
    $object1 = new ArrayObject(array('my_id' => 2));
    $object2 = new ArrayObject(array('my_id' => 3));
    $select->setSelection(array($object1, $object2));

    $output = $page->capture();
    $this->assertWantedPattern('~<form[^>]+id="testForm"[^>]*>.*</form>$~ims', $output);
    $this->assertWantedPattern('~<select[^>]+id="test"[^>]*>(\s*<option\svalue="[1-3]"[^>]*>[^<]*</option>)+.*</select>~ims', $output);
    $this->assertWantedPattern('~<option[^>]+value="2"[^>]+selected[^>]*>green</option>~ims', $output);
    $this->assertWantedPattern('~<option[^>]+value="3"[^>]+selected[^>]*>blue</option>~ims', $output);
  }

  function testSelectUseOptionsListWithDefaultSelectedOption()
  {
    $template = '<form runat="server">'.
                  '<select id="test" name="mySelect[]" runat="server" multiple="true">'.
                  '<option value="foo" selected="true">"test1"</option>'.
                  '<option value="bar">\'test2\'</option>'.
                  '<option value="zoo" selected="true">test3</option>'.
                  '</select>'.
                '</form>';
    $expected_template =
                '<form>'.
                  '<select id="test" name="mySelect[]" multiple="true">'.
                  '<option value="foo" selected="true">&quot;test1&quot;</option>'.
                  '<option value="bar">\&#039;test2\&#039;</option>'.
                  '<option value="zoo" selected="true">test3</option>'.
                  '</select>'.
                '</form>';
    $this->registerTestingTemplate('/tags/form/controls/selectmultiple/select_with_options_default.html', $template);
    $page = $this->initTemplate('/tags/form/controls/selectmultiple/select_with_options_default.html');

    $output = $page->capture();
    $this->assertEqual($output, $expected_template);
  }

  function testSelectUseOptionsListWithSelectedOption()
  {
    $template = '<form name="my_form" runat="server">'.
                  '<select id="test" name="mySelect[]" runat="server" multiple="true">'.
                  '<option value="1">test1</option>'.
                  '<option value="2" selected="true">test2</option>'.
                  '<option value="3">test3</option>'.
                  '</select>'.
                '</form>';
    $expected_template =
                '<form name="my_form">'.
                  '<select id="test" name="mySelect[]" multiple="true">'.
                  '<option value="1" selected="true">test1</option>'.
                  '<option value="2">test2</option>'.
                  '<option value="3" selected="true">test3</option>'.
                  '</select>'.
                '</form>';
    $this->registerTestingTemplate('/tags/form/controls/selectmultiple/select_with_options.html', $template);
    $page = $this->initTemplate('/tags/form/controls/selectmultiple/select_with_options.html');

    $form = $page->getChild('my_form');
    $form->setValue('mySelect', array(1,3));

    $output = $page->capture();
    $this->assertEqual($output, $expected_template);
  }
}

