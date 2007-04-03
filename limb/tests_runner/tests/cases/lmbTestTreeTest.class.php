<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestTreeTest.class.php 5050 2007-02-13 10:52:02Z pachanga $
 * @package    tests_runner
 */
require_once(dirname(__FILE__) . '/../../src/lmbTestTree.class.php');
require_once(dirname(__FILE__) . '/../../src/lmbTestTreeDirNode.class.php');
require_once(dirname(__FILE__) . '/../../src/lmbTestGroup.class.php');

Mock :: generate('lmbTestTreeDirNode', 'MockTestTreeNode');
Mock :: generate('lmbTestGroup', 'MockTestGroup');

class lmbTestTreeTest extends UnitTestCase
{
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
