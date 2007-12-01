<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/lmbMacroNode.class.php');
lmb_require('limb/macro/src/lmbMacroTag.class.php');
lmb_require('limb/macro/src/lmbMacroTagInfo.class.php');
lmb_require('limb/macro/src/lmbMacroSourceLocation.class.php');
lmb_require('limb/macro/src/lmbMacroCodeWriter.class.php');
lmb_require('limb/macro/src/lmbMacroCompiler.class.php');
lmb_require('limb/macro/src/lmbMacroTagAttribute.class.php');

class MacroTagClass1CompilerTest extends lmbMacroTag{}
class MacroTagClass2CompilerTest extends lmbMacroTag{}

Mock::generate('lmbMacroNode', 'MockMacroNode');
Mock::generate('lmbMacroCodeWriter', 'MockMacroCodeWriter');
Mock::generate('lmbMacroCompiler', 'MockMacroCompiler');
Mock::generate('lmbMacroTagAttribute', 'MockMacroTagAttribute');

class lmbMacroTagTest extends UnitTestCase
{
  protected $node;
  protected $tag_info;
  protected $source_location;

  function setUp()
  {
    $this->tag_info = new lmbMacroTagInfo('MacroTag', 'whatever');
    $this->source_location = new lmbMacroSourceLocation('my_file', 10);
    $this->node = $this->_createNode();
  }

  protected function _createNode()
  {
    return new lmbMacroTag($this->source_location, 'my_tag', $this->tag_info);
  }

  function testGetIdAttribute()
  {
    $this->node->setId('Test');
    $this->assertEqual($this->node->getId(), 'Test');
  }

  function testGetIdGenerated()
  {
    $id = $this->node->getId();
    $this->assertEqual($this->node->getId(), $id);
  }

  function testFindChild()
  {
    $mock = new MockMacroNode();
    $mock->setReturnValue('getId', 'Test');
    $this->node->addChild($mock);
    $this->assertEqual($this->node->findChild('Test')->getId(), 'Test');
  }

  function testFindChildInMany()
  {
    $node1 = new MockMacroNode();
    $node1->setReturnValue('getId', 'foo');
    $node2 = new MockMacroNode();
    $node2->setReturnValue('getId', 'bar');
    $this->node->addChild($node1);
    $this->node->addChild($node2);
    $this->assertEqual($this->node->findChild('bar')->getId(), 'bar');
  }

  function testFindChildNotFound()
  {
    $this->assertFalse($this->node->findChild('Test'));
  }

  function testFindUpChild()
  {
    $node1 = new lmbMacroNode();
    $node1->setId('foo');
    $node2 = new lmbMacroNode();
    $node2->setId('bar');
    $parent1 = new lmbMacroNode();
    $parent1->setId('parent1');
    $parent2 = new lmbMacroNode();
    $parent2->setId('parent2');
    
    $parent1->addChild($node1);
    $parent2->addChild($node2);
    
    $this->node->addChild($parent1);
    $this->node->addChild($parent2);
    
    $this->assertEqual($node2->findUpChild('foo')->getId(), $node1->getId());
    $this->assertEqual($parent1->findUpChild('parent2')->getId(), $parent2->getId());
    $this->assertEqual($parent1->findUpChild('foo')->getId(), $node1->getId());
  }

  function testFindChildByClass()
  {
    $mock = new MockMacroNode();
    $this->node->addChild($mock);
    $this->assertIsA($this->node->findChildByClass('MockMacroNode'), 'MockMacroNode');
  }

  function testFindChildByClassNotFound()
  {
    $this->assertFalse($this->node->findChildByClass('Booo'));
  }

  function testFindParentByChild()
  {
    $parent = new lmbMacroNode;
    $parent->addChild($this->node);
    $this->assertIsA($this->node->findParentByClass('lmbMacroNode'), 'lmbMacroNode');
  }

  function testFindParentByClassNotFound()
  {
    $this->assertFalse($this->node->findParentByClass('Test'));
  }

  function testRemoveChild()
  {
    $mock = new MockMacroNode();
    $mock->setReturnValue('getId', 'Test');
    $this->node->addChild($mock);
    $this->assertIsA($this->node->removeChild('Test'), 'MockMacroNode');
  }

  function testGetChildren()
  {
    $mock = new MockMacroNode();
    $this->node->addChild($mock);
    $children = $this->node->getChildren();
    $this->assertReference($mock, $children[0]);
  }

  function testGenerate()
  {
    $code_writer = new MockMacroCodeWriter();
    $child = new MockMacroNode();
    $child->expectCallCount('generate', 1);
    $this->node->addChild($child);
    $this->node->generate($code_writer);
  }

  function testGenerateCallsPreGenerateForAttributes()
  {
    $code_writer = new MockMacroCodeWriter();
    $attribute = new MockMacroTagAttribute();
    $attribute->expectOnce('preGenerate');
    $this->node->add($attribute);
    $this->node->generate($code_writer);
  }

  function testCheckIdsOk()
  {
    $root = new lmbMacroNode;
    $child1 = new lmbMacroNode;
    $child1->setId('id1');

    $child2 = new lmbMacroNode;
    $child2->setId('id2');

    $root->addChild($child1);
    $root->addChild($child2);

    $root->checkChildrenIds();
  }

  function testDuplicateIdsError()
  {
    $root = new lmbMacroNode;
    $child1 = new lmbMacroNode(new lmbMacroSourceLocation('my_file', 10));
    $child1->setId('my_tag');
    $root->addChild($child1);

    $child2 = new lmbMacroNode(new lmbMacroSourceLocation('my_file2', 15));
    $child2->setId('my_tag');
    $root->addChild($child2);

    try
    {
      $root->checkChildrenIds();
      $this->assertTrue(false);
    }
    catch(lmbMacroException $e)
    {
      $this->assertWantedPattern('/Duplicate "id" attribute/', $e->getMessage());
      $params = $e->getParams();
      $this->assertEqual($params['file'], 'my_file2');
      $this->assertEqual($params['line'], 15);
      $this->assertEqual($params['duplicate_node_file'], 'my_file');
      $this->assertEqual($params['duplicate_node_line'], 10);
    }
  }

  function testDuplicateIdIsLegalInDifferentBranches()
  {
    $root = new lmbMacroNode;

    $Branch = new lmbMacroNode;
    $root->addChild($Branch);

    $child1 = new lmbMacroNode;
    $child1->setId('my_tag');
    $Branch->addChild($child1);

    $child2 = new MockMacroNode();
    $child2->setId('my_tag');
    $root->addChild($child2);

    $root->checkChildrenIds();
  }

  function testGetIdByDefault()
  {
    $this->assertNotNull($this->node->getId());
  }

  function testGetId()
  {
    $this->node->setId('TestId');
    $this->assertEqual($this->node->getId(), 'TestId');
  }
  
  function testGetAttributeUnset()
  {
    $this->assertNull($this->node->get('foo'));
  }

  function testGetAttribute()
  {
    $this->node->set('foo', 'bar');
    $this->assertEqual($this->node->get('foo'), 'bar');
    $this->assertEqual($this->node->get('FOO'), 'bar');
  }

  function testHasAttribute()
  {
    $this->node->set('foo', 'bar');
    $this->node->set('tricky', NULL);
    $this->assertTrue($this->node->has('foo'));
    $this->assertTrue($this->node->has('tricky'));
    $this->assertFalse($this->node->has('missing'));
    $this->assertTrue($this->node->has('FOO'));
    $this->assertTrue($this->node->has('TRICKY'));
    $this->assertFalse($this->node->has('MISSING'));
  }
  
  function testHasConstantAttribute()
  {
    $this->node->set('foo', 'bar');
    $this->node->set('tricky', '$this->bar');
    
    $this->assertTrue($this->node->hasConstant('foo'));
    $this->assertFalse($this->node->hasConstant('tricky'));
  }

  function testRemoveAttribute()
  {
    $this->node->set('foo', 'bar');
    $this->node->set('untouched', 'value');
    $this->assertTrue($this->node->has('foo'));
    $this->node->remove('FOO');
    $this->assertFalse($this->node->has('foo'));
  }

  function testBooleanAttribute()
  {
    //true cases
    $this->node->set('B', 'True');
    $this->assertTrue($this->node->getBool('B'));

    $this->node->set('C', 'Something');
    $this->assertTrue($this->node->getBool('C'));

    //false cases
    $this->node->set('A', NULL);
    $this->assertFalse($this->node->getBool('A'));

    $this->node->set('D', 'False');
    $this->assertFalse($this->node->getBool('D'));

    $this->assertFalse($this->node->getBool('E'));

    $this->node->set('F', 'n');
    $this->assertFalse($this->node->getBool('F'));

    $this->node->set('G', 'No');
    $this->assertFalse($this->node->getBool('G'));

    $this->node->set('H', 'none');
    $this->assertFalse($this->node->getBool('H'));

    $this->node->set('I', '0');
    $this->assertFalse($this->node->getBool('I'));
  }

  function testPreparseAndCheckForRequiredAttributes()
  {
    $this->tag_info->setRequiredAttributes(array('bar'));
    $this->node->set('bar', null);
    $this->node->preParse(new MockMacroCompiler());
  }

  function testPreparseAndCheckForMissedRequiredAttributes()
  {
    $this->tag_info->setRequiredAttributes(array('bar'));

    try
    {
      $this->node->preParse(new MockMacroCompiler());
      $this->assertTrue(false);
    }
    catch(lmbMacroException $e)
    {
      $this->assertWantedPattern('/Missing required attribute/', $e->getMessage());
      $this->assertEqual($e->getParam('attribute'), 'bar');
    }
  }

  function testRestrictSelfNesting()
  {
    $tag_info = new lmbMacroTagInfo('CompilerTag', 'whatever');
    $tag_info->setRestrictSelfNesting(true);

    $node = new lmbMacroTag(new lmbMacroSourceLocation('my_file', 13), 'whatever', $tag_info);

    $parent = new lmbMacroTag(new lmbMacroSourceLocation('my_file', 10), 'whatEver', $tag_info);
    $node->setParent($parent);

    try
    {
      $node->preParse(new MockMacroCompiler());
      $this->assertTrue(false);
    }
    catch(lmbMacroException $e)
    {
      $this->assertWantedPattern('/Tag cannot be nested within the same tag/', $e->getMessage());
      $this->assertEqual($e->getParam('same_tag_file'), 'my_file');
      $this->assertEqual($e->getParam('same_tag_line'), 10);
    }
  }

  function testCheckParentTagClassOk()
  {
    $this->tag_info->setParentClass('MacroTagClass1CompilerTest');

    $parent = new MacroTagClass1CompilerTest(null, null, null);
    $this->node->setParent($parent);

    $this->node->preParse(new MockMacroCompiler());
  }

  function testCheckParentTagClassException()
  {
    $this->tag_info->setParentClass('MacroTagClass1CompilerTest');

    $parent = new MacroTagClass2CompilerTest(null, null, null);
    $this->node->setParent($parent);

    try
    {
      $this->node->preParse(new MockMacroCompiler());
      $this->assertTrue(false);
    }
    catch(lmbMacroException $e)
    {
      $this->assertWantedPattern('/Tag must be enclosed by a proper parent tag/', $e->getMessage());
      $this->assertEqual($e->getParam('required_parent_tag_class'), 'MacroTagClass1CompilerTest');
      $this->assertEqual($e->getParam('file'), $this->source_location->getFile());
      $this->assertEqual($e->getParam('line'), $this->source_location->getLine());
    }
  }
}

