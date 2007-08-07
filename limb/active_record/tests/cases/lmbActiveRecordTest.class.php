<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/active_record/src/lmbActiveRecord.class.php');
require_once('limb/dbal/src/lmbSimpleDb.class.php');
require_once('limb/dbal/src/lmbTableGateway.class.php');

class TestOneTableObject extends lmbActiveRecord
{
  protected $_db_table_name = 'test_one_table_object';
  protected $dummy;
}

class TestOneTableObjectFailing extends lmbActiveRecord
{
  var $fail;
  protected $_db_table_name = 'test_one_table_object';

  protected function _onAfterSave()
  {
    if(is_object($this->fail))
      throw $this->fail;
  }
}

class TestOneTableObjectWithCustomDestroy extends lmbActiveRecord
{
  protected $_db_table_name = 'test_one_table_object';

  function destroy()
  {
    parent :: destroy();
    echo "destroyed!";
  }
}

class TestOneTableObjectWithHooks extends TestOneTableObject
{
  protected function _onValidate()
  {
    echo '|on_validate|';
  }

  protected function _onBeforeUpdate()
  {
    echo '|on_before_update|';
  }

  protected function _onBeforeCreate()
  {
    echo '|on_before_create|';
  }

  protected function _onBeforeSave()
  {
    echo '|on_before_save|';
  }

  protected function _onAfterSave()
  {
    echo '|on_after_save|';
  }

  protected function _onSave()
  {
    echo '|on_save|';
  }

  protected function _onUpdate()
  {
    echo '|on_update|';
  }

  protected function _onCreate()
  {
    echo '|on_create|';
  }

  protected function _onAfterUpdate()
  {
    echo '|on_after_update|';
  }

  protected function _onAfterCreate()
  {
    echo '|on_after_create|';
  }

  protected function _onBeforeDestroy()
  {
    echo '|on_before_destroy|';
  }

  protected function _onAfterDestroy()
  {
    echo '|on_after_destroy|';
  }
}

class TestOneTableObjectWithSortParams extends TestOneTableObject
{
  protected $_default_sort_params = array('id' => 'DESC');
}

class lmbActiveRecordTest extends UnitTestCase
{
  var $conn;
  var $db;
  var $class_name = 'TestOneTableObject';

  function setUp()
  {
    $toolkit = lmbToolkit :: save();
    $this->conn = $toolkit->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->conn);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();

    lmbToolkit :: restore();
  }

  function _cleanUp()
  {
    $this->db->delete('test_one_table_object');
  }

  function testSaveNewRecord()
  {
    $object = new TestOneTableObject();
    $object->set('annotation', $annotation = 'Super annotation');
    $object->set('content', $content = 'Super content');
    $object->set('news_date', $news_date = '2005-01-10');

    $this->assertTrue($object->isNew());

    $id = $object->save();

    $this->assertFalse($object->isNew());
    $this->assertNotNull($object->getId());
    $this->assertEqual($object->getId(), $id);

    $this->assertEqual($this->db->count('test_one_table_object'), 1);

    $record = $this->db->selectRecord('test_one_table_object');
    $this->assertEqual($record->get('id'), $id);
    $this->assertEqual($record->get('annotation'), $annotation);
    $this->assertEqual($record->get('content'), $content);
    $this->assertEqual($record->get('news_date'), $news_date);
    $this->assertEqual($record->get('id'), $object->getId());
  }

  function testDontCreateNewRecordTwice()
  {
    $object = $this->_initActiveRecordWithData(new TestOneTableObject());

    $object->save();
    $object->save();

    $this->assertTrue($object->getId());

    $this->assertEqual($this->db->count('test_one_table_object'), 1);
  }

  function testIsNew()
  {
    $object = $this->_initActiveRecordWithData(new TestOneTableObject());
    $this->assertTrue($object->isNew());

    $object->save();
    $this->assertFalse($object->isNew());

    $object->setIsNew();

    $this->assertTrue($object->isNew());
  }

  function testDetach()
  {
    $object = $this->_initActiveRecordWithData(new TestOneTableObject());
    $this->assertTrue($object->isNew());

    $object->save();
    $this->assertFalse($object->isNew());
    $this->assertNotNull($object->getId());

    $object->detach();

    $this->assertTrue($object->isNew());
    $this->assertNull($object->getId());

    $object->save();

    $this->assertEqual($this->db->count('test_one_table_object'), 2);
  }

  function testSaveUpdate()
  {
    $object = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $object->set('annotation', $annotation = 'Other annotation');
    $object->set('content', $content = 'Other content');
    $object->set('news_date', $news_date = '2005-10-20');
    $object->save();

    $this->assertEqual($this->db->count('test_one_table_object'), 1);

    $record = $this->db->selectRecord('test_one_table_object');

    $this->assertEqual($record->get('annotation'), $annotation);
    $this->assertEqual($record->get('content'), $content);
    $this->assertEqual($record->get('news_date'), $news_date);
    $this->assertEqual($record->get('id'), $object->getId());
  }

  function testProperOrderOfCreateHooksCalls()
  {
    $object = new TestOneTableObjectWithHooks();
    $object->setContent('whatever');

    ob_start();
    $object->save();
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, '|on_before_save||on_before_create||on_validate||on_save||on_create||on_after_create||on_after_save|');
  }

  function testProperOrderOfUpdateHooksCalls()
  {
    $object = new TestOneTableObjectWithHooks();
    $object->setContent('whatever');
    ob_start();
    $object->save();
    ob_end_clean();

    $object->setContent('other content');

    ob_start();
    $object->save();
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, '|on_before_save||on_before_update||on_validate||on_save||on_update||on_after_update||on_after_save|');
  }

  function testProperOrderOfDestroyHooksCalls()
  {
    $object = new TestOneTableObjectWithHooks();
    $object->setContent('whatever');
    ob_start();
    $object->save();
    ob_clean();

    $object->destroy();
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, '|on_before_destroy||on_after_destroy|');
  }

  function testFindById()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $object3 = lmbActiveRecord :: findById($this->class_name, $object2->getId());
    $this->assertEqual($object3->export(), $object2->export());
  }

  function testFindByIdThrowsExceptionIfNotFound()
  {
    try
    {
      lmbActiveRecord :: findById($this->class_name, -1000);
      $this->assertTrue(false);
    }
    catch(lmbARException $e){}
  }

  function testFindByIdReturnsNullIfNotFound()
  {
    $this->assertNull(lmbActiveRecord :: findById($this->class_name, -1000, false));
  }

  function testLoadById()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $object3 = new TestOneTableObject();
    $object3->loadById($object2->getId());
    $this->assertEqual($object3->export(), $object2->export());
    $this->assertFalse($object3->isNew());
  }

  function testPassingIntToConstructorLoadsObject()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $object2 = new TestOneTableObject($object1->getId());
    $this->assertEqual($object2->export(), $object1->export());
    $this->assertFalse($object2->isNew());
  }

  function tesFindFirst()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $object3 = lmbActiveRecord :: findFirst($this->class_name, array('criteria' => 'id=' . $object1->getId()));

    $this->assertFalse($object2->isNew());
    $this->assertEqual($object3->get('annotation'), $object1->get('annotation'));
    $this->assertEqual($object3->get('content'), $object1->get('content'));
    $this->assertEqual($object3->get('news_date'), $object1->get('news_date'));
    $this->assertEqual($object3->get('id'), $object1->getId());
  }

  function tesFindFirstConvertStringToCriteria()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $object3 = lmbActiveRecord :: findFirst($this->class_name, 'id=' . $object1->getId());

    $this->assertFalse($object2->isNew());
    $this->assertEqual($object3->get('annotation'), $object1->get('annotation'));
    $this->assertEqual($object3->get('content'), $object1->get('content'));
    $this->assertEqual($object3->get('news_date'), $object1->get('news_date'));
    $this->assertEqual($object3->get('id'), $object1->getId());
  }

  function tesFindFirstConvertObjectToCriteria()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $object3 = lmbActiveRecord :: findFirst($this->class_name, new lmbSQLRawCriteria('id=' . $object1->getId()));

    $this->assertFalse($object2->isNew());
    $this->assertEqual($object3->get('annotation'), $object1->get('annotation'));
    $this->assertEqual($object3->get('content'), $object1->get('content'));
    $this->assertEqual($object3->get('news_date'), $object1->get('news_date'));
    $this->assertEqual($object3->get('id'), $object1->getId());
  }

  function tesFindFirstConvertArrayToCriteria()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $object3 = lmbActiveRecord :: findFirst($this->class_name, array('id=?', $object1->getId()));

    $this->assertFalse($object2->isNew());
    $this->assertEqual($object3->get('annotation'), $object1->get('annotation'));
    $this->assertEqual($object3->get('content'), $object1->get('content'));
    $this->assertEqual($object3->get('news_date'), $object1->get('news_date'));
    $this->assertEqual($object3->get('id'), $object1->getId());
  }

  function testFindFirstWithSortParams()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $object3 = lmbActiveRecord :: findFirst($this->class_name, array('sort' => array('id' => 'DESC')));

    $this->assertEqual($object3->get('id'), $object2->getId());
  }

  function testFindFirstWithDefaultSortParams()
  {
    $object1 = new TestOneTableObjectWithSortParams();
    $object1->setContent('Content'.mt_rand());
    $object1->save();

    $object2 = new TestOneTableObjectWithSortParams();
    $object2->setContent('Content'.mt_rand());
    $object2->save();

    $object3 = lmbActiveRecord :: findFirst('TestOneTableObjectWithSortParams');
    $this->assertEqual($object3->get('id'), $object2->getId());
  }

  function testFindOneAlias()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $object3 = lmbActiveRecord :: findOne($this->class_name, 'id=' . $object1->getId());

    $this->assertFalse($object2->isNew());
    $this->assertEqual($object3->get('annotation'), $object1->get('annotation'));
    $this->assertEqual($object3->get('content'), $object1->get('content'));
    $this->assertEqual($object3->get('news_date'), $object1->get('news_date'));
    $this->assertEqual($object3->get('id'), $object1->getId());
  }

  function testFindAllRecordsNoCriteria()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $object3 = new TestOneTableObject();
    $rs = $object3->findAllRecords();
    $rs->rewind();
    $this->assertEqual($object1->getId(), $rs->current()->get('id'));
    $rs->next();
    $this->assertEqual($object2->getId(), $rs->current()->get('id'));
  }

  function testFildAllRecordsWithCriteria()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $object3 = new TestOneTableObject();
    $rs = $object3->findAllRecords(new lmbSQLFieldCriteria('id', $object2->getId()));
    $rs->rewind();
    $this->assertEqual($object2->getId(), $rs->current()->get('id'));
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testFindAllNoCriteria()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $rs = lmbActiveRecord :: find($this->class_name);
    $rs->rewind();
    $this->assertEqual($object1->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertEqual($object2->getId(), $rs->current()->getId());
  }

  function testFindAllWithCriteria()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $rs = lmbActiveRecord :: find($this->class_name, array('criteria' => new lmbSQLFieldCriteria('id', $object2->getId())));
    $rs->rewind();
    $this->assertEqual($object2->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testFindConvertObjectToCriteria()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $rs = lmbActiveRecord :: find($this->class_name, new lmbSQLFieldCriteria('id', $object2->getId()));
    $rs->rewind();
    $this->assertEqual($object2->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testFindConvertStringToCriteria()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $rs = lmbActiveRecord :: find($this->class_name, 'id=' . $object2->getId());
    $rs->rewind();
    $this->assertEqual($object2->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testFindConvertArrayToCriteria()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $rs = lmbActiveRecord :: find($this->class_name, array('id=?', $object2->getId()));
    $rs->rewind();
    $this->assertEqual($object2->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testFindWithIntegerCallsFindById()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $object = lmbActiveRecord :: find($this->class_name, $object2->getId());
    $this->assertEqual($object2->getId(), $object->getId());
  }

  function testFindWithIntegerDoesNotThrowException()
  {
    $this->assertNull(lmbActiveRecord :: find($this->class_name, -10000));
  }

  function testFindByThrowsExceptionIfMagicParamsIsNull()
  {
    try
    {
      lmbActiveRecord :: find($this->class_name, null);
      $this->assertTrue(false);
    }
    catch(lmbARException $e){}
  }


  function testFindAllWithSortParams()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $rs = lmbActiveRecord :: find($this->class_name, array('sort' => array('id' => 'DESC')));
    $arr = $rs->getArray();
    $this->assertEqual($arr[0]->get('id'), $object2->getId());
    $this->assertEqual($arr[1]->get('id'), $object1->getId());
  }

  function testFindAllWithDefaultSortParams()
  {
    $object1 = new TestOneTableObjectWithSortParams();
    $object1->setContent('Content'.mt_rand());
    $object1->save();

    $object2 = new TestOneTableObjectWithSortParams();
    $object2->setContent('Content'.mt_rand());
    $object2->save();

    $rs = lmbActiveRecord :: find('TestOneTableObjectWithSortParams', array('sort' => array('id' => 'DESC')));
    $arr = $rs->getArray();
    $this->assertEqual($arr[0]->get('id'), $object2->getId());
    $this->assertEqual($arr[1]->get('id'), $object1->getId());
  }

  function testFindBySql()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $rs = lmbActiveRecord :: findBySql($this->class_name, 'select * from test_one_table_object order by id desc');
    $rs->rewind();
    $this->assertEqual($object2->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertEqual($object1->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testFindFirstBySql()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $object = lmbActiveRecord :: findFirstBySql($this->class_name, 'select * from test_one_table_object order by id desc');
    $this->assertEqual($object2->getId(), $object->getId());
  }

  function testFindByIds()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object3 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $rs = lmbActiveRecord :: findByIds($this->class_name,
                                       array($object1->getId(), $object3->getId()),
                                       array('sort' => array('id' => 'asc')));
    $rs->rewind();
    $this->assertEqual($object1->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertEqual($object3->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testFetchByIdsReturnEmptyIteratorIfNoIds()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $rs = lmbActiveRecord :: findByIds($this->class_name, array());
    $rs->rewind();
    $this->assertFalse($rs->valid());
  }

  function testGetDatasetActsAsStaticFind()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $ds = $object2->getDataset();
    $this->assertEqual($ds->at(0)->getId(), $object1->getId());
    $this->assertEqual($ds->at(1)->getId(), $object2->getId());
  }

  function testDelete()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    lmbActiveRecord :: delete($this->class_name);

    $this->assertEqual($this->db->count('test_one_table_object'), 0);
  }

  function testDeleteCallsDestroy()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObjectWithCustomDestroy());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObjectWithCustomDestroy());

    ob_start();
    lmbActiveRecord :: delete('TestOneTableObjectWithCustomDestroy');
    $contents = ob_get_contents();
    ob_end_clean();

    $this->assertEqual($contents, 'destroyed!destroyed!');
    $this->assertEqual($this->db->count('test_one_table_object'), 0);
  }

  function testDeleteByCriteria()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $criteria = new lmbSQLFieldCriteria('id', $object2->getId());
    lmbActiveRecord :: delete($this->class_name, $criteria);

    $this->assertEqual($this->db->count('test_one_table_object'), 1);

    $object3 = lmbActiveRecord :: findById($this->class_name, $object1->getId());
    $this->assertEqual($object3->getContent(), $object1->getContent());
  }

  function testDeleteRaw()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    lmbActiveRecord :: deleteRaw($this->class_name);

    $this->assertEqual($this->db->count('test_one_table_object'), 0);
  }

  function testDeleteRawDoesntCallDestroy()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObjectWithCustomDestroy());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObjectWithCustomDestroy());

    ob_start();
    lmbActiveRecord :: deleteRaw('TestOneTableObjectWithCustomDestroy');
    $contents = ob_get_contents();
    ob_end_clean();

    $this->assertEqual($contents, '');
    $this->assertEqual($this->db->count('test_one_table_object'), 0);
  }

  function testDeleteRawByCriteria()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    $criteria = new lmbSQLFieldCriteria('id', $object2->getId());
    lmbActiveRecord :: deleteRaw($this->class_name, $criteria);

    $this->assertEqual($this->db->count('test_one_table_object'), 1);

    $object3 = lmbActiveRecord :: findById($this->class_name, $object1->getId());
    $this->assertEqual($object3->getContent(), $object1->getContent());
  }

  function testUpdateRawAllWithArraySet()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    lmbActiveRecord :: updateRaw($this->class_name, array('content' => 'blah'));

    $rs = lmbActiveRecord :: find($this->class_name);
    $rs->rewind();
    $this->assertEqual($rs->current()->getContent(), 'blah');
    $rs->next();
    $this->assertEqual($rs->current()->getContent(), 'blah');
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testUpdateRawAllWithRawValues()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    lmbActiveRecord :: updateRaw($this->class_name, 'ordr=1');

    $rs = lmbActiveRecord :: find($this->class_name);
    $rs->rewind();
    $this->assertEqual($rs->current()->getOrdr(), 1);
    $rs->next();
    $this->assertEqual($rs->current()->getOrdr(), 1);
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testUpdateRawWithCriteria()
  {
    $object1 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());
    $object2 = $this->_initActiveRecordWithDataAndSave(new TestOneTableObject());

    lmbActiveRecord :: updateRaw($this->class_name, array('content' => 'blah'), 'id=' . $object2->getId());

    $rs = lmbActiveRecord :: find($this->class_name);
    $rs->rewind();
    $this->assertEqual($rs->current()->getContent(), $object1->getContent());
    $rs->next();
    $this->assertEqual($rs->current()->getContent(), 'blah');
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testGetTableName()
  {
    $object = new TestOneTableObject();
    $this->assertEqual($object->getTableName(), 'test_one_table_object');
  }

  protected function _initActiveRecordWithData($object)
  {
    $object->set('annotation', 'Annotation ' . time());
    $object->set('content', 'Content ' . time());
    $object->set('news_date', date("Y-m-d", time()));
    $object->set('ordr', mt_rand());
    return $object;
  }

  protected function _initActiveRecordWithDataAndSave($object)
  {
    $this->_initActiveRecordWithData($object);
    $object->save();
    return $object;
  }
}

