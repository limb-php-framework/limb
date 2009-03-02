<?php

lmb_require('limb/cms/tests/cases/lmbCmsTestCase.class.php');

class lmbActiveRecordTreeNodeTest extends lmbCmsTestCase
{
  protected $tables_to_cleanup = array('lmb_cms_document');
  
  function testSavingNewNodeInEmptyTreeCreatesTreeRootNode()
  {
    $document = $this->_createDocument($identifier = 'foo');

    $must_be_document = lmbActiveRecord :: findById('lmbCmsDocument', $document->getId());
    $this->assertEqual($must_be_document->identifier, $identifier);
    $this->assertEqual($must_be_document->id, $document->getId());
    $this->assertEqual($must_be_document->parent_id, $document->getTree()->getRootNode()->get('id'));
    $this->assertEqual($must_be_document->children->count(), 0);

    $root = $must_be_document->getParent();
    $this->assertEqual($root->identifier, '');
    $this->assertEqual($root->id, $must_be_document->parent_id);
  }

  function testDestroyAlsoRemovesChildren()
  {
    $root = lmbCmsDocument::findRoot();
    $parent = $this->_createDocument('parent', $root);
    $child = $this->_createDocument('child', $parent);

    $nodes = array($parent, $child);
    $this->assertEqual(count($root->getChildren(2)), count($nodes));

    $parent = lmbActiveRecord :: findById('lmbCmsDocument', $parent->getId());
    $parent->destroy();

    $this->assertEqual(count($root->getChildren(2)), 0);
  }

  function testGetChildren()
  {
    $object1 = $this->_createDocument('root', $parent_node = null);
    $object2 = $this->_createDocument('folder1', $object1);
    $object3 = $this->_createDocument('child1', $object2);
    $object4 = $this->_createDocument('child2', $object2);

    $object5 = lmbActiveRecord :: findById('lmbCmsDocument', $object2->getId());
    $children = $object5->getKids();
    $children->rewind();
    $this->assertTrue($children->valid());
    $child1 = $children->current();
    $this->assertEqual($child1->title, $object3->title);

    $children->next();
    $this->assertTrue($children->valid());
    $child2 = $children->current();
    $this->assertEqual($child2->title, $object4->title);
  }

  function testFindRoot()
  {
    //force creation of root node
    $this->_createDocument();

    if($this->assertNotNull($must_be_root = lmbCmsDocument::findRoot()))
      $this->assertNull($must_be_root->getParent());
  }

  /**
   * @todo fix error (hack)
   */
  function testIsRoot_Negative()
  {
    $root = lmbCmsDocument::findRoot();
    $node = $this->_createDocument('node', $root);

    $this->assertFalse($node->isRoot());
    if($this->assertTrue(is_object($node->getParent())))
      $this->assertTrue($node->getParent()->isRoot());
  }

  /**
   * @todo fix error (hack)
   */
  function testIsRoot_Positive()
  {
    //force tree init
    $node = $this->_createDocument('root', $parent_node = null);

    $root = lmbActiveRecord :: findOne('lmbCmsDocument', array('sort' => 'id'));

    $this->assertTrue($root->isRoot());
  }
}