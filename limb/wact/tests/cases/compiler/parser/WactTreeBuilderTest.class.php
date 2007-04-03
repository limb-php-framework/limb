<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactTreeBuilderTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/templatecompiler.inc.php';

Mock::generate('WactCompileTreeNode','MockWactCompileTreeNode');
Mock::generate('WactSourceFileParser','MockWactSourceFileParser');
Mock::generate('WactTextNode','MockWactTextNode');
Mock::generate('WactAttributeNode','MockWactAttributeNode');
Mock::generate('WactPHPNode','MockWactPHPNode');
Mock::generate('WactHTMLParser','MockWactHTMLParser');
Mock::generate('WactCompiler','MockWactCompiler');

class WactTreeBuilderTest extends UnitTestCase
{
  protected $compiler;
  protected $tree_builder;
  protected $component;

  function setUp()
  {
    $this->compiler = new MockWactCompiler();
    $this->component = new MockWactCompileTreeNode();
    $this->tree_builder = new WactTreeBuilder($this->compiler);
    $this->tree_builder->setCursor($this->component);
  }

  function testPushNode()
  {
    $new_component = new MockWactCompileTreeNode();

    $this->component->expectOnce('addChild');
    $new_component->expectOnce('preParse', array($this->compiler));

    $this->tree_builder->pushNode($new_component);

    $this->assertReference($new_component, $this->tree_builder->getCursor());
  }

  function testAddNode()
  {
    $new_component = new MockWactCompileTreeNode();

    $this->component->expectOnce('addChild');
    $new_component->expectOnce('preParse');

    $this->tree_builder->addNode($new_component);

    $this->assertReference($this->component, $this->tree_builder->getCursor());
  }

  function testAddWactTextNode()
  {
    $this->component->expectOnce('addChild');

    $this->tree_builder->addWactTextNode('text');

    $this->assertReference($this->component, $this->tree_builder->getCursor());
  }

  function testPopNode()
  {
    $ParentComponent = new MockWactCompileTreeNode();
    $GrandParentComponent = new MockWactCompileTreeNode();
    $ParentComponent->parent = $GrandParentComponent;
    $this->component->parent = $ParentComponent;

    $this->component->setReturnValue('getChildren', array());

    $this->tree_builder->popNode(TRUE);
    $TreeBuilderCursor = $this->tree_builder->getCursor();

    $this->assertTrue($this->component->hasClosingTag);
    $this->assertReference($ParentComponent, $TreeBuilderCursor);
    $this->assertReference($GrandParentComponent,$TreeBuilderCursor->parent);
  }

  function testPopExpectedTag()
  {
    $location = new WactSourceLocation('my_file', 10);

    try
    {
      $this->tree_builder->popExpectedTag('tag2', $location);
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

  function testPushAndPopExpectedTagTwice()
  {
    $location1 = new WactSourceLocation('my_file', 10);
    $location2 = new WactSourceLocation('my_file', 11);
    $location3 = new WactSourceLocation('my_file', 12);
    $location4 = new WactSourceLocation('my_file', 14);

    $this->tree_builder->pushExpectedTag('tag1', 'tag1info', $location1);
    $this->tree_builder->pushExpectedTag('tag2', 'tag2info', $location2);

    $this->assertEqual($this->tree_builder->popExpectedTag('tag2', $location3), 'tag2info');

    try
    {
      $this->tree_builder->popExpectedTag('tag2', $location4);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Unexpected closing tag/', $e->getMessage());
      $params = $e->getParams();
      $this->assertEqual($params['file'], 'my_file');
      $this->assertEqual($params['tag'], 'tag2');
      $this->assertEqual($params['line'], 14);
      $this->assertEqual($params['ExpectTag'], 'tag1');
      $this->assertEqual($params['ExpectTagFile'], 'my_file');
      $this->assertEqual($params['ExpectedTagLine'], 10);
    }
  }

  function testPushCursor() {
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
    $this->tree_builder->popExpectedTag('tag', new WactSourceLocation('my_file', 16));
    $this->tree_builder->pushNode($child2);
    $this->tree_builder->popNode(true);

    // the new node should be a child of Root, not InsertionPoint
    $this->assertReference($this->tree_builder->getCursor(), $root);
    $this->assertReference($child2->parent, $root);
  }

  function testPushAndPopExpectedTagsWithPushCursor()
  {
    $new_cursor = new WactCompileTreeNode();

    $this->tree_builder->pushExpectedTag('tag1', 'tag1info', new WactSourceLocation('my_file', 10));

    // push a new cursor
    $this->tree_builder->pushCursor($new_cursor, new WactSourceLocation('my_file', 12));
    $this->assertReference($this->tree_builder->getCursor(), $new_cursor);

    $this->tree_builder->pushExpectedTag('tag2', 'tag2info', new WactSourceLocation('my_file', 13));

    $this->assertEqual($this->tree_builder->getExpectedTagCount(), 3);

    $this->assertEqual($this->tree_builder->getExpectedTag(), 'tag2');
    $this->assertEqual($this->tree_builder->popExpectedTag('tag2', new WactSourceLocation('my_file', 15)), 'tag2info');
    $this->assertEqual($this->tree_builder->getExpectedTagCount(), 2);

    // getting expected tag should skip the cursor
    $this->assertEqual($this->tree_builder->getExpectedTag(), 'tag1');

    // popping the next tag should restore the cursor to the original
    $this->assertEqual($this->tree_builder->popExpectedTag('tag1', new WactSourceLocation('my_file', 17)), 'tag1info');
    $this->assertReference($this->tree_builder->getCursor(), $this->component);
    $this->assertEqual($this->tree_builder->getExpectedTagCount(), 0);
  }
}
?>