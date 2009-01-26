<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class WactSelectOptionsSourceTagTest extends WactTemplateTestCase
{
  function testTargetNotFound()
  {
    $template = '<core:DATASOURCE id="data">' .
                '<select:OPTIONS_SOURCE target="select" from="source"/>' .
                '</core:DATASOURCE>';

    $this->registerTestingTemplate('/tags/form/select_options_source/error.html', $template);

    try
    {
      $page = $this->initTemplate('/tags/form/select_options_source/error.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Could not find component/', $e->getMessage());
    }
  }

  function testTargetIsNotSupported()
  {
    $template = '<core:DATASOURCE id="data">' .
                '<select:OPTIONS_SOURCE target="select" from="source"/>' .
                '<core:DATASOURCE id="select"></core:DATASOURCE>' .
                '</core:DATASOURCE>';

    $this->registerTestingTemplate('/tags/form/select_options_source/not_supported.html', $template);

    try
    {
      $page = $this->initTemplate('/tags/form/select_options_source/not_supported.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Select tag not found/', $e->getMessage());
    }
  }

  function testTakeOptionsFrom()
  {
    $template = '<core:DATASOURCE id="data">' .
                '<select:OPTIONS_SOURCE target="select" from="{$^source}"/>' .
                '<form runat="server">' .
                '<select id="select" name="select"></select>' .
                '</form>' .
                '</core:DATASOURCE>';

    $this->registerTestingTemplate('/tags/form/select_options_source/from.html', $template);

    $page = $this->initTemplate('/tags/form/select_options_source/from.html');

    $data = $page->getChild('data');
    $data->set('source', $options = array('4' => 'red', '5' => 'blue'));

    $this->assertEqual($page->capture(),
                       '<form>'.
                       '<select id="select" name="select"><option value="4">red</option><option value="5">blue</option></select>'.
                       '</form>');
  }

  function testOptionsViaRegisterDataSet()
  {
    $template = '<select:OPTIONS_SOURCE id="source" target="select"/>' .
                '<form runat="server">' .
                '<select id="select" name="select"></select>' .
                '</form>';

    $this->registerTestingTemplate('/tags/form/select_options_source/register_dataset.html', $template);

    $page = $this->initTemplate('/tags/form/select_options_source/register_dataset.html');

    $data = $page->getChild('source');

    $data->registerDataSet($options = array(array('4' => 'red'), array('5' => 'blue')));

    $this->assertEqual($page->capture(),
                       '<form>'.
                       '<select id="select" name="select"><option value="4">red</option><option value="5">blue</option></select>'.
                       '</form>');
  }

  function testOptionsUseNameAndId()
  {
    $template = '<select:OPTIONS_SOURCE id="source" target="select" use_as_name="name" use_as_id="id"/>' .
                '<form runat="server">' .
                '<select id="select" name="select"></select>' .
                '</form>';

    $this->registerTestingTemplate('/tags/form/select_options_source/use_name_and_id.html', $template);

    $page = $this->initTemplate('/tags/form/select_options_source/use_name_and_id.html');

    $data = $page->getChild('source');

    $data->registerDataSet($options = array(array('id' => '4', 'name' => 'red'),
                                            array('id' => '5', 'name' => 'blue')));

    $this->assertEqual($page->capture(),
                       '<form>'.
                       '<select id="select" name="select"><option value="4">red</option><option value="5">blue</option></select>'.
                       '</form>');
  }

  function testSeveralTargets()
  {
    $template = '<select:OPTIONS_SOURCE id="source" target="select1,select2" use_as_name="name" use_as_id="id"/>' .
                '<form runat="server">' .
                '<select id="select1" name="select1"></select>' .
                '<select id="select2" name="select2"></select>' .
                '</form>';

    $this->registerTestingTemplate('/tags/form/select_options_source/several_targets.html', $template);

    $page = $this->initTemplate('/tags/form/select_options_source/several_targets.html');

    $data = $page->getChild('source');

    $data->registerDataSet($options = array(array('id' => '4', 'name' => 'red'),
                                            array('id' => '5', 'name' => 'blue')));

    $this->assertEqual($page->capture(),
                       '<form>'.
                       '<select id="select1" name="select1"><option value="4">red</option><option value="5">blue</option></select>'.
                       '<select id="select2" name="select2"><option value="4">red</option><option value="5">blue</option></select>'.
                       '</form>');
  }

  function testWithDefaultOption()
  {
    $template = '<select:OPTIONS_SOURCE id="source" target="select" use_as_name="name" use_as_id="id" default_value="-1" default_name="Select something"/>' .
                '<form runat="server">' .
                '<select id="select" name="select"></select>' .
                '</form>';

    $this->registerTestingTemplate('/tags/form/select_options_source/with_default.html', $template);

    $page = $this->initTemplate('/tags/form/select_options_source/with_default.html');

    $data = $page->getChild('source');

    $data->registerDataSet($options = array(array('id' => '4', 'name' => 'red'),
                                            array('id' => '5', 'name' => 'blue')));

    $this->assertEqual($page->capture(),
                       '<form>'.
                       '<select id="select" name="select"><option value="-1">Select something</option><option value="4">red</option><option value="5">blue</option></select>'.
                       '</form>');
  }

  function testOptionsViaRegisterDatasourceAndDefaultOption()
  {
    $template = '<select:OPTIONS_SOURCE id="source" target="select" default_value="0" default_name="select"/>' .
                '<form runat="server">' .
                '<select id="select" name="select"></select>' .
                '</form>';

    $this->registerTestingTemplate('/tags/form/select_options_source/register_datasource_and_default_option.html', $template);

    $page = $this->initTemplate('/tags/form/select_options_source/register_datasource_and_default_option.html');

    $data = $page->getChild('source');

    $data->registerDatasource($options = array('4' => 'red', '5' => 'blue'));

    $this->assertEqual($page->capture(),
                       '<form>'.
                       '<select id="select" name="select"><option value="" selected="selected">select</option><option value="4">red</option><option value="5">blue</option></select>'.
                       '</form>');
  }
}

