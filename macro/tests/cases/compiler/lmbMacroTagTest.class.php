<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

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
    $this->node = new lmbMacroTag($this->source_location, 'my_tag', $this->tag_info);
  }

  function testGetAttribute_NoSuchAttribute()
  {
    $this->assertNull($this->node->get('no_such_attribute'));
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
  
  function testGetConstantAttributes()
  {
    $this->node->set('foo', 'value1');
    $this->node->set('zoo', 'value2');
    $this->node->set('tricky', '$this->bar');
    $this->assertEqual($this->node->getConstantAttributes(), array('foo' => 'value1', 'zoo' => 'value2'));
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
  
  function testGetNodeId()
  {
    $this->node->setNodeId('Test');
    $this->assertEqual($this->node->getNodeId(), 'Test');
  }

  function testGetNodeIdGenerated()
  {
    $id = $this->node->getNodeId();
    $this->assertEqual($this->node->getNodeId(), $id);
  }
  
  function testGetNodeIdByDefault()
  {
    $this->assertNotNull($this->node->getNodeId());
  }
     
  function testGetNodeId_ByIdAttribute()
  {
    $this->node->set('id', 'my_tag');
    $this->assertEqual($this->node->getNodeId(), 'my_tag');
  }

  function testGetNodeId_DontUseDynamicIdAttribute()
  {
    $this->node->set('id', '$my_tag');
    $this->assertNotEqual($this->node->getNodeId(), '$my_tag');
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

