<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/components/form/form.inc.php');

class WactSelectWithGroupedOptionsTagTest extends WactTemplateTestCase
{
  function testOptGroupsRenderedCorrectlyWithSingleSelectedOption()
  {
    $template = '<form id="my_form" runat="server">'.
                '<select_with_grouped_options id="my_selector">'.
                '</select_with_grouped_options>'.
                '</form>';

    $this->registerTestingTemplate('/form/select_with_grouped_options/simple.html', $template);

    $page = $this->initTemplate('/form/select_with_grouped_options/simple.html');

    $form = $page->getChild('my_form');
    $form->registerDatasource(array('my_selector' => $selected_value = 10));

    $options = array(array('label' => 'Names',
                           'options' => array(5 => 'Ivan', 10 => 'Mike')),
                     array('label' => 'Last Names',
                           'other_option' => 'any_value',
                           'options' => array(1 => 'Voronov', 2 => 'Kirov')));

    $select = $page->getChild('my_selector');
    $select->registerDataset($options);

    $expected = '<form id="my_form"><select id="my_selector" name="my_selector">'.
                '<optgroup label="&nbsp;Names" >'.
                '<option value="5">Ivan</option><option value="10" selected="selected">Mike</option>'.
                '</optgroup>'.
                '<optgroup label="&nbsp;Last Names" other_option="any_value" >'.
                '<option value="1">Voronov</option><option value="2">Kirov</option>'.
                '</optgroup>'.
                '</select></form>';

    $this->assertEqual($page->capture(), $expected);
  }

  function testOptGroupsRenderedCorrectlyWithNestedOptions()
  {
    $template = '<form id="my_form" runat="server">'.
                '<select_with_grouped_options id="my_selector">'.
                '</select_with_grouped_options>'.
                '</form>';

    $this->registerTestingTemplate('/form/select_with_grouped_options/nested_options.html', $template);

    $page = $this->initTemplate('/form/select_with_grouped_options/nested_options.html');

    $form = $page->getChild('my_form');
    $form->registerDatasource(array('my_selector' => $selected_value = 4));

    $options = array(array('label' => 'Groups',
                           'options' => array(array('label' => 'Students',
                                                    'options' => array(3 => 'Sherbakov', 4 => 'Vasiliev')))));

    $select = $page->getChild('my_selector');
    $select->registerDataset($options);

    $expected = '<form id="my_form"><select id="my_selector" name="my_selector">'.
                '<optgroup label="&nbsp;Groups" >'.
                '<optgroup label="&nbsp;&nbsp;Students" >'.
                '<option value="3">Sherbakov</option><option value="4" selected="selected">Vasiliev</option>'.
                '</optgroup>'.
                '</optgroup>'.
                '</select></form>';

    $this->assertEqual($page->capture(), $expected);
  }
}

