<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

Mock::generate('lmbMacroNode', 'MockMacroNode');
Mock::generate('lmbMacroCompiler', 'MockMacroCompiler');

class lmbMacroTreeBuilderTest extends UnitTestCase
{
  protected $compiler;
  protected $tree_builder;
  protected $component;
  protected $tag_dictionary;

  function setUp()
  {
    $this->compiler = new MockMacroCompiler();
    $this->tag_dictionary = new lmbMacroTagDictionary();
    $this->component = new lmbMacroTag(new lmbMacroSourceLocation('my_file', 1),
                                           $tag_name = 'my_tag',
                                           new lmbMacroTagInfo($tag_name, 'MyTagClass'));
    $this->tree_builder = new lmbMacroTreeBuilder($this->compiler);
    $this->tree_builder->setCursor($this->component);
  }

  function testPushNodeMakedPushedNodeCurrentCursor()
  {
    $this->assertEqual($this->component->getChildren(), array());
    $this->assertReference($this->component, $this->tree_builder->getCursor());

    $child_component = new MockMacroNode();
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

    $child_component = new MockMacroNode();
    $child_component->expectOnce('preParse');

    $this->tree_builder->addNode($child_component);

    $this->assertReference($this->component, $this->tree_builder->getCursor());
    $children = $this->component->getChildren();
    $this->assertReference($children[0], $child_component);
  }

  function testAddlmbMacroTextNode()
  {
    $this->assertReference($this->component, $this->tree_builder->getCursor());

    $this->tree_builder->addTextNode('text');

    $this->assertReference($this->component, $this->tree_builder->getCursor());
    $children = $this->component->getChildren();
    $this->assertEqual(sizeof($children), 1);
    $this->assertIsA($children[0], 'lmbMacroTextNode');
    $this->assertEqual($children[0]->getText(), 'text');
  }

  function testPopNodeChangeCursorToParent()
  {
    $this->assertReference($this->component, $this->tree_builder->getCursor());

    $parent_component = new lmbMacroNode();
    $this->component->setParent($parent_component);

    $this->tree_builder->popNode();    

    $this->assertTrue($this->component->getHasClosingTag());
    $this->assertReference($parent_component, $this->tree_builder->getCursor());
  }

  function testPopExpectedTagWithoutAnyExpected()
  {
    $location = new lmbMacroSourceLocation('my_file', 10);

    try
    {
      $this->tree_builder->popExpectedTag('tag2', $location);
    }
    catch(lmbMacroException $e)
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
    $whatever_location = new lmbMacroSourceLocation('my_file', 1);

    $open_location = new lmbMacroSourceLocation('my_file', 10);
    $close_location = new lmbMacroSourceLocation('my_file', 12);

    $this->tree_builder->pushExpectedTag('other_tag', $whatever_location);
    $this->tree_builder->pushExpectedTag('tag', $open_location);
    $this->tree_builder->popExpectedTag('tag', $close_location);

    $this->assertEqual($this->tree_builder->getExpectedTagCount(), 1);
    $this->assertEqual($this->tree_builder->getExpectedTag(), 'other_tag');
  }

  function testPopTagThrowsExceptionForNonClosedTags()
  {
    $first_location = new lmbMacroSourceLocation('my_file', 1);
    $open_location = new lmbMacroSourceLocation('my_file', 10);
    $second_location = new lmbMacroSourceLocation('my_file', 11);
    $close_location = new lmbMacroSourceLocation('my_file', 12);

    $this->tree_builder->pushExpectedTag('first_tag', $first_location);
    $this->tree_builder->pushExpectedTag('our_tag', $open_location);
    $this->tree_builder->pushExpectedTag('plain_tag', $second_location);

    try
    {
      $this->tree_builder->popExpectedTag('our_tag', $close_location);
    }
    catch(lmbMacroException $e)
    {
      $this->assertWantedPattern('/Unexpected closing tag/', $e->getMessage());
      $params = $e->getParams();
      $this->assertEqual($params['file'], 'my_file');
      $this->assertEqual($params['line'], 12);
      $this->assertEqual($params['tag'], 'our_tag');
      $this->assertEqual($params['expected_tag'], 'plain_tag');
      $this->assertEqual($params['expected_file'], 'my_file');
      $this->assertEqual($params['expected_line'], 11);         
    }
  }

  function testPushCursor()
  {
    // This test is essentially a test of the functionality that enables the
    // {{wrap}} implementation.
    // Briefly:
    // 	(1) A tree is set up
    //  (2) A new cursor is pushed
    //  (3) New components added should appear under the tree
    //	(4) When the parser pops the tag at which the cursor was pushed
    //      the cursor returns where it was before step (2)
    //  (5) New components added should appear under this orig. point

    $root = new lmbMacroNode();
    $InsertionPoint = new lmbMacroNode();
    $child1 = new lmbMacroNode();
    $child2 = new lmbMacroNode();

    // set up an open tag at root
    $this->tree_builder->setCursor($root);
    $this->tree_builder->pushExpectedTag('tag', new lmbMacroSourceLocation('my_file', 10));

    // add some content to the tree
    $this->tree_builder->pushNode($InsertionPoint);
    $this->tree_builder->popNode();

    // make sure the tree is: Root --child--> InsertionPoint with cursor
    // at Root and open 'tag'
    $this->assertReference($this->tree_builder->getCursor(), $root);
    $this->assertReference($InsertionPoint->getParent(), $root);
    $this->assertEqual($this->tree_builder->getExpectedTag(), 'tag');

    // push InsertionPoint as cursor, and add another node to the tree
    $this->tree_builder->pushCursor($InsertionPoint, new lmbMacroSourceLocation('my_file', 15));
    $this->tree_builder->pushNode($child1);
    $this->tree_builder->popNode();

    // make sure cursor is at InsertionPoint, and new node is child of InsertionPoint
    $this->assertReference($this->tree_builder->getCursor(), $InsertionPoint);
    $this->assertReference($child1->getParent(), $InsertionPoint);

    // now the parser gets '</tag>', and then more content
    // so we pop 'tag' (should restore orig cursor), and add a new node
    $this->tree_builder->popExpectedTag('tag', new lmbMacroSourceLocation('my_file', 16));
    $this->tree_builder->pushNode($child2);
    $this->tree_builder->popNode();

    // the new node should be a child of Root, not InsertionPoint
    $this->assertReference($this->tree_builder->getCursor(), $root);
    $this->assertReference($child2->getParent(), $root);
  }

  function testPushAndPopExpectedTagsWithPushCursor()
  {
    $new_cursor = new lmbMacroNode();

    $this->tree_builder->pushExpectedTag('tag1', new lmbMacroSourceLocation('my_file', 10));

    // push a new cursor
    $this->tree_builder->pushCursor($new_cursor, new lmbMacroSourceLocation('my_file', 12));
    $this->assertReference($this->tree_builder->getCursor(), $new_cursor);

    $this->tree_builder->pushExpectedTag('tag2', new lmbMacroSourceLocation('my_file', 13));

    $this->assertEqual($this->tree_builder->getExpectedTagCount(), 3);

    $this->assertEqual($this->tree_builder->getExpectedTag(), 'tag2');
    $this->assertEqual($this->tree_builder->popExpectedTag('tag2', new lmbMacroSourceLocation('my_file', 15)), 'tag2');
    $this->assertEqual($this->tree_builder->getExpectedTagCount(), 2);

    // getting expected tag should skip the cursor
    $this->assertEqual($this->tree_builder->getExpectedTag(), 'tag1');

    // popping the next tag should restore the cursor to the original
    $this->assertEqual($this->tree_builder->popExpectedTag('tag1', new lmbMacroSourceLocation('my_file', 17)), 'tag1');
    $this->assertReference($this->tree_builder->getCursor(), $this->component);
    $this->assertEqual($this->tree_builder->getExpectedTagCount(), 0);
  }
}

