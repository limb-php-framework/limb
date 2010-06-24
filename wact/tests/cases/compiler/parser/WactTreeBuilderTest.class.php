<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';

Mock::generate('WactCompileTreeNode','MockWactCompileTreeNode');
Mock::generate('WactCompiler','MockWactCompiler');

class WactNodeBuilderTestTag extends WactCompilerTag{}

class WactNodeBuilderTestProperty extends WactCompilerProperty
{
  function isConstant()
  {
    return true;
  }

  function getValue()
  {
    return 'my_property_value';
  }
}

class WactNodeBuilderTestFilter extends WactCompilerFilter{}


class WactTreeBuilderTest extends UnitTestCase
{
  protected $compiler;
  protected $tree_builder;
  protected $component;
  protected $tag_dictionary;
  protected $property_dictionary;
  protected $filter_dictionary;

  function setUp()
  {
    $this->compiler = new MockWactCompiler();
    $this->tag_dictionary = new WactTagDictionary();
    $this->property_dictionary = new WactPropertyDictionary();
    $this->filter_dictionary = new WactFilterDictionary();

    $this->component = new WactCompilerTag(new WactSourceLocation('my_file', 1),
                                           $tag_name = 'my_tag',
                                           new WactTagInfo($tag_name, 'MyTagClass'));
    $this->tree_builder = new WactTreeBuilder($this->compiler, $this->tag_dictionary, $this->property_dictionary, $this->filter_dictionary);
    $this->tree_builder->setCursor($this->component);
  }

  function testPushNodeMakedPushedNodeCurrentCursor()
  {
    $this->assertEqual($this->component->getChildren(), array());
    $this->assertReference($this->component, $this->tree_builder->getCursor());

    $child_component = new MockWactCompileTreeNode();
    $child_component->expectOnce('preParse', array($this->compiler));

    $this->tree_builder->pushNode($child_component);

    $this->assertReference($child_component, $this->tree_builder->getCursor());
    $children = $this->component->getChildren();
    $this->assertReference($children[0], $child_component);
  }

  function testAddNodeDontChangeCursor()
  {
    $this->assertEqual($this->component->getChildren(), array());
    $this->assertReference($this->component, $this->tree_builder->getCursor());

    $child_component = new MockWactCompileTreeNode();
    $child_component->expectOnce('preParse');

    $this->tree_builder->addNode($child_component);

    $this->assertReference($this->component, $this->tree_builder->getCursor());
    $children = $this->component->getChildren();
    $this->assertReference($children[0], $child_component);
  }

  function testAddWactTextNode()
  {
    $this->assertReference($this->component, $this->tree_builder->getCursor());

    $this->tree_builder->addWactTextNode('text');

    $this->assertReference($this->component, $this->tree_builder->getCursor());
    $children = $this->component->getChildren();
    $this->assertEqual(sizeof($children), 1);
    $this->assertIsA($children[0], 'WactTextNode');
    $this->assertEqual($children[0]->getText(), 'text');
  }

  function testPopNodeChangeCursorToParent()
  {
    $this->assertReference($this->component, $this->tree_builder->getCursor());

    $parent_component = new WactCompileTreeNode();
    $this->component->parent = $parent_component;

    $this->tree_builder->popNode();
    $TreeBuilderCursor = $this->tree_builder->getCursor();

    $this->assertTrue($this->component->hasClosingTag);
    $this->assertReference($parent_component, $this->tree_builder->getCursor());
  }

  function testPopExpectedTagWithoutAnyExpected()
  {
    $location = new WactSourceLocation('my_file', 10);

    try
    {
      $this->tree_builder->popExpectedWactTag('tag2', $location);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Lonely closing tag/', $e->getMessage());
      $params = $e->getParams();
      $this->assertEqual($params['file'], 'my_file');
      $this->assertEqual($params['line'], 10);
      $this->assertEqual($params['tag'], 'tag2');
    }
  }

  function testPairPushAndPopTheSameTagWorksOk()
  {
    $whatever_location = new WactSourceLocation('my_file', 1);

    $open_location = new WactSourceLocation('my_file', 10);
    $close_location = new WactSourceLocation('my_file', 12);

    $this->tree_builder->pushExpectedPlainTag('other_tag', $whatever_location);
    $this->tree_builder->pushExpectedPlainTag('tag', $open_location);
    $this->tree_builder->popExpectedPlainTag('tag', $close_location);

    $this->assertEqual($this->tree_builder->getExpectedTagCount(), 1);
    $this->assertEqual($this->tree_builder->getExpectedTag(), 'other_tag');
  }

  function testPopPlainTagSearchesNearestOpeningTag()
  {
    $first_location = new WactSourceLocation('my_file', 1);
    $open_location = new WactSourceLocation('my_file', 10);
    $second_location = new WactSourceLocation('my_file', 11);
    $close_location = new WactSourceLocation('my_file', 12);

    $this->tree_builder->pushExpectedPlainTag('first_tag', $first_location);
    $this->tree_builder->pushExpectedPlainTag('our_tag', $open_location);
    $this->tree_builder->pushExpectedPlainTag('plain_tag', $second_location);

    $this->tree_builder->popExpectedPlainTag('our_tag', $close_location);

    $this->assertEqual($this->tree_builder->getExpectedTagCount(), 1);
    $this->assertEqual($this->tree_builder->getExpectedTag(), 'first_tag');
  }

  function testPopPlainTagStopsSearchesNearestOpeningTagIfFoundComponentTag()
  {
    $first_location = new WactSourceLocation('my_file', 1);
    $open_location = new WactSourceLocation('my_file', 9);
    $second_location = new WactSourceLocation('my_file', 10);
    $third_location = new WactSourceLocation('my_file', 11);
    $close_location = new WactSourceLocation('my_file', 12);

    $this->tree_builder->pushExpectedPlainTag('first_tag', $first_location);
    $this->tree_builder->pushExpectedPlainTag('our_tag', $open_location);
    $this->tree_builder->pushExpectedWactTag('component_tag', $second_location);
    $this->tree_builder->pushExpectedPlainTag('plain_tag', $third_location);

    $this->assertEqual($this->tree_builder->popExpectedPlainTag('our_tag', $close_location), true);

    $this->assertEqual($this->tree_builder->getExpectedTagCount(), 3);
    $this->assertEqual($this->tree_builder->getExpectedTag(), 'component_tag');
    $this->assertReference($this->tree_builder->getExpectedTagLocation(), $second_location);
  }

  function testPopComponentTagStopsSearchesNearestOpeningTagIfFoundComponentTagAndThrowsException()
  {
    $first_location = new WactSourceLocation('my_file', 1);
    $open_location = new WactSourceLocation('my_file', 9);
    $second_location = new WactSourceLocation('my_file', 10);
    $third_location = new WactSourceLocation('my_file', 11);
    $close_location = new WactSourceLocation('my_file', 12);

    $this->tree_builder->pushExpectedPlainTag('first_tag', $first_location);
    $this->tree_builder->pushExpectedWactTag('our_tag', $open_location);
    $this->tree_builder->pushExpectedWactTag('component_tag', $second_location);
    $this->tree_builder->pushExpectedPlainTag('plain_tag', $third_location);

    try
    {
      $this->tree_builder->popExpectedWactTag('our_tag', $close_location);
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Unexpected closing tag/', $e->getMessage());
      $params = $e->getParams();
      $this->assertEqual($params['file'], 'my_file');
      $this->assertEqual($params['tag'], 'our_tag');
      $this->assertEqual($params['line'], 12);
      $this->assertEqual($params['ExpectTag'], 'component_tag');
      $this->assertEqual($params['ExpectTagFile'], 'my_file');
      $this->assertEqual($params['ExpectedTagLine'], 10);
    }
  }

  function testPushCursor()
  {
    // This test is essentially a test of the functionality that enables the
    // core:wrap implementation.
    // Briefly:
    // 	(1) A tree is set up
    //  (2) A new cursor is pushed
    //  (3) New components added should appear under the tree
    //	(4) When the parser pops the tag at which the cursor was pushed
    //      the cursor returns where it was before step (2)
    //  (5) New components added should appear under this orig point.

    $root = new WactCompileTreeNode();
    $InsertionPoint = new WactCompileTreeNode();
    $child1 = new WactCompileTreeNode();
    $child2 = new WactCompileTreeNode();

    // set up an open tag at root
    $this->tree_builder->setCursor($root);
    $this->tree_builder->pushExpectedWactTag('tag', new WactSourceLocation('my_file', 10));

    // add some content to the tree
    $this->tree_builder->pushNode($InsertionPoint);
    $this->tree_builder->popNode();

    // make sure the tree is: Root --child--> InsertionPoint with cursor
    // at Root and open 'tag'
    $this->assertReference($this->tree_builder->getCursor(), $root);
    $this->assertReference($InsertionPoint->parent, $root);
    $this->assertEqual($this->tree_builder->getExpectedTag(), 'tag');

    // push InsertionPoint as cursor, and add another node to the tree
    $this->tree_builder->pushCursor($InsertionPoint, new WactSourceLocation('my_file', 15));
    $this->tree_builder->pushNode($child1);
    $this->tree_builder->popNode();

    // make sure cursor is at InsertionPoint, and new node is child of InsertionPoint
    $this->assertReference($this->tree_builder->getCursor(), $InsertionPoint);
    $this->assertReference($child1->parent, $InsertionPoint);

    // now the parser gets '</tag>', and then more content
    // so we pop 'tag' (should restore orig cursor), and add a new node
    $this->tree_builder->popExpectedWactTag('tag', new WactSourceLocation('my_file', 16), $is_wact_tag = true);
    $this->tree_builder->pushNode($child2);
    $this->tree_builder->popNode();

    // the new node should be a child of Root, not InsertionPoint
    $this->assertReference($this->tree_builder->getCursor(), $root);
    $this->assertReference($child2->parent, $root);
  }

  function testPushAndPopExpectedTagsWithPushCursor()
  {
    $new_cursor = new WactCompileTreeNode();

    $this->tree_builder->pushExpectedWactTag('tag1', new WactSourceLocation('my_file', 10));

    // push a new cursor
    $this->tree_builder->pushCursor($new_cursor, new WactSourceLocation('my_file', 12));
    $this->assertReference($this->tree_builder->getCursor(), $new_cursor);

    $this->tree_builder->pushExpectedWactTag('tag2', new WactSourceLocation('my_file', 13));

    $this->assertEqual($this->tree_builder->getExpectedTagCount(), 3);

    $this->assertEqual($this->tree_builder->getExpectedTag(), 'tag2');
    $this->assertEqual($this->tree_builder->popExpectedWactTag('tag2', new WactSourceLocation('my_file', 15)), WACT_EXPECTED_WACT_TAG);
    $this->assertEqual($this->tree_builder->getExpectedTagCount(), 2);

    // getting expected tag should skip the cursor
    $this->assertEqual($this->tree_builder->getExpectedTag(), 'tag1');

    // popping the next tag should restore the cursor to the original
    $this->assertEqual($this->tree_builder->popExpectedWactTag('tag1', new WactSourceLocation('my_file', 17)), WACT_EXPECTED_WACT_TAG);
    $this->assertReference($this->tree_builder->getCursor(), $this->component);
    $this->assertEqual($this->tree_builder->getExpectedTagCount(), 0);
  }

  function testAddProcessingInstructionPHP()
  {
    $this->tree_builder->addProcessingInstruction('php', $code = 'echo $var');
    $children = $this->component->getChildren();
    $this->assertIsA($children[0], 'WactPHPNode');
    $this->assertEqual($children[0]->getCode(), $code);
  }

  function testAddProcessingInstructionNotPHP()
  {
    $this->tree_builder->addProcessingInstruction('other', 'some content');
    $children = $this->component->getChildren();
    $this->assertIsA($children[0], 'WactPHPNode');
    $this->assertEqual($children[0]->getCode(), 'echo "<?other some content?>\n";');
  }

  function testBuildTagNodeWithPropertyAndAttributes()
  {
    $tag_info = new WactTagInfo('my_tag', 'WactNodeBuilderTestTag');
    $location = new WactSourceLocation('my_file', 10);

    $property_info = new WactPropertyInfo('my_property', 'WactNodeBuilderTestTag', 'WactNodeBuilderTestProperty');
    $this->property_dictionary->registerPropertyInfo($property_info, __FILE__);

    $attrs = array('attr1' => 'value1',
                   'attr2' => 'value2{$my_property}', // will be compount attribute
                   'attr3' => '{$my_property}', // will be regular attribute expression
                   );

    $node = $this->tree_builder->buildTagNode($tag_info, $tag = 'MY_TAG', $location, $attrs, $isEmpty = true);

    $this->assertIsA($node, 'WactNodeBuilderTestTag');
    $this->assertTrue($node->emptyClosedTag);
    $this->assertIsA($node->getProperty('my_property'), 'WactNodeBuilderTestProperty');

    $this->assertEqual($node->getAttribute('attr1'), 'value1');
    $attr1 = $node->getAttributeNode('attr1');
    $this->assertIsA($attr1, 'WactAttribute');
    $attr1_fragment1 = $attr1->getFragment(0);
    $this->assertIsA($attr1_fragment1, 'WactAttributeLiteralFragment');

    $attr2 = $node->getAttributeNode('attr2');
    $this->assertIsA($attr2, 'WactAttribute');

    $attr2_fragment1 = $attr2->getFragment(0);
    $this->assertEqual($attr2->getFragment(0)->getValue(), 'value2');
    $this->assertIsA($attr2->getFragment(0), 'WactAttributeLiteralFragment');
    $this->assertIsA($attr2->getFragment(1), 'WactAttributeExpressionFragment');
    $this->assertEqual($attr2->getFragment(1)->getValue(), 'my_property_value');
    $this->assertReference($attr2->getFragment(1)->getExpression()->getFilterDictionary(),
                           $this->filter_dictionary);

    $attr3 = $node->getAttributeNode('attr3');
    $this->assertIsA($attr3, 'WactAttribute');
    $attr3_fragment1 = $attr3->getFragment(0);
    $this->assertIsA($attr3_fragment1, 'WactAttributeExpressionFragment');

    $this->assertReference($attr3_fragment1->getExpression()->getFilterDictionary(),
                           $this->filter_dictionary);
  }

  function testBuildTagNodeWithNullAttribute()
  {
    $location = new WactSourceLocation('my_file', 5);
    $tag_info = new WactTagInfo('my_tag', 'WactNodeBuilderTestTag');

    $attrs = array('attr1' => null);

    $node = $this->tree_builder->buildTagNode($tag_info, $tag = 'MY_TAG', $location, $attrs, $isEmpty = true);
    $this->assertFalse($node->getAttribute('attr1'));
  }

  function testAddContentWithSimpleText()
  {
    $location = new WactSourceLocation('my_file', 5);

    $text = 'my text';
    $this->tree_builder->addContent($text, $location);

    $children = $this->component->getChildren();
    $this->assertEqual(sizeof($children), 1);
    $this->assertIsA($children[0], 'WactTextNode');
    $this->assertEqual($children[0]->getText(), 'my text');
  }

  function testAddContentWithTextWithDBE()
  {
    $location = new WactSourceLocation('my_file', 5);

    $text = 'my text{$var}my other text';

    $this->filter_dictionary->registerFilterInfo(new WactFilterInfo('html', 'WactNodeBuilderTestFilter'), __FILE__);

    $this->tree_builder->addContent($text, $location);

    $children = $this->component->getChildren();
    $this->assertEqual(sizeof($children), 3);
    $this->assertIsA($children[0], 'WactTextNode');
    $this->assertEqual($children[0]->getText(), 'my text');
    $this->assertIsA($children[1], 'WactOutputExpressionNode');
    $this->assertIsA($children[2], 'WactTextNode');
    $this->assertEqual($children[2]->getText(), 'my other text');
  }
}

