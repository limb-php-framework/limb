<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/wact/src/components/fetch/WactFetcher.interface.php');
require_once('limb/wact/src/components/WactArrayIteratorDecorator.class.php');

class TestingFetchTagsDataset extends WactArrayIterator
{
  function sort($sort_params)
  {
    throw new WactException('Sorting is not implemented', array('sort_params' => $sort_params));
  }
}

class TestingTemplateDatasetDecorator extends WactArrayIteratorDecorator
{
  var $prefix1;
  var $prefix2;
  var $sort_params;

  function setPrefix1($prefix)
  {
    $this->prefix1 = $prefix;
  }

  function setPrefix2($prefix)
  {
    $this->prefix2 = $prefix;
  }

  function sort($sort_params)
  {
    $this->sort_params = $sort_params;
  }

  function current()
  {
    $record = parent :: current();
    $data = $record;
    $data['full'] = $this->prefix1 . $data['title'] . '-' . $data['description'] . $this->prefix2;
    return new ArrayObject($data);
  }
}

class TestingFetchTagsDatasetFetcher implements WactFetcher
{
  static $stub_dataset;
  protected $extra_param;

  function fetch()
  {
    $result = array();
    foreach(self :: $stub_dataset as $key => $value)
    {
      $result[$key] = $value;
      if($this->extra_param)
        $result[$key]['param'] = $this->extra_param;
    }

    return new TestingFetchTagsDataset($result);
  }

  function setExtraParam($value)
  {
    $this->extra_param = $value;
  }

  static function setStubDataset($dataset)
  {
    self :: $stub_dataset = $dataset;
  }
}

class WactFetchTagTest extends WactTemplateTestCase
{
  function setUp()
  {
    parent :: setUp();

    $dataset =  array(array('title' => 'joe', 'description' => 'fisher'),
                      array('title' => 'ivan', 'description' => 'gamer'));

    TestingFetchTagsDatasetFetcher :: setStubDataset($dataset);
  }

  function testSaveDatasetToVar()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" to="data"/>' .
                '<list:LIST from="data"><list:ITEM>{$title}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/save_dataset_to_var.html', $template);

    $page = $this->initTemplate('/tags/fetch/save_dataset_to_var.html');

    $this->assertEqual(trim($page->capture()), 'joe|ivan|');
  }

  function testPathBasedTargetVarsAreNotSupported()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" to="path.to.var"/>' .
                '<list:LIST from="data"><list:ITEM>{$title}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/buffer_path_based_vars_are_not_supported.html', $template);

    try
    {
      $page = $this->initTemplate('/tags/fetch/buffer_path_based_vars_are_not_supported.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Path based variable name is not supported in buffer attribute/', $e->getMessage());
      $this->assertEqual($e->getParam('expression'), 'path.to.var');
    }
  }

  function testSaveDatasetToVarInDatasource()
  {
    $template = '<core:datasource id="data"/><fetch using="TestingFetchTagsDatasetFetcher" to="[data]var1"/>' .
                '<list:LIST from="{$#[data]var1}"><list:ITEM>{$title}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/save_dataset_to_datasource_buffer.html', $template);

    $page = $this->initTemplate('/tags/fetch/save_dataset_to_datasource_buffer.html');

    $this->assertEqual(trim($page->capture()), 'joe|ivan|');
  }

  function testSaveDatasetToVarInWrongDatasourceThrowException()
  {
    $template = '<list:list id="data"/><fetch using="TestingFetchTagsDatasetFetcher" to="[data]var1"/>' .
                '<list:LIST from="{$[data]var1}"><list:ITEM>{$title}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/save_dataset_to_var_in_wrong_datasource.html', $template);

    try
    {
      $page = $this->initTemplate('/tags/fetch/save_dataset_to_var_in_wrong_datasource.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/None existing expression datasource context/', $e->getMessage());
      $this->assertEqual($e->getParam('expression'), '[data]var1');
    }
  }

  function testSaveDatasetToListList()
  {
    $template = '<list:list id="data"/><fetch using="TestingFetchTagsDatasetFetcher" to="[data]"/>' .
                 '<list:LIST from="{$#[data]}"><list:ITEM>{$title}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/save_dataset_to_list_list.html', $template);

    $page = $this->initTemplate('/tags/fetch/save_dataset_to_list_list.html');

    $this->assertEqual(trim($page->capture()), 'joe|ivan|');
  }

  function testSaveDatasetToNonExistingTargetDatasourceThrowException()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" to="[no_such_datasource]"/>' .
                '<list:LIST from="{$[data]}"><list:ITEM>{$title}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/save_dataset_to_none_existing_target_datasource.html', $template);

    try
    {
      $page = $this->initTemplate('/tags/fetch/save_dataset_to_none_existing_target_datasource.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/None existing expression datasource context/', $e->getMessage());
    }
  }

  function testSaveRecordToDatasource()
  {
    $template = '<core:datasource id="data"/>'.
                '<fetch using="TestingFetchTagsDatasetFetcher" to="[data]" first="true"/>' .
                '<core:datasource from="[data]">{$title}</core:datasource>';

    $this->registerTestingTemplate('/tags/fetch/save_record_to_datasource.html', $template);

    $page = $this->initTemplate('/tags/fetch/save_record_to_datasource.html');

    $this->assertEqual(trim($page->capture()), 'joe');
  }

  function testSaveRecordToDatasourceVar()
  {
    $template = '<core:datasource id="data"/>'.
                '<fetch using="TestingFetchTagsDatasetFetcher" to="[data]var" first="true"/>' .
                '<core:datasource from="[data]var">{$title}</core:datasource>';

    $this->registerTestingTemplate('/tags/fetch/save_record_to_datasource_var.html', $template);

    $page = $this->initTemplate('/tags/fetch/save_record_to_datasource_var.html');

    $this->assertEqual(trim($page->capture()), 'joe');
  }

  function testMultipleTargets()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" to="[testTarget1],[testTarget2]" />' .
                '<list:LIST id="testTarget1"><list:ITEM>{$title}-</list:ITEM></list:LIST>' .
                '<list:LIST id="testTarget2"><list:ITEM>{$description}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/dataset_multiple_targets.html', $template);

    $page = $this->initTemplate('/tags/fetch/dataset_multiple_targets.html');

    $this->assertEqual($page->capture(), 'joe-ivan-fisher|gamer|');
  }

  function testWithNavigator()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" to="[testTarget]"  navigator="pagenav" />' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}|</list:ITEM></list:LIST>'.
                '<pager:NAVIGATOR id="pagenav" items="10"></pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/fetch/dataset_with_navigator.html', $template);

    $page = $this->initTemplate('/tags/fetch/dataset_with_navigator.html');

    $this->assertEqual($page->capture(), 'joe|ivan|');

    $pager = $page->findChild('pagenav');
    $this->assertEqual($pager->getTotalItems(), 2);
  }

  function testOnlyRecord()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" to="[testTarget]" first="true" />' .
                '<core:datasource id="testTarget">{$title}</core:datasource>';

    $this->registerTestingTemplate('/tags/fetch/dataset_only_record.html', $template);

    $page = $this->initTemplate('/tags/fetch/dataset_only_record.html');

    $this->assertEqual($page->capture(), 'joe');
  }

  function testApplyDecorators()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" to="[testTarget]" >' .
                '<fetch:decorate using="TestingTemplateDatasetDecorator"/>' .
                '</fetch>' .
                '<list:LIST id="testTarget"><list:ITEM>{$full}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/dataset_with_decorators.html', $template);

    $page = $this->initTemplate('/tags/fetch/dataset_with_decorators.html');

    $this->assertEqual(trim($page->capture()), 'joe-fisher|ivan-gamer|');
  }

  function testSingleTargetWithDBEParam()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" to="[testTarget]">' .
                '<fetch:param extra_param="{$#genger}"/>' .
                '</fetch>' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}-{$param}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/dataset_single_target_with_dbe.html', $template);

    $page = $this->initTemplate('/tags/fetch/dataset_single_target_with_dbe.html');
    $page->set('genger', 'Man');

    $this->assertEqual(trim($page->capture()), 'joe-Man|ivan-Man|');
  }

  function testSingleTargetWithComplexDBEParam()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" to="[testTarget]">' .
                '<fetch:param extra_param="{$#request.gender}"/>' .
                '</fetch>' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}-{$param}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/dataset_single_target_with_complex_dbe.html', $template);

    $page = $this->initTemplate('/tags/fetch/dataset_single_target_with_complex_dbe.html');

    $page->set('request', array('gender' => 'Man'));

    $this->assertEqual(trim($page->capture()), 'joe-Man|ivan-Man|');
  }

  function testApplyDecoratorsWithExtraParams()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" to="[testTarget]">' .
                '<fetch:decorate using="TestingTemplateDatasetDecorator" prefix1="Hi-" prefix2="-!!"/>' .
                '</fetch>' .
                '<list:LIST id="testTarget"><list:ITEM>{$full}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/dataset_with_decorators_with_params.html', $template);

    $page = $this->initTemplate('/tags/fetch/dataset_with_decorators_with_params.html');

    $this->assertEqual(trim($page->capture()), 'Hi-joe-fisher-!!|Hi-ivan-gamer-!!|');
  }

  function testOrderParamIsPassedFromParamTag()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" to="[testTarget]" >' .
                '<fetch:param order="title=ASC"/>' .
                '</fetch>' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}-{$description}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/dataset_with_sort_params_by_param_tag.html', $template);

    $page = $this->initTemplate('/tags/fetch/dataset_with_sort_params_by_param_tag.html');

    try
    {
      $page->capture();
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Sorting is not implemented/', $e->getMessage()); // see TestingFetchTagsDataset class at the top
      $this->assertEqual($e->getParam('sort_params'), array('title' => 'ASC'));
    }
  }

  function testOrderParamIsPassedFromFetchTagAttribute()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" to="[testTarget]" order="title"/>' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}-{$description}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/dataset_with_sort_params_by_tag_attribute.html', $template);

    $page = $this->initTemplate('/tags/fetch/dataset_with_sort_params_by_tag_attribute.html');

    try
    {
      $page->capture();
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Sorting is not implemented/', $e->getMessage()); // see TestingFetchTagsDataset class at the top
      $this->assertEqual($e->getParam('sort_params'), array('title' => 'ASC'));
    }
  }

  function testOffsetAndLimitFromParamTag()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" to="[testTarget]">' .
                '<fetch:param offset="1" limit="1"/></fetch>'.
                '<list:LIST id="testTarget"><list:ITEM>{$title}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/dataset_with_offset_limit_by_param_tag.html', $template);

    $page = $this->initTemplate('/tags/fetch/dataset_with_offset_limit_by_param_tag.html');

    $this->assertEqual($page->capture(), 'ivan|');
  }

  function testLimitNoOffsetFromParamTag()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" to="[testTarget]">' .
                '<fetch:param limit="1"/></fetch>'.
                '<list:LIST id="testTarget"><list:ITEM>{$title}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/dataset_with_limit_no_offset_by_param_tag.html', $template);

    $page = $this->initTemplate('/tags/fetch/dataset_with_limit_no_offset_by_param_tag.html');

    $this->assertEqual($page->capture(), 'joe|');
  }

  function testOffsetAndLimitFromFetchTagAttributes()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" to="[testTarget]" offset="1" limit="1" />' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}|</list:ITEM></list:LIST>'.
                '<pager:NAVIGATOR id="pagenav" items="10"></pager:NAVIGATOR>';

    $this->registerTestingTemplate('/tags/fetch/dataset_with_offset_limit_by_attributes.html', $template);

    $page = $this->initTemplate('/tags/fetch/dataset_with_offset_limit_by_attributes.html');

    $this->assertEqual($page->capture(), 'ivan|');
  }

  function testDatasetIsCachedByDetault()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" to="[testTarget]" />' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/fetched_dataset_is_cached.html', $template);

    $page = $this->initTemplate('/tags/fetch/fetched_dataset_is_cached.html');

    $this->assertEqual(trim($page->capture()), 'joe|ivan|');

    // let's change dataset and see what is does not have any affect
    $dataset = array(array('title' => 'vika', 'description' => 'dancer'),
                      array('title' => 'loly', 'description' => 'stripper'));

    TestingFetchTagsDatasetFetcher :: setStubDataset($dataset);

    $this->assertEqual(trim($page->capture()), 'joe|ivan|');
  }

  function testDatasetCachingCanBeDisabled()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" to="[testTarget]" cache_dataset="false"/>' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/feched_dataset_caching_disabled.html', $template);

    $page = $this->initTemplate('/tags/fetch/feched_dataset_caching_disabled.html');

    $this->assertEqual(trim($page->capture()), 'joe|ivan|');

    // let's change dataset and see what is does not have any affect
    $dataset = array(array('title' => 'vika', 'description' => 'dancer'),
                     array('title' => 'loly', 'description' => 'stripper'));

    TestingFetchTagsDatasetFetcher :: setStubDataset($dataset);

    $this->assertEqual(trim($page->capture()), 'vika|loly|');
  }

  function testMultipleTargetsForBC()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" target="testTarget1,testTarget2" />' .
                '<list:LIST id="testTarget1"><list:ITEM>{$title}-</list:ITEM></list:LIST>' .
                '<list:LIST id="testTarget2"><list:ITEM>{$description}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/dataset_multiple_targets_in_target_for_bc.html', $template);

    $page = $this->initTemplate('/tags/fetch/dataset_multiple_targets_in_target_for_bc.html');

    $this->assertEqual($page->capture(), 'joe-ivan-fisher|gamer|');
  }

  function testTargetAttributeForBC()
  {
    $template = '<fetch using="TestingFetchTagsDatasetFetcher" target="testTarget" />' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/fetch/dataset_single_target_for_bc.html', $template);

    $page = $this->initTemplate('/tags/fetch/dataset_single_target_for_bc.html');

    $this->assertEqual(trim($page->capture()), 'joe|ivan|');
  }
}

