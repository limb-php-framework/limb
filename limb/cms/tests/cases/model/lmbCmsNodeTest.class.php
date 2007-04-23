<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsNodeTest.class.php 5752 2007-04-23 14:14:56Z serega $
 * @package    cms
 */
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');

class lmbTestingNodeObject extends lmbActiveRecord
{
  protected $_db_table_name = 'testing_node_object';

  protected $_has_one = array('node' => array('field' => 'node_id',
                                              'class' => 'lmbCmsNode'));
}

class lmbTestingNodeChild extends lmbCmsNode{}

class lmbCmsNodeTest extends UnitTestCase
{
  protected $db;
  protected $tree;

  function setUp()
  {
    $toolkit = lmbToolkit :: instance();
    $this->db = new lmbSimpleDb($toolkit->getDefaultDbConnection());
    $this->tree = $toolkit->getCmsTree();

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->tree->deleteAll();
    $this->db->delete('class_name');
    $this->db->delete('testing_node_object');
  }

  function testSavingNewNodeInEmptyTreeCreatesTreeRootNode()
  {
    $this->assertNull($this->tree->getRootNode());

    $node = new lmbCmsNode();
    $node->setTitle($title = 'Some title');
    $node->setIdentifier($identifier = 'my_node');
    $node->setControllerName('MyController');
    $node->save();

    $node2 = lmbActiveRecord :: findById('lmbCmsNode', $node->getId());
    $this->assertEqual($node2->title, $title);
    $this->assertEqual($node2->identifier, $identifier);
    $this->assertEqual($node2->id, $node->getId());
    $this->assertEqual($node2->parent_id, $this->tree->getRootNode()->get('id'));
    $this->assertEqual($node2->children, 0);

    $root_node = $node2->getParent();
    $this->assertEqual($root_node->identifier, '');
    $this->assertEqual($root_node->id, $node2->parent_id);
  }

  function testLoadByPath()
  {
    $object1 = $this->_createNode('root', $parent_node = null);
    $object2 = $this->_createNode('child', $object1);

    $object3 = new lmbCmsNode();
    $object3->loadByPath('/root/child');
    $this->assertEqual($object3->title, $object2->title);

    $object4 = new lmbCmsNode();
    $object4->loadByPath('/root');
    $this->assertEqual($object4->title, $object1->title);
  }

  function testPreserveNodeClass()
  {
    $node = new lmbTestingNodeChild();
    $node->setIdentifier('node2');
    $node->setTitle('title_a');
    $node->save();

    $nodes = lmbActiveRecord :: find('lmbCmsNode', 'id = '. $node->id);
    $nodes->rewind();
    $node2 = $nodes->current();
    $this->assertIsA($node2, 'lmbTestingNodeChild');
    $this->assertEqual($node2->title, $node->title);
  }

  function testDestroyAlsoRemovesChildren()
  {
    $node1 = $this->_createNode('root', $parent_node = null);
    $node2 = $this->_createNode('child', $node1);

    $this->assertEqual($this->tree->countChildrenAll('/'), 2);

    $node3 = lmbActiveRecord :: findById('lmbCmsNode', $node1->getId());

    $node3->destroy();

    $this->assertEqual($this->tree->countChildrenAll('/'), 0);
  }

  function testGetChildren()
  {
    $object1 = $this->_createNode('root', $parent_node = null);
    $object2 = $this->_createNode('folder1', $object1);
    $object3 = $this->_createNode('child1', $object2);
    $object4 = $this->_createNode('child2', $object2);

    $object5 = lmbActiveRecord :: findById('lmbCmsNode', $object2->getId());
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

  function testSaveAndLoadLinkedObject()
  {
    $node = $this->_createNode('root', $parent_node = null);
    $object = new lmbTestingNodeObject();
    $object->setContent('test content');
    $node->setObject($object);
    $object->setNode($node);
    $node->save();

    $test_node = lmbActiveRecord :: findById('lmbCmsNode', $node->getId());
    $this->assertEqual($test_node->getObject()->getContent(), $object->getContent());

    $object2 = lmbActiveRecord :: findById('lmbTestingNodeObject', $object->getId());
    $this->assertEqual($object2->content, $object->content);
    $this->assertEqual($object2->getNode()->title, $node->title);
  }

  function testGetObjectOnNoLinkedObject()
  {
    $node = $this->_createNode('root');
    $this->assertNull($node->getObject());
  }

  function testDestroyNodeWithLinkedObject()
  {
    $node1 = $this->_createNode('root');
    $object1 = $this->_initObject($node1);
    $node1->save();

    $node1->destroy();

    $nodes = lmbActiveRecord :: find('lmbCmsNode'); //it's a root node
    $this->assertEqual($nodes->count(), 1);
    $this->assertEqual($nodes[0]->getParentId(), 0);//root

    $objects = lmbActiveRecord :: find('lmbTestingNodeObject');
    $this->assertEqual($objects->count(), 0);
  }

  function testDestroyKidsWithLinkedObjects()
  {
    $node1 = $this->_createNode('root');
    $object1 = $this->_initObject($node1);
    $node1->save();

    $node2 = $this->_createNode('node2', $node1);
    $object2 = $this->_initObject($node2);
    $node2->save();

    $node1->destroy();

    $nodes = lmbActiveRecord :: find('lmbCmsNode'); //it's a root node
    $this->assertEqual($nodes->count(), 1);
    $this->assertEqual($nodes[0]->getParentId(), 0);//root

    $objects = lmbActiveRecord :: find('lmbTestingNodeObject');
    $this->assertEqual($objects->count(), 0);
  }

  function testDestroyLinkedObjectDoDestroyNode()
  {
    $node1 = $this->_createNode('root');
    $object1 = $this->_initObject($node1);
    $node1->save();

    $object2 = lmbActiveRecord :: findById('lmbTestingNodeObject', $object1->id);
    $object2->destroy();

    $nodes = lmbActiveRecord :: find('lmbCmsNode'); //it's a root node
    $this->assertEqual($nodes->count(), 1);
    $this->assertEqual($nodes[0]->getParentId(), 0);
  }

  function testDestroyNodeUsingTreeAlgorithm()
  {
    $node1 = $this->_createNode('root');
    $object1 = $this->_initObject($node1);
    $node1->save();

    $node2 = $this->_createNode('folder', $node1);
    $object2 = $this->_initObject($node2);
    $node2->save();

    $node3 = $this->_createNode('child', $node2);
    $object3 = $this->_initObject($node3);
    $node3->save();

    $node1_1 = lmbActiveRecord :: findById('lmbCmsNode', $node1->id);
    $this->assertEqual($this->tree->countChildren($node1_1), 1);

    $node2->destroy();

    $node1_2 = lmbActiveRecord :: findById('lmbCmsNode', $node1->id);
    $this->assertEqual($this->tree->countChildren($node1_2), 0);

    $nodes = lmbActiveRecord :: find('lmbCmsNode');
    $this->assertEqual($nodes[0]->getParentId(), 0);//root
    $this->assertEqual($nodes[1]->getId(), $node1->getId());

    $objects = lmbActiveRecord :: find('lmbTestingNodeObject');
    $this->assertEqual($objects->count(), 1);
  }

  function testSaveAndLoadControllerName()
  {
    $node = $this->_createNode('root');
    $node->setControllerName('SomeController');
    $node->save();

    $node1 = lmbActiveRecord :: findById('lmbCmsNode', $node->id);
    $this->assertEqual($node1->getControllerName(), 'SomeController');
  }

  function testSaveNodeFromObject()
  {
    $node = $this->_initNode('root');
    $object = $this->_initObject($node);
    $object->save();

    $node1 = lmbActiveRecord :: findById('lmbCmsNode', $node->id);
    $this->assertEqual($node1->getObject()->content, $object->content);
  }

  function testGenerateNewIdentifier()
  {
    $node = $this->_createNode('root');
    $identifier = lmbCmsNode :: generateIdentifier($node->id);
    $this->assertEqual($identifier, 1);
  }

  function testGenerateNewIdentifierAfterNonNumericIdentifier()
  {
    $root = $this->_createNode('root');
    $child1 = $this->_createNode('alfa', $root);
    $identifier = lmbCmsNode :: generateIdentifier($root->id);
    $this->assertEqual($identifier, 'alfa1');
  }

  function testGenerateNewIdentifierAfterNumericIdentifier()
  {
    $root = $this->_createNode('root');
    $child1 = $this->_createNode('alfa', $root);
    $child2 = $this->_createNode('alfa1', $root);
    $identifier = lmbCmsNode :: generateIdentifier($root->id);
    $this->assertEqual($identifier, 'alfa2');
  }

  protected function _initObject($node)
  {
    $object = new lmbTestingNodeObject();
    $object->setContent('test content' . mt_rand(0, 1000));
    $node->setObject($object);
    $object->setNode($node);
    return $object;
  }

  protected function _initNode($node_identifier, $parent_node = null)
  {
    $node = new lmbCmsNode();
    $node->setTitle('title_'. mt_rand(0, 10000));
    $node->setIdentifier($node_identifier);
    $node->setControllerName('lmbController');
    if($parent_node)
      $node->setParent($parent_node);
    return $node;
  }

  protected function _createNode($node_identifier, $parent_node = null)
  {
    $node = $this->_initNode($node_identifier, $parent_node);
    $node->save();
    return $node;
  }
}

?>
