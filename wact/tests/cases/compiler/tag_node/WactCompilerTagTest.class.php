<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/tests/cases/compiler/compile_tree_node/WactCompileTreeNodeTest.class.php');

if(!class_exists('WactCompilerTagTest'))
{

class MyTagClass1CompilerTest extends WactCompilerTag{}

class MyTagClass2CompilerTest extends WactCompilerTag{}

class WactCompilerTagTest extends WactCompileTreeNodeTest
{
  protected $tag_info;
  protected $source_location;

  function setUp()
  {
    $this->tag_info = new WactTagInfo('CompilerTag', 'whatever');
    $this->source_location = new WactSourceLocation('my_file', 10);

    parent :: setUp();
  }

  protected function _createNode()
  {
    return new WactCompilerTag($this->source_location, 'my_tag', $this->tag_info);
  }

  function testGetClientId()
  {
    $this->component->setAttribute('id', 'TestId');
    $this->assertEqual($this->component->getClientId(), 'TestId');
  }

  function testGetClientIdUnset()
  {
    $this->assertNull($this->component->getClientId());
  }

  function testGetServerIdFromId()
  {
    $this->component->setAttribute('id', 'TestId');
    $this->assertEqual($this->component->getServerId(), 'TestId');
  }

  function testGetServerIdFromWactId()
  {
    $this->component->setAttribute('wact:id', 'TestId');
    $this->assertEqual($this->component->getServerId(), 'TestId');
  }

  function testGetAttribute()
  {
    $this->component->setAttribute('foo', 'bar');
    $this->assertEqual($this->component->getAttribute('foo'), 'bar');
    $this->assertEqual($this->component->getAttribute('FOO'), 'bar');
  }

  function testGetAttributeUnset()
  {
    $this->assertNull($this->component->getAttribute('foo'));
  }

  function testHasAttribute()
  {
    $this->component->setAttribute('foo', 'bar');
    $this->component->setAttribute('tricky', NULL);
    $this->assertTrue( $this->component->hasAttribute('foo'));
    $this->assertTrue( $this->component->hasAttribute('tricky'));
    $this->assertFalse( $this->component->hasAttribute('missing'));
    $this->assertTrue( $this->component->hasAttribute('FOO'));
    $this->assertTrue( $this->component->hasAttribute('TRICKY'));
    $this->assertFalse( $this->component->hasAttribute('MISSING'));
  }

  function testDuplicateAttribute()
  {
    // Once set, attributes at compile time are immutable.
    $this->component->setAttribute('SAME', 'value 1');
    try
    {
      $this->component->setAttribute('same', 'value 2');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Duplicate attribute/', $e->getMessage());
      $this->assertEqual($e->getParam('attribute'), 'same');
    }
  }

  function testRemoveAttribute()
  {
    $this->component->setAttribute('foo', 'bar');
    $this->component->setAttribute('untouched', 'value');
    $this->assertTrue( $this->component->hasAttribute('foo'));
    $this->component->removeAttribute('FOO');
    $this->assertFalse( $this->component->hasAttribute('foo'));
  }

  function testBooleanAttribute()
  {
    //true cases
    $this->component->setAttribute('A', NULL);
    $this->assertTrue( $this->component->getBoolAttribute('A'));

    $this->component->setAttribute('B', 'True');
    $this->assertTrue( $this->component->getBoolAttribute('B'));

    $this->component->setAttribute('C', 'Something');
    $this->assertTrue( $this->component->getBoolAttribute('C'));

    //false cases
    $this->component->setAttribute('D', 'False');
    $this->assertFalse( $this->component->getBoolAttribute('D'));

    $this->assertFalse( $this->component->getBoolAttribute('E'));

    $this->component->setAttribute('F', 'n');
    $this->assertFalse( $this->component->getBoolAttribute('F'));

    $this->component->setAttribute('G', 'No');
    $this->assertFalse( $this->component->getBoolAttribute('G'));

    $this->component->setAttribute('H', 'none');
    $this->assertFalse( $this->component->getBoolAttribute('H'));

    $this->component->setAttribute('I', '0');
    $this->assertFalse( $this->component->getBoolAttribute('I'));
  }

  function testAddChildAttribute()
  {
    $attrib = new WactAttribute('Foo', 'bar');
    $this->component->addChildAttribute($attrib);
    $this->assertEqual($this->component->getAttribute('FOO'), 'bar');
  }

  function testGetAttributesAsArray()
  {
    $this->component->setAttribute('foo', 'bar');
    $this->component->setAttribute('tricky', NULL);
    $this->assertIdentical($this->component->getAttributesAsArray(array('tricky')), array('foo'  => 'bar'));
  }

  function testPreparseAndCheckForRequiredConstantAttributes()
  {
    $this->tag_info->setRequiredConstantAttributes(array('bar'));
    $this->component->setAttribute('bar', 'value');
    $this->component->preParse();
  }

  function testPreparseAndCheckForRequiredAttributes()
  {
    $this->tag_info->setRequiredAttributes(array('bar'));
    $this->component->setAttribute('bar', null);
    $this->component->preParse();
  }

  function testPreparseAndCheckForMissedRequiredConstantAttributes()
  {
    $this->tag_info->setRequiredConstantAttributes(array('bar'));
    try
    {
      $this->component->preParse();
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Missing required attribute/', $e->getMessage());
      $this->assertEqual($e->getParam('attribute'), 'bar');
    }
  }

  function testPreparseAndCheckForMissedRequiredAttributes()
  {
    $this->tag_info->setRequiredAttributes(array('bar'));

    try
    {
      $this->component->preParse();
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Missing required attribute/', $e->getMessage());
      $this->assertEqual($e->getParam('attribute'), 'bar');
    }
  }

  function testRestrictSelfNesting()
  {
    $tag_info = new WactTagInfo('CompilerTag', 'whatever');
    $tag_info->setRestrictSelfNesting(true);

    $component = new WactCompilerTag(new WactSourceLocation('my_file', 13), 'whatever', $tag_info);

    $parent = new WactCompilerTag(new WactSourceLocation('my_file', 10), 'whatEver', $tag_info);
    $component->parent = $parent;

    try
    {
      $component->preParse();
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Tag cannot be nested within the same tag/', $e->getMessage());
      $this->assertEqual($e->getParam('same_tag_file'), 'my_file');
      $this->assertEqual($e->getParam('same_tag_line'), 10);
    }
  }

  function testCheckParentTagClassOk()
  {
    $this->tag_info->setParentTagClass('MyTagClass1CompilerTest');

    $parent = new MyTagClass1CompilerTest(null, null, null);
    $this->component->parent = $parent;

    $this->component->preParse();
  }

  function testCheckParentTagClassException()
  {
    $this->tag_info->setParentTagClass('MyTagClass1CompilerTest');

    $parent = new MyTagClass2CompilerTest(null, null, null);
    $this->component->parent = $parent;

    try
    {
      $this->component->preParse();
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Tag must be enclosed by a proper parent tag/', $e->getMessage());
      $this->assertEqual($e->getParam('required_parent_tag_class'), 'MyTagClass1CompilerTest');
      $this->assertEqual($e->getParam('file'), $this->source_location->getFile());
      $this->assertEqual($e->getParam('line'), $this->source_location->getLine());
    }
  }
}

}


