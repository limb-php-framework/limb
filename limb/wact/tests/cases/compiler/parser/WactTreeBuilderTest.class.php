<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactTreeBuilderTest.class.php 5783 2007-04-29 07:04:40Z serega $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';

Mock::generate('WactCompileTreeNode','MockWactCompileTreeNode');
Mock::generate('WactCompiler','MockWactCompiler');

class WactTreeBuilderTest extends UnitTestCase
{
  protected $compiler;
  protected $tree_builder;
  protected $component;
  protected $tag_dictionary;

  function setUp()
  {
    $this->compiler = new MockWactCompiler();
    $this->tag_dictionary = new WactTagDictionary();
    $this->component = new WactCompilerTag(new WactSourceLocation('my_file', 1),
                                           $tag_name = 'my_tag',
                                           new WactTagInfo($tag_name, 'MyTagClass'));
    $this->tree_builder = new WactTreeBuilder($this->compiler);
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
      $this->tree_builder->popExpectedTag('tag2', $location, PARSER_TAG_IS_PLAIN);
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

    $this->tree_builder->pushExpectedTag('other_tag', PARSER_TAG_IS_PLAIN, $whatever_location);
    $this->tree_builder->pushExpectedTag('tag', PARSER_TAG_IS_PLAIN, $open_location);
    $this->tree_builder->popExpectedTag('tag', $close_location, PARSER_TAG_IS_PLAIN);

    $this->assertEqual($this->tree_builder->getExpectedTagCount(), 1);
    $this->assertEqual($this->tree_builder->getExpectedTag(), 'other_tag');
  }

  function testPopPlainTagSearchesNearestOpeningTag()
  {
    $first_location = new WactSourceLocation('my_file', 1);
    $open_location = new WactSourceLocation('my_file', 10);
    $second_location = new WactSourceLocation('my_file', 11);
    $close_location = new WactSourceLocation('my_file', 12);

    $this->tree_builder->pushExpectedTag('first_tag', PARSER_TAG_IS_PLAIN, $first_location);
    $this->tree_builder->pushExpectedTag('our_tag', PARSER_TAG_IS_PLAIN, $open_location);
    $this->tree_builder->pushExpectedTag('plain_tag', PARSER_TAG_IS_PLAIN, $second_location);

    $this->tree_builder->popExpectedTag('our_tag', $close_location, PARSER_TAG_IS_PLAIN);

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

    $this->tree_builder->pushExpectedTag('first_tag', PARSER_TAG_IS_PLAIN, $first_location);
    $this->tree_builder->pushExpectedTag('our_tag', PARSER_TAG_IS_PLAIN, $open_location);
    $this->tree_builder->pushExpectedTag('component_tag', PARSER_TAG_IS_COMPONENT, $second_location);
    $this->tree_builder->pushExpectedTag('plain_tag', PARSER_TAG_IS_PLAIN, $third_location);

    $this->tree_builder->popExpectedTag('our_tag', $close_location, PARSER_TAG_IS_PLAIN);

    $this->assertEqual($this->tree_builder->getExpectedTagCount(), 3);
    $this->assertEqual($this->tree_builder->getExpectedTag(), 'component_tag');
  }

  function testPopComponentTagStopsSearchesNearestOpeningTagIfFoundComponentTagAndThrowsException()
  {
    $first_location = new WactSourceLocation('my_file', 1);
    $open_location = new WactSourceLocation('my_file', 9);
    $second_location = new WactSourceLocation('my_file', 10);
    $third_location = new WactSourceLocation('my_file', 11);
    $close_location = new WactSourceLocation('my_file', 12);

    $this->tree_builder->pushExpectedTag('first_tag', PARSER_TAG_IS_PLAIN, $first_location);
    $this->tree_builder->pushExpectedTag('our_tag', PARSER_TAG_IS_COMPONENT, $open_location);
    $this->tree_builder->pushExpectedTag('component_tag', PARSER_TAG_IS_COMPONENT, $second_location);
    $this->tree_builder->pushExpectedTag('plain_tag', PARSER_TAG_IS_PLAIN, $third_location);

    try
    {
      $this->tree_builder->popExpectedTag('our_tag', $close_location, PARSER_TAG_IS_COMPONENT);
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
    $this->tree_builder->pushExpectedTag('tag', new WactSourceLocation('my_file', 10));

    // add some content to the tree
    $this->tree_builder->pushNode($InsertionPoint);
    $this->tree_builder->popNode(true);

    // make sure the tree is: Root --child--> InsertionPoint with cursor
    // at Root and open 'tag'
    $this->assertReference($this->tree_builder->getCursor(), $root);
    $this->assertReference($InsertionPoint->parent, $root);
    $this->assertEqual($this->tree_builder->getExpectedTag(), 'tag');

    // push InsertionPoint as cursor, and add another node to the tree
    $this->tree_builder->pushCursor($InsertionPoint, new WactSourceLocation('my_file', 15));
    $this->tree_builder->pushNode($child1);
    $this->tree_builder->popNode(true);

    // make sure cursor is at InsertionPoint, and new node is child of InsertionPoint
    $this->assertReference($this->tree_builder->getCursor(), $InsertionPoint);
    $this->assertReference($child1->parent, $InsertionPoint);

    // now the parser gets '</tag>', and then more content
    // so we pop 'tag' (should restore orig cursor), and add a new node
    $this->tree_builder->popExpectedTag('tag', new WactSourceLocation('my_file', 16), PARSER_TAG_IS_COMPONENT);
    $this->tree_builder->pushNode($child2);
    $this->tree_builder->popNode(true);

    // the new node should be a child of Root, not InsertionPoint
    $this->assertReference($this->tree_builder->getCursor(), $root);
    $this->assertReference($child2->parent, $root);
  }

  function testPushAndPopExpectedTagsWithPushCursor()
  {
    $new_cursor = new WactCompileTreeNode();

    $this->tree_builder->pushExpectedTag('tag1', PARSER_TAG_IS_COMPONENT, new WactSourceLocation('my_file', 10));

    // push a new cursor
    $this->tree_builder->pushCursor($new_cursor, new WactSourceLocation('my_file', 12));
    $this->assertReference($this->tree_builder->getCursor(), $new_cursor);

    $this->tree_builder->pushExpectedTag('tag2', PARSER_TAG_IS_COMPONENT, new WactSourceLocation('my_file', 13));

    $this->assertEqual($this->tree_builder->getExpectedTagCount(), 3);

    $this->assertEqual($this->tree_builder->getExpectedTag(), 'tag2');
    $this->assertEqual($this->tree_builder->popExpectedTag('tag2', new WactSourceLocation('my_file', 15), PARSER_TAG_IS_COMPONENT), PARSER_TAG_IS_COMPONENT);
    $this->assertEqual($this->tree_builder->getExpectedTagCount(), 2);

    // getting expected tag should skip the cursor
    $this->assertEqual($this->tree_builder->getExpectedTag(), 'tag1');

    // popping the next tag should restore the cursor to the original
    $this->assertEqual($this->tree_builder->popExpectedTag('tag1', new WactSourceLocation('my_file', 17), PARSER_TAG_IS_COMPONENT), PARSER_TAG_IS_COMPONENT);
    $this->assertReference($this->tree_builder->getCursor(), $this->component);
    $this->assertEqual($this->tree_builder->getExpectedTagCount(), 0);
  }
}
?>