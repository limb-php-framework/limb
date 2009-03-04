<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/components/form/form.inc.php');

class WactFormMultipleTagTest extends WactTemplateTestCase
{
  function testChildFormElementsNamesWrapping()
  {
    $template = '<form_multiple id="testForm" name="testForm">
                    <label id="testLabel" name="label" for="testId">A label</label>
                    <input id="testInput" name="testInput" type="text"/>
                </form_multiple>';

    $this->registerTestingTemplate('/form/multiple/elements_name_wrapping.html', $template);

    $page = $this->initTemplate('/form/multiple/elements_name_wrapping.html');

    $input = $page->getChild('testInput');
    $label = $page->getChild('testLabel');

    $this->assertEqual($input->getAttribute('name'), 'testForm[testInput]');
    $this->assertEqual($label->getAttribute('name'), 'label');
  }

  function testChildFormElementsNamesWrappingFilter()
  {
    $template = '<form_multiple id="testForm" name="testForm">
                    <input id="testInput" name="{$\'testInput\'|uppercase}" type="text"/>
                </form_multiple>';

    $this->registerTestingTemplate('/form/multiple/elements_name_wrapping_filter.html', $template);

    $page = $this->initTemplate('/form/multiple/elements_name_wrapping_filter.html');

    $input = $page->getChild('testInput');

    $this->assertEqual($input->getAttribute('name'), 'testForm[TESTINPUT]');
  }

  function testChildFormElementsNamesWrappingDBE()
  {
    $template = '<core:SET name="testinput" runtime="false"/>
                <form_multiple id="testForm" name="testForm">
                    <input id="testInput" name="{$^name|uppercase}" type="text"/>
                </form_multiple>';

    $this->registerTestingTemplate('/form/multiple/elements_name_wrapping_dbe.html', $template);

    $page = $this->initTemplate('/form/multiple/elements_name_wrapping_dbe.html');

    $input = $page->getChild('testInput');

    $this->assertEqual($input->getAttribute('name'), 'testForm[TESTINPUT]');
  }

  function testChildFormElementsComplicatedNamesWrapping()
  {
    $template = '<form_multiple id="testForm" name="testForm">
                    <input id="testInput" name="wow[wrap][testInput]" type="text"/>
                </form_multiple>';

    $this->registerTestingTemplate('/form/multiple/elements_name_wrapping_comples.html', $template);

    $page = $this->initTemplate('/form/multiple/elements_name_wrapping_comples.html');

    $input = $page->getChild('testInput');

    $this->assertEqual($input->getAttribute('name'), 'testForm[wow][wrap][testInput]');
  }

  function testElementsNamesWrappingInDeeperComponent()
  {
    $template = '<form_multiple id="testForm" name="testForm">
                     <div id="wow" runat="server">
                     <input id="testInput" name="testInput" type="text"/>
                    </div>
                </form_multiple>';

    $this->registerTestingTemplate('/form/multiple/elements_name_wrapping_in_deeper_component.html', $template);

    $page = $this->initTemplate('/form/multiple/elements_name_wrapping_in_deeper_component.html');

    $input = $page->getChild('testInput');

    $this->assertEqual($input->getAttribute('name'), 'testForm[testInput]');
  }

  function testRenderAsCommonForm()
  {
    $template = '<form_multiple id="testForm" name="testForm"></form_multiple>';

    $this->registerTestingTemplate('/form/multiple/render_as_regular_form.html', $template);

    $page = $this->initTemplate('/form/multiple/render_as_regular_form.html');
    $result = $page->capture();

    $this->assertEqual($result, '<form id="testForm" name="testForm"></form>');
  }

  function testFormComponentsReceiveValues()
  {
    $template = '<form_multiple id="testForm" name="testForm">
                    <input id="testInput" name="testInput" type="text"/>
                </form_multiple>';

    $this->registerTestingTemplate('/form/multiple/form_elements_accept_values.html', $template);

    $page = $this->initTemplate('/form/multiple/form_elements_accept_values.html');

    $form = $page->getChild('testForm');

    $form->registerDatasource(array('testInput' =>  'Hello'));

    $input = $page->getChild('testInput');
    $this->assertEqual($input->getValue(), 'Hello');
  }
}

