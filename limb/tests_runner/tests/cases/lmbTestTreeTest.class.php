<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/../../src/lmbTestTree.class.php');
require_once(dirname(__FILE__) . '/../../src/lmbTestTreeDirNode.class.php');
require_once(dirname(__FILE__) . '/../../src/lmbTestGroup.class.php');

Mock :: generate('lmbTestTreeDirNode', 'MockTestTreeNode');

class lmbTestTreeTest extends UnitTestCase
{
  function setUp()
  {
    Mock :: generate('lmbTestGroup', 'MockTestGroup');//prevent this mock to be executed as a test case
  }

  function testPerform()
  {
    $node = new MockTestTreeNode();

    $root_node = new MockTestTreeNode();
    $root_node->expectOnce('findChildByPath', array($path = '/1/0'));
    $root_node->expectOnce('bootstrapPath', array($path));
    $root_node->setReturnValue('findChildByPath', $node);

    $group = new MockTestGroup();

    $node->expectOnce('createTestGroupWithParents');
    $node->setReturnValue('createTestGroupWithParents', $group);

    $group->expectOnce('run', array($reporter = new SimpleReporter()));
    $group->setReturnValue('run', $res = 1);

    $runner = new lmbTestTree($root_node);

    $this->assertEqual($runner->getElapsedTime(), 0);
    $this->assertEqual($runner->perform($path, $reporter), $res);
    $this->assertNotEqual($runner->getElapsedTime(), 0);
  }

  function testFind()
  {
    $node = new MockTestTreeNode();
    $node->expectOnce('findChildByPath', array($path = '/1/0'));
    $node->setReturnValue('findChildByPath', $node);

    $runner = new lmbTestTree($node);

    //preventing "Nesting level too deep - recursive dependency?"
    $this->assertTrue($runner->find($path) === $node);
  }
}

?>
