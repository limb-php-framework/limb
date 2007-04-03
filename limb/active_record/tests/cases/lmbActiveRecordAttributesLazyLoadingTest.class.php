<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActiveRecordAttributesLazyLoadingTest.class.php 4984 2007-02-08 15:35:02Z pachanga $
 * @package    active_record
 */
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/dbal/src/lmbTableGateway.class.php');

class LazyTestOneTableObject extends lmbActiveRecord
{
  protected $_db_table_name = 'test_one_table_object';
  protected $_lazy_attributes = array('annotation', 'content');
}

class lmbActiveRecordAttributesLazyLoadingTest extends UnitTestCase
{
  var $conn = null;
  var $db = null;

  function setUp()
  {
    $this->conn = lmbToolkit :: instance()->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->conn);
    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('test_one_table_object');
  }

  function testLazyFind()
  {
    $object = $this->_createActiveRecord($annotation = 'Some annotation', $content = 'Some content');
    $object2 = lmbActiveRecord :: findById('LazyTestOneTableObject', $object->getId());

    $this->_checkLazyness($object2, $annotation, $content);
  }

  function testLazyLoadById()
  {
    $object = $this->_createActiveRecord($annotation = 'Some annotation', $content = 'Some content');

    $object2 = new LazyTestOneTableObject();
    $object2->loadById($object->getId());

    $this->_checkLazyness($object2, $annotation, $content);
  }

  function testExportIsNotLazy()
  {
    $object = $this->_createActiveRecord($annotation = 'Some annotation', $content = 'Some content');
    $object2 = lmbActiveRecord :: findById('LazyTestOneTableObject', $object->getId());
    $exported = $object2->export();
    $this->assertEqual($exported['annotation'], $annotation);
    $this->assertEqual($exported['content'], $content);
  }

  protected function _checkLazyness($object, $annotation, $content)
  {
    $this->assertTrue($object->hasAttribute('news_date'));

    $this->assertFalse($object->hasAttribute('annotation'));
    $this->assertEqual($object->getAnnotation(), $annotation);
    $this->assertTrue($object->hasAttribute('annotation'));

    $this->assertFalse($object->hasAttribute('content'));
    $this->assertEqual($object->getContent(), $content);
    $this->assertTrue($object->hasAttribute('content'));
  }

  protected function _createActiveRecord($annotation, $content)
  {
    $object = new LazyTestOneTableObject();
    $object->setAnnotation($annotation);
    $object->setContent($content);
    $object->save();
    return $object;
  }
}
?>
