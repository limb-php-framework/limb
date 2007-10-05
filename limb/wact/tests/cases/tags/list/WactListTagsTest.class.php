<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');
require_once('decorators.inc.php');

class WactListTagsTest extends WactTemplateTestCase
{
  protected $founding_fathers;
  protected $numbers;

  function setUp()
  {
    parent :: setUp();

    $this->founding_fathers =  array(array('First' => 'George', 'Last' => 'Washington'),
                                    array('First' => 'Alexander', 'Last' => 'Hamilton'),
                                    array('First' => 'Benjamin', 'Last' => 'Franklin'));

    $this->numbers = array(array('BaseNumber' => 2),
                          array('BaseNumber' => 4),
                          array('BaseNumber' => 6));
  }

  function testList()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$First}-</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/list.html', $template);
    $page = $this->initTemplate('/tags/list/list.html');

    $list = $page->getChild('test');
    $list->registerDataSet($this->founding_fathers);
    $output = $page->capture();
    $this->assertEqual($output, "George-Alexander-Benjamin-");
  }

  function testListItemGeneratedLocalVariableInside()
  {
    $template = '<list:LIST id="test"><list:ITEM id="father"><?php echo $father["First"]; ?></list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/list_list_generates_local_php_variable.html', $template);
    $page = $this->initTemplate('/tags/list/list_list_generates_local_php_variable.html');

    $list = $page->getChild('test');
    $list->registerDataSet($this->founding_fathers);
    $output = $page->capture();
    $this->assertEqual($output, "GeorgeAlexanderBenjamin");
  }

  function testListSeparator()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$First}'.
                '<list:SEPARATOR>-</list:SEPARATOR></list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/separator.html', $template);
    $page = $this->initTemplate('/tags/list/separator.html');

    $list = $page->getChild('test');
    $list->registerDataSet($this->founding_fathers);
    $output = $page->capture();
    $this->assertEqual($output, "George-Alexander-Benjamin");
  }

  function testSeparatorWithDefinedStep()
  {
    $template = '<list:LIST id="test">'.
                '<list:ITEM>{$First}<list:SEPARATOR every="2">|</list:SEPARATOR></list:ITEM>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/tags/list/separator_defined_step.html', $template);

    $page = $this->initTemplate('/tags/list/separator_defined_step.html');

    $list = $page->getChild('test');
    $list->registerDataSet($this->founding_fathers);

    $this->assertEqual($page->capture(), 'GeorgeAlexander|Benjamin');
  }

  function testTwoDependentSeparators()
  {
    $template = '<list:LIST id="test">'.
                '<list:ITEM>{$name}'.
                '<list:SEPARATOR every="2">|</list:SEPARATOR>'.
                '<list:SEPARATOR>:</list:SEPARATOR>'.
                '</list:ITEM>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/tags/list/two_dependent_separators.html', $template);

    $page = $this->initTemplate('/tags/list/two_dependent_separators.html');

    $list = $page->getChild('test');
    $list->registerDataSet(array(array('name' => 'John'),
                                 array('name' => 'Pavel'),
                                 array('name' => 'Peter'),
                                 array('name' => 'Harry'),
                                 array('name' => 'Roman'),
                                 array('name' => 'Sergey')));

    $this->assertEqual($page->capture(), 'John:Pavel|Peter:Harry|Roman:Sergey');
  }

  function testThreeDependentSeparators()
  {
    $template = '<list:LIST id="test">'.
                '<list:ITEM>{$name}'.
                '<list:SEPARATOR every="5">++</list:SEPARATOR>'.
                '<list:SEPARATOR every="2">|</list:SEPARATOR>'.
                '<list:SEPARATOR>:</list:SEPARATOR>'.
                '</list:ITEM>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/tags/list/three_dependent_separators.html', $template);

    $page = $this->initTemplate('/tags/list/three_dependent_separators.html');

    $list = $page->getChild('test');
    $list->registerDataSet(array(array('name' => 'John'), array('name' => 'Pavel'),
                                 array('name' => 'Peter'), array('name' => 'Harry'),
                                 array('name' => 'Roman'),
                                 array('name' => 'Sergey'), array('name' => 'Ilia'),
                                 array('name' => 'Vlad'),
                                 ));

    $this->assertEqual($page->capture(), 'John:Pavel|Peter:Harry|Roman++Sergey:Ilia|Vlad');
  }

  function testListSeparatorWithUnbalancedContent()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$First}'.
                '<list:SEPARATOR></tr><tr></list:SEPARATOR></list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/separator_with_unbalanced_content.html', $template);
    $page = $this->initTemplate('/tags/list/separator_with_unbalanced_content.html');

    $list = $page->getChild('test');
    $list->registerDataSet($this->founding_fathers);
    $output = $page->capture();
    $this->assertEqual($output, "George</tr><tr>Alexander</tr><tr>Benjamin");
  }

  function testListSeparatorWithUnpropertyClosedParentTag()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$First}'.
                '<list:SEPARATOR></tr><tr></list:ITEM></list:SEPARATOR></list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/separator_with_unproperty_closed_parent_tag.html', $template);
    try
    {
      $page = $this->initTemplate('/tags/list/separator_with_unproperty_closed_parent_tag.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Unexpected closing tag/', $e->getMessage());
      $this->assertEqual($e->getParam('tag'), 'list:ITEM');
    }
  }

  function testListSeparatorWithLiteralAttribute()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$First}'.
                 '<list:SEPARATOR literal="true">{$var}</list:SEPARATOR></list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/separator_with_literal.html', $template);
    $page = $this->initTemplate('/tags/list/separator_with_literal.html');

    $list = $page->getChild('test');
    $list->registerDataSet($this->founding_fathers);
    $output = $page->capture();
    $this->assertEqual($output, 'George{$var}Alexander{$var}Benjamin');
  }

  function testSeparatorWithDynamicStepValue()
  {
    $template = '<list:LIST id="test">'.
                '<list:ITEM>{$First}<list:SEPARATOR every="{$#step}">|</list:SEPARATOR></list:ITEM>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/tags/list/separator_dynamic_step.html', $template);

    $page = $this->initTemplate('/tags/list/separator_dynamic_step.html');
    $page->setChildDataset('test', $this->founding_fathers);
    $page->set('step', 2);

    $this->assertEqual($page->capture(), 'GeorgeAlexander|Benjamin');
  }

  function testListDefaultWithDataNotOutput()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$First}-</list:ITEM>'.
                '<list:default>default</list:default>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/tags/list/default.html', $template);
    $page = $this->initTemplate('/tags/list/default.html');

    $list = $page->getChild('test');
    $list->registerDataSet($this->founding_fathers);
    $output = $page->capture();
    $this->assertEqual($output, "George-Alexander-Benjamin-");
  }

  function testListDefaultWithNoDataOutputs()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$First}-</list:ITEM>'.
                '<list:default>default</list:default>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/tags/list/default-empty.html', $template);
    $page = $this->initTemplate('/tags/list/default-empty.html');

    $list = $page->getChild('test');
    $list->registerDataSet(new WactArrayIterator(array()));
    $output = $page->capture();
    $this->assertEqual($output, "default");
  }

  function testListRowNumberProperty()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$ListRowNumber}:{$First}-</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/list-rownumber.html', $template);
    $page = $this->initTemplate('/tags/list/list-rownumber.html');

    $list = $page->getChild('test');
    $list->registerDataSet($this->founding_fathers);
    $output = $page->capture();
    $this->assertEqual($output, "1:George-2:Alexander-3:Benjamin-");
  }

  function testRowNumberWithOffset()
  {
    $template = '<list:LIST id="test">'.
                '<list:ITEM>{$ListRowNumber}:{$First}</list:ITEM>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/tags/list/list_row_number_with_offset.html', $template);

    $page = $this->initTemplate('/tags/list/list_row_number_with_offset.html');

    $list = $page->getChild('test');

    $dataset = new WactArrayIterator($this->founding_fathers);
    $dataset->paginate(1, 2);

    $list->registerDataSet($dataset);
    $this->assertEqual($page->capture(), '2:Alexander3:Benjamin');
  }

  function testListRowOddProperty()
  {
    $template = '<list:LIST id="test"><list:ITEM>'.
                '<core:optional for="{$:ListRowOdd}">odd</core:optional>'.
                '<core:default for="{$:ListRowOdd}">even</core:default>'.
                ':{$First}-'.
                '</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/list-rowodd.html', $template);
    $page = $this->initTemplate('/tags/list/list-rowodd.html');

    $list = $page->getChild('test');
    $list->registerDataSet($this->founding_fathers);
    $output = $page->capture();
    $this->assertEqual($output, "odd:George-even:Alexander-odd:Benjamin-");
  }

  function testListRowEvenProperty()
  {
    $template = '<list:LIST id="test"><list:ITEM>'.
                '<core:optional for="ListRowEven">even</core:optional>'.
                '<core:default for="ListRowEven">odd</core:default>'.
                ':{$First}-'.
                '</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/list-roweven.html', $template);
    $page = $this->initTemplate('/tags/list/list-roweven.html');

    $list = $page->getChild('test');
    $list->registerDataSet($this->founding_fathers);
    $output = $page->capture();
    $this->assertEqual($output, "odd:George-even:Alexander-odd:Benjamin-");
  }

  function testListParityProperty()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$Parity}:{$First}-</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/list-parity.html', $template);
    $page = $this->initTemplate('/tags/list/list-parity.html');

    $list = $page->getChild('test');
    $list->registerDataSet($this->founding_fathers);
    $output = $page->capture();
    $this->assertEqual($output, "odd:George-even:Alexander-odd:Benjamin-");
  }

  function testListTotalItemsProperty()
  {
    $template = '<list:LIST id="test"><list:item>{$^:TotalItems}</list:item></list:LIST>';

    $this->registerTestingTemplate('/tags/list/list-total-items.html', $template);
    $page = $this->initTemplate('/tags/list/list-total-items.html');

    $list = $page->getChild('test');
    $list->registerDataSet($this->founding_fathers);
    $output = $page->capture();
    $this->assertEqual($output, "333");
  }

  function testListFrom()
  {
    $template = '<list:LIST from="{$test}"><list:ITEM>{$First}-</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/list_from.html', $template);
    $page = $this->initTemplate('/tags/list/list_from.html');

    $page->set('test', $this->founding_fathers);
    $output = $page->capture();
    $this->assertEqual($output, "George-Alexander-Benjamin-");
  }

  function testListFromOldSyntaxForBC()
  {
    $template = '<list:LIST from="test"><list:ITEM>{$First}-</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/list_from_old_syntax.html', $template);
    $page = $this->initTemplate('/tags/list/list_from_old_syntax.html');

    $page->set('test', $this->founding_fathers);
    $output = $page->capture();
    $this->assertEqual($output, "George-Alexander-Benjamin-");
  }

  function testNestedListOuterIdInnerFrom()
  {
     $template = '<list:LIST id="test"><list:ITEM>'.
                  '{$First}:'.
                  '<list:LIST from="sub"><list:ITEM>{$subvar1} </list:ITEM>-</list:LIST>'.
                  '</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/nested-id-from.html', $template);
    $page = $this->initTemplate('/tags/list/nested-id-from.html');

    $list = $page->getChild('test');
    $list->registerDataSet(new NestedDataSetDecorator($this->founding_fathers));
    $output = $page->capture();
    $this->assertEqual($output, "George:value1 value3 value5 -Alexander:value1 value3 value5 -Benjamin:value1 value3 value5 -");
  }

  function testNestedListOuterFromInnerFrom()
  {
    $template = '<list:LIST from="test"><list:ITEM>'.
                '{$First}:'.
                 '<list:LIST from="sub"><list:ITEM>{$subvar1} </list:ITEM>-</list:LIST>'.
                 '</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/nested-from-from.html', $template);
    $page = $this->initTemplate('/tags/list/nested-from-from.html');

    $page->set('test',new NestedDataSetDecorator($this->founding_fathers));
    $output = $page->capture();
    $this->assertEqual($output, "George:value1 value3 value5 -Alexander:value1 value3 value5 -Benjamin:value1 value3 value5 -");
  }

  function testNestedListOuterIdInnerId()
  {
    $template = '<list:LIST id="test"><list:ITEM>'.
                '{$BaseNumber}:'.
                '<list:LIST id="sub"><list:ITEM>{$Num} </list:ITEM>-</list:LIST>'.
                '</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/nested-id-id.html', $template);
    $page = $this->initTemplate('/tags/list/nested-id-id.html');

    $numbers_list = new WactArrayIterator($this->numbers);
    $page->setChildDataSet('test', $numbers_list);
    $page->setChildDataSet('sub', new InnerDataSource($numbers_list));
    $output = $page->capture();
    $this->assertEqual($output, "2:2 4 8 -4:4 16 64 -6:6 36 216 -");
  }

  function testNestedListOuterFromInnerId()
  {
    $template = '<list:LIST from="test"><list:ITEM>'.
                '{$BaseNumber}:'.
                '<list:LIST id="sub"><list:ITEM>{$Num} </list:ITEM>-</list:LIST>'.
                '</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/nested-from-id.html', $template);
    $page = $this->initTemplate('/tags/list/nested-from-id.html');

    $numbers_list = new WactArrayIterator($this->numbers);
    $page->set('test', $numbers_list);
    $page->setChildDataSet('sub', new InnerDataSource($numbers_list));

    $output = $page->capture();
    $this->assertEqual($output, "2:2 4 8 -4:4 16 64 -6:6 36 216 -");
  }

  function testFromComplexDBE()
  {
    $template = '<list:LIST from="object.test"><list:ITEM>'.
                '{$BaseNumber}-'.
                '</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/nested_from_complext_dbe.html', $template);
    $page = $this->initTemplate('/tags/list/nested_from_complext_dbe.html');

    $object = new ArrayObject(array('test' => new WactArrayIterator($this->numbers)));
    $page->set('object', $object);

    $output = $page->capture();
    $this->assertEqual($output, "2-4-6-");
  }

  function testWorksOkWithScalarValuesAsEmptyIterators()
  {
    $template = '<list:LIST from="object"><list:ITEM>'.
                '{$BaseNumber}-'.
                '</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/works_ok_with_scalar_as_empty_iterators.html', $template);
    $page = $this->initTemplate('/tags/list/works_ok_with_scalar_as_empty_iterators.html');

    $page->set('object', 'any_scalar');

    $output = $page->capture();
    $this->assertEqual($output, "");
  }

  function testScriptInList()
  {
    $template = '<list:list id="script_test" from="data"><script type="text/javascript">'.
                '<list:item>alert("{$msg}");</list:item>'.
                '</script></list:list>';

    $this->registerTestingTemplate('/tags/list/script-in-list.html', $template);
    $page = $this->initTemplate('/tags/list/script-in-list.html');

    $test_ds = new WactArrayIterator(array(array('msg' => 1),
                                           array('msg' => 2),
                                           array('msg' => 3)));

    $page->set('data', $test_ds);

    $output = $page->capture();
    $this->assertWantedPattern('/alert.*1.*alert.*2.*alert.*3/iU', $output, 'Bug 1000806-Failed to iterated over the list [%s]');
    $this->assertNoUnwantedPattern('/list/i', $output, 'Bug 1000806-Output contains the word list [%s]');
    $this->assertNoUnwantedPattern('/item/i', $output, 'Bug 1000806-Output contains the word item [%s]');
  }

  function testScriptInListWorkAround()
  {
    $template = '{$startscript|raw}<list:list id="script_test" from="data">'
                .'<list:item>alert("{$msg}");</list:item>'
                .'</list:list>{$endscript|raw}';

    $this->registerTestingTemplate('/tags/list/script-in-list2.html', $template);
    $page = $this->initTemplate('/tags/list/script-in-list2.html');

    $test_ds = new WactArrayIterator(array(array('msg' => 1),
                                              array('msg' => 2),
                                              array('msg' => 3)));

    $page->set('data', $test_ds);
    $page->set('startscript',  '<script type="text/javascript">');
    $page->set('endscript',  '</script>');

    $output = $page->capture();
    $this->assertWantedPattern('/alert.*1.*alert.*2.*alert.*3/iU', $output, 'Bug 1000806-Failed to iterated over the list [%s]');
    $this->assertNoUnwantedPattern('/list/i', $output, 'Bug 1000806-Output contains the word list [%s]');
    $this->assertNoUnwantedPattern('/item/i', $output, 'Bug 1000806-Output contains the word item [%s]');
  }

  function testListFillTagWithRatio()
  {
    $template = '<list:LIST id="test">'.
                '<list:ITEM>{$name}'.
                '<list:SEPARATOR every="3">++</list:SEPARATOR>'.
                '<list:SEPARATOR>:</list:SEPARATOR>'.
                '</list:ITEM>'.
                '<list:FILL upto="3">{$items_left}</list:FILL>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/tags/list/list_fill_tag_with_ratio.html', $template);

    $page = $this->initTemplate('/tags/list/list_fill_tag_with_ratio.html');

    $list = $page->getChild('test');
    $list->registerDataSet(array(array('name' => 'John'), array('name' => 'Pavel'),
                                 array('name' => 'Peter'), array('name' => 'Harry')));

    $this->assertEqual($page->capture(), 'John:Pavel:Peter++Harry2');
  }

  function testListFillTagWithRatioAndVarName()
  {
    $template = '<list:LIST id="test">'.
                '<list:ITEM>{$name}'.
                '<list:SEPARATOR every="3">++</list:SEPARATOR>'.
                '<list:SEPARATOR>:</list:SEPARATOR>'.
                '</list:ITEM>'.
                '<list:FILL upto="3" var="count">{$count}</list:FILL>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/tags/list/list_fill_tag_with_ratio_and_var_name.html', $template);

    $page = $this->initTemplate('/tags/list/list_fill_tag_with_ratio_and_var_name.html');

    $list = $page->getChild('test');
    $list->registerDataSet(array(array('name' => 'John'), array('name' => 'Pavel'),
                                 array('name' => 'Peter'), array('name' => 'Harry')));

    $this->assertEqual($page->capture(), 'John:Pavel:Peter++Harry2');
  }

  function testListFillTagWithTotalElementsLessThanRatio()
  {
    $template = '<list:LIST id="test">'.
                '<list:ITEM>{$name}'.
                '<list:SEPARATOR every="3">++</list:SEPARATOR>'.
                '<list:SEPARATOR>:</list:SEPARATOR>'.
                '</list:ITEM>'.
                '<list:FILL upto="3" var="count">{$count}</list:FILL>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/tags/list/list_fill_tag_with_ratio_and_var_name.html', $template);

    $page = $this->initTemplate('/tags/list/list_fill_tag_with_ratio_and_var_name.html');

    $list = $page->getChild('test');
    $list->registerDataSet(array(array('name' => 'John'), array('name' => 'Pavel')));

    $this->assertEqual($page->capture(), 'John:Pavel');
  }

  function testListFillTagWithDynamicUpToAttribute()
  {
    $template = '<list:LIST id="test">'.
                '<list:ITEM>{$name}'.
                '<list:SEPARATOR every="{$#step}">++</list:SEPARATOR>'.
                '<list:SEPARATOR>:</list:SEPARATOR>'.
                '</list:ITEM>'.
                '<list:FILL upto="{$#step}">{$items_left}</list:FILL>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/tags/list/list_fill_tag_with_dynamic_upto.html', $template);

    $page = $this->initTemplate('/tags/list/list_fill_tag_with_dynamic_upto.html');
    $page->set('step', 3);

    $list = $page->getChild('test');
    $list->registerDataSet(array(array('name' => 'John'), array('name' => 'Pavel'),
                                 array('name' => 'Peter'), array('name' => 'Harry')));

    $this->assertEqual($page->capture(), 'John:Pavel:Peter++Harry2');
  }


  function testListSeparatorInNestedList()
  {
    $template = '<list:LIST id="base">'.
                '<list:ITEM>{$name}:'.
                  '<list:list from="kids" id="nested">'.
                    '<list:item>{$name}'.
                      '<list:SEPARATOR every="2">++</list:SEPARATOR>'.
                    '</list:item>'.
                    '<list:FILL upto="2" var="count">{$count}</list:FILL>'.
                  '</list:list>'.
                '</list:item>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/tags/list/list_separator_in_nested_list.html', $template);

    $page = $this->initTemplate('/tags/list/list_separator_in_nested_list.html');

    $data = array(array('name' => 'John',
                        'kids' => array(array('name' => 'Serega'),
                                        array('name' => 'Pavel'),
                                        array('name' => 'Ilia'))),
                  array('name' => 'Mike',
                        'kids' => array(array('name' => 'Roman'),
                                        array('name' => 'Denis'),
                                        array('name' => 'Alex'))),
                  );

    $list = $page->getChild('base');
    $list->registerDataSet($data);

    $this->assertEqual($page->capture(), 'John:SeregaPavel++Ilia1Mike:RomanDenis++Alex1');
  }

  function testListKeyProperty()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$:Key}-{$lastname}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/list_key_property.html', $template);
    $page = $this->initTemplate('/tags/list/list_key_property.html');

    $page->setChildDataset('test', array('Ivan' => array('lastname' => 'Ivanov'),
                                         'Peter' => array('lastname' => 'Petrov')));

    $output = $page->capture();
    $this->assertEqual($output, "Ivan-Ivanov|Peter-Petrov|");
  }

  function testListFirstRowProperty()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$FirstRow}:{$First}-</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/list_first_row_property.html', $template);
    $page = $this->initTemplate('/tags/list/list_first_row_property.html');
    $page->setChildDataset('test', $this->founding_fathers);

    $output = $page->capture();
    $this->assertEqual($output, "1:George-0:Alexander-0:Benjamin-");
  }

  function testListLastRowProperty()
  {
    $template = '<list:LIST id="test"><list:ITEM>{$LastRow}:{$First}-</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/tags/list/list_last_row_property.html', $template);
    $page = $this->initTemplate('/tags/list/list_last_row_property.html');
    $page->setChildDataset('test', $this->founding_fathers);

    $output = $page->capture();
    $this->assertEqual($output, "0:George-0:Alexander-1:Benjamin-");
  }

  function testOutputPlainArrays()
  {
    $template = '<list:LIST id="test">'.
                '<list:ITEM id="father">{$$father}</list:ITEM>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/tags/list/output_plain_arrays.html', $template);

    $page = $this->initTemplate('/tags/list/output_plain_arrays.html');
    $page->setChildDataset('test', array('George', 'Alexander', 'Benjamin'));

    $this->assertEqual($page->capture(), 'GeorgeAlexanderBenjamin');
  }

  function testOutputNestedPlainArrays()
  {
    $template = '<list:LIST id="test">'.
                '<list:ITEM id="item">'.
                '<list:list from="$item">:{$:Key}:<list:item id="father">{$$father}</list:item></list:list>'.
                '</list:ITEM>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/tags/list/output_nested_plain_arrays.html', $template);

    $page = $this->initTemplate('/tags/list/output_nested_plain_arrays.html');

    $page->setChildDataset('test', array('first' => array('George', 'Alexander', 'Benjamin'),
                                         'second' => array('Ivanov', 'Petrov', 'Sidorov')));

    $this->assertEqual($page->capture(), ':first:GeorgeAlexanderBenjamin:second:IvanovPetrovSidorov');
  }

  function testOutputNestedPlainArraysIndexedFields()
  {
    $template = '<list:LIST id="test">'.
                '<list:ITEM id="item">'.
                ':{$:Key}:{$$item.0} or {$.0}-{$$item.1} or {$.1}'.
                '</list:ITEM>'.
                '</list:LIST>';

    $this->registerTestingTemplate('/tags/list/output_nested_plain_arrays_indexed_fields.html', $template);

    $page = $this->initTemplate('/tags/list/output_nested_plain_arrays_indexed_fields.html');

    $page->setChildDataset('test', array('first' => array('Ivan', 'Ivanov'),
                                         'second' => array('Peter', 'Petrov')));

    $this->assertEqual($page->capture(), ':first:Ivan or Ivan-Ivanov or Ivanov:second:Peter or Peter-Petrov or Petrov');
  }
}

