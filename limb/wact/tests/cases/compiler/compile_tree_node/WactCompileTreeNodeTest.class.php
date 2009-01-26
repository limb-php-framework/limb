<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once('limb/wact/src/compiler/templatecompiler.inc.php');
require_once('limb/wact/src/WactTemplate.class.php');

Mock::generate('WactCompileTreeNode', 'MockWactCompileTreeNode');
Mock::generate('WactCodeWriter', 'MockWactCodeWriter');

if(!class_exists('WactCompileTreeNodeTest'))
{

class WactCompileTreeNodeTest extends UnitTestCase
{
  protected $component;

  function setUp()
  {
    $this->component = $this->_createNode();
  }

  protected function _createNode()
  {
    return new WactCompileTreeNode(new WactSourceLocation('my_file', 10));
  }

  function testGetServerIdAttribute()
  {
    $this->component->ServerId = 'Test';
    $this->assertEqual($this->component->getServerId(), 'Test');
  }

  function testGetServerIdGenerated()
  {
    $id = $this->component->getServerId();
    $this->assertEqual($this->component->getServerId(), $id);
  }

  function testFindChild()
  {
    $mock = new MockWactCompileTreeNode();
    $mock->setReturnValue('getServerId', 'Test');
    $this->component->addChild($mock);
    $this->assertIsA($this->component->findChild('Test'), 'MockWactCompileTreeNode');
  }

  function testFindChildNotFound()
  {
    $this->assertFalse($this->component->findChild('Test'));
  }

  function testFindChildByClass()
  {
    $mock = new MockWactCompileTreeNode();
    $this->component->addChild($mock);
    $this->assertIsA($this->component->findChildByClass('MockWactCompileTreeNode'), 'MockWactCompileTreeNode');
  }

  function testFindChildByClassNotFound()
  {
    $this->assertFalse($this->component->findChildByClass('MockWactCompileTreeNode'));
  }

  function testFindUpChild()
  {
    $child1 = new WactCompileTreeNode();
    $child1->ServerId = 'child1';

    $child2 = new WactCompileTreeNode();
    $child2->ServerId = 'child2';

    $parent1 = new WactCompileTreeNode();
    $parent1->ServerId = 'parent1';
    $parent1->addChild($child1);

    $parent2 = new WactCompileTreeNode();
    $parent2->ServerId = 'parent2';
    $parent2->addChild($child2);

    $this->component->addChild($parent1);
    $this->component->addChild($parent2);

    $this->assertReference($parent2->findUpChild('child1'), $child1);
  }

  function testFindParentByChilld()
  {
    $parent = new WactCompileTreeNode();
    $parent->addChild($this->component);
    $this->assertIsA($this->component->findParentByClass('WactCompileTreeNode'), 'WactCompileTreeNode');
  }

  function testFindParentByClassNotFound()
  {
    $this->assertFalse($this->component->findParentByClass('Test'));
  }

  function testRemoveChild()
  {
    $mock = new MockWactCompileTreeNode();
    $mock->setReturnValue('getServerId', 'Test');
    $this->component->addChild($mock);
    $this->assertIsA($this->component->removeChild('Test'), 'MockWactCompileTreeNode');
  }

  function testGetChildren()
  {
    $mock = new MockWactCompileTreeNode();
    $this->component->addChild($mock);
    $children = $this->component->getChildren();
    $this->assertReference($mock, $children[0]);
  }

  function testPrepare()
  {
    $child = new MockWactCompileTreeNode();
    $this->component->addChild($child);
    $child->expectCallCount('prepare', 1);
    $this->component->prepare();
  }

  function testGetDataSource()
  {
    $parent = new MockWactCompileTreeNode();
    $ds = new ArrayObject();
    $parent->setReturnValue('getDataSource', $ds);
    $this->component->parent = $parent;
    $this->assertIsA($this->component->getDataSource(), 'ArrayObject');
  }

  function testGetParentDataSource() {
      /* This test case is broken
      $parent = &new MockWactCompileTreeNode($this);
      $ds = new ArrayObject();
      $testparent = new MockWactCompileTreeNode($this);
      $testparent->expectCallCount('getDataSource', 1);
      $mockds->parent = & $testparent;
      $parent->setReturnValue('getDataSource', $ds);
      $this->component->parent = & $parent;
      */
  }

  function testGetRootDataSource()
  {
    $parent = new MockWactCompileTreeNode();
    $parent->parent = NULL;
    $this->component->parent = $parent;
    $this->assertIsA($this->component->getRootDataSource(), 'MockWactCompileTreeNode');
  }

  function testgetComponentRefCode()
  {
    $parent = new MockWactCompileTreeNode();
    $parent->setReturnValue('getComponentRefCode', 'Test');
    $this->component->parent = $parent;
    $this->assertEqual($this->component->getComponentRefCode(), 'Test');
  }

  function testGenerateConstructor()
  {
    $code_writer = new MockWactCodeWriter();
    $child = new MockWactCompileTreeNode();
    $child->expectCallCount('generateConstructor', 1);
    $this->component->addChild($child);
    $this->component->generateConstructor($code_writer);
  }

  function testGenerateContent()
  {
    $code_writer = new MockWactCodeWriter();
    $child = new MockWactCompileTreeNode();
    $child->expectCallCount('generate', 1);
    $this->component->addChild($child);
    $this->component->generateContent($code_writer);
  }

  function testGenerate()
  {
    $code_writer = new MockWactCodeWriter();
    $child = new MockWactCompileTreeNode();
    $child->expectCallCount('generate', 1);
    $this->component->addChild($child);
    $this->component->generate($code_writer);
  }

  function testCheckServerIdsOk()
  {
    $root = new WactCompileTreeNode();
    $child1 = new WactCompileTreeNode();
    $child1->ServerId = 'id1';

    $child2 = new WactCompileTreeNode();
    $child2->ServerId = 'id2';

    $root->addChild($child1);
    $root->addChild($child2);

    $root->checkChildrenServerIds();
  }

  function testDuplicateServerIdsError()
  {
    $root = new WactCompileTreeNode();
    $child1 = new WactCompileTreeNode(new WactSourceLocation('my_file', 10));
    $child1->ServerId = 'my_tag';
    $root->addChild($child1);

    $child2 = new WactCompileTreeNode(new WactSourceLocation('my_file2', 15));
    $child2->ServerId = 'my_tag';
    $root->addChild($child2);

    try
    {
      $root->checkChildrenServerIds();
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Duplicate "id" attribute/', $e->getMessage());
      $params = $e->getParams();
      $this->assertEqual($params['file'], 'my_file2');
      $this->assertEqual($params['line'], 15);
      $this->assertEqual($params['duplicate_component_file'], 'my_file');
      $this->assertEqual($params['duplicate_component_line'], 10);
    }
  }

  function testDuplicateIdIsLegalInDifferentBranches()
  {
    $root = new WactCompileTreeNode();

    $Branch = new WactCompileTreeNode();
    $root->addChild($Branch);

    $child1 = new WactCompileTreeNode();
    $child1->ServerId = 'my_tag';
    $Branch->addChild($child1);

    $child2 = new MockWactCompileTreeNode();
    $child2->ServerId = 'my_tag';
    $root->addChild($child2);

    $root->checkChildrenServerIds();
  }
}

}

