<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/criteria/lmbSQLRawCriteria.class.php');

class TestOneTableObjectWithCustomProperty extends TestOneTableObject {
  protected $custom_property = true;
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

class lmbActiveRecordTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('test_one_table_object', 'lecture_for_test', 'course_for_test');

  function testArrayAccessConsidersDbFields()
  {
    $object = new TestOneTableObject();
    $this->assertTrue(isset($object['annotation']));
    unset($object['annotation']); // Does not make any sense since db fields always available
    $this->assertTrue(isset($object['annotation']));
  }

  function testGetCustomProperty() {
    $object = new TestOneTableObjectWithCustomProperty();
    $this->assertTrue($object->getCustomProperty());
  }

  function testGetWithDefaultValue()
  {
    $object = new TestOneTableObject();
    try
    {
      $object->get('foo');
      $this->fail();
    }
    catch (Exception $e)
    {
      $this->pass();
    }
    $this->assertEqual($object->get('foo', 'bar'), 'bar');
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
    $object = $this->creator->initOneTableObject();

    $object->save();
    $object->save();

    $this->assertTrue($object->getId());

    $this->assertEqual($this->db->count('test_one_table_object'), 1);
  }

  function testIsNew()
  {
    $object = $this->creator->initOneTableObject();
    $this->assertTrue($object->isNew());

    $object->save();
    $this->assertFalse($object->isNew());

    $object->setIsNew();

    $this->assertTrue($object->isNew());
  }

  function testDetach()
  {
    $object = $this->creator->initOneTableObject();
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
    $object = $this->creator->createOneTableObject();

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
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $found = lmbActiveRecord :: findById('TestOneTableObject', $object2->getId());
    $this->assertEqual($found->export(), $object2->export());

    //testing convenient alias
    $found = TestOneTableObject :: findById($object2->getId());
    $this->assertEqual($found->export(), $object2->export());
  }

  function testFindByIdThrowsExceptionIfNotFound()
  {
    try
    {
      lmbActiveRecord :: findById('TestOneTableObject', -1000);
      $this->assertTrue(false);
    }
    catch(lmbARException $e){}
  }

  function testFindByIdReturnsNullIfNotFound()
  {
    $this->assertNull(lmbActiveRecord :: findById('TestOneTableObject', -1000, false));
  }

  function testLoadById()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $loaded = new TestOneTableObject();
    $loaded->loadById($object2->getId());
    $this->assertEqual($loaded->export(), $object2->export());
    $this->assertFalse($loaded->isNew());
  }

  function testLoadByIdThrowsExceptionIfNotFound()
  {
    $loaded = new TestOneTableObject();
    try
    {
      $loaded->loadById(-10000);
      $this->assertTrue(false);
    }
    catch(lmbARException $e){}
  }

  function testPassingIntToConstructorLoadsObject()
  {
    $object1 = $this->creator->createOneTableObject();

    $object2 = new TestOneTableObject($object1->getId());
    $this->assertEqual($object2->export(), $object1->export());
    $this->assertFalse($object2->isNew());
  }

  function testPassingNonExistingIntToConstructorTrowsException()
  {
    try
    {
      $loaded = new TestOneTableObject(-10000);
      $this->assertTrue(false);
    }
    catch(lmbARException $e){}
  }

  function testFindFirst()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();
    $this->assertFalse($object2->isNew());

    $found = lmbActiveRecord :: findFirst('TestOneTableObject', array('criteria' => lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object1->getId()));
    $this->assertEqual($found->get('annotation'), $object1->get('annotation'));
    $this->assertEqual($found->get('content'), $object1->get('content'));
    $this->assertEqual($found->get('news_date'), $object1->get('news_date'));
    $this->assertEqual($found->get('id'), $object1->getId());

    //testing convenient alias
    $found = TestOneTableObject :: findFirst(array('criteria' => lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object1->getId()));
    $this->assertEqual($found->get('annotation'), $object1->get('annotation'));
    $this->assertEqual($found->get('content'), $object1->get('content'));
    $this->assertEqual($found->get('news_date'), $object1->get('news_date'));
    $this->assertEqual($found->get('id'), $object1->getId());
  }

  function testFindFirstConvertStringToCriteria()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();
    $this->assertFalse($object2->isNew());

    $found = lmbActiveRecord :: findFirst('TestOneTableObject', lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object1->getId());
    $this->assertEqual($found->get('annotation'), $object1->get('annotation'));
    $this->assertEqual($found->get('content'), $object1->get('content'));
    $this->assertEqual($found->get('news_date'), $object1->get('news_date'));
    $this->assertEqual($found->get('id'), $object1->getId());

    //testing convenient alias
    $found = TestOneTableObject :: findFirst(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object1->getId());
    $this->assertEqual($found->get('annotation'), $object1->get('annotation'));
    $this->assertEqual($found->get('content'), $object1->get('content'));
    $this->assertEqual($found->get('news_date'), $object1->get('news_date'));
    $this->assertEqual($found->get('id'), $object1->getId());
  }

  function testFindFirstConvertObjectToCriteria()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();
    $this->assertFalse($object2->isNew());

    $found = lmbActiveRecord :: findFirst('TestOneTableObject', new lmbSQLRawCriteria(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object1->getId()));
    $this->assertEqual($found->get('annotation'), $object1->get('annotation'));
    $this->assertEqual($found->get('content'), $object1->get('content'));
    $this->assertEqual($found->get('news_date'), $object1->get('news_date'));
    $this->assertEqual($found->get('id'), $object1->getId());

    //testing convenient alias
    $found = TestOneTableObject :: findFirst(new lmbSQLRawCriteria(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object1->getId()));
    $this->assertEqual($found->get('annotation'), $object1->get('annotation'));
    $this->assertEqual($found->get('content'), $object1->get('content'));
    $this->assertEqual($found->get('news_date'), $object1->get('news_date'));
    $this->assertEqual($found->get('id'), $object1->getId());
  }

  function testFindFirstConvertArrayToCriteria()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();
    $this->assertFalse($object2->isNew());

    $found = lmbActiveRecord :: findFirst('TestOneTableObject', array(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=?', $object1->getId()));
    $this->assertEqual($found->get('annotation'), $object1->get('annotation'));
    $this->assertEqual($found->get('content'), $object1->get('content'));
    $this->assertEqual($found->get('news_date'), $object1->get('news_date'));
    $this->assertEqual($found->get('id'), $object1->getId());

    //testing convenient alias
    $found = TestOneTableObject :: findFirst(array(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=?', $object1->getId()));
    $this->assertEqual($found->get('annotation'), $object1->get('annotation'));
    $this->assertEqual($found->get('content'), $object1->get('content'));
    $this->assertEqual($found->get('news_date'), $object1->get('news_date'));
    $this->assertEqual($found->get('id'), $object1->getId());
  }

  function testFindFirstWithSortParams()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $found = lmbActiveRecord :: findFirst('TestOneTableObject', array('sort' => array('id' => 'DESC')));
    $this->assertEqual($found->get('id'), $object2->getId());

    //testing convenient alias
    $found = TestOneTableObject :: findFirst(array('sort' => array('id' => 'DESC')));
    $this->assertEqual($found->get('id'), $object2->getId());
  }

  function testFindFirstWithDefaultSortParams()
  {
    $object1 = new TestOneTableObjectWithSortParams();
    $object1->setContent('Content'.mt_rand());
    $object1->save();

    $object2 = new TestOneTableObjectWithSortParams();
    $object2->setContent('Content'.mt_rand());
    $object2->save();

    $found = lmbActiveRecord :: findFirst('TestOneTableObjectWithSortParams');
    $this->assertEqual($found->get('id'), $object2->getId());

    //testing convenient alias
    $found = TestOneTableObjectWithSortParams :: findFirst();
    $this->assertEqual($found->get('id'), $object2->getId());
  }

  function testFindOneAlias()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();
    $this->assertFalse($object2->isNew());

    $found = lmbActiveRecord :: findOne('TestOneTableObject', lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object1->getId());
    $this->assertEqual($found->get('annotation'), $object1->get('annotation'));
    $this->assertEqual($found->get('content'), $object1->get('content'));
    $this->assertEqual($found->get('news_date'), $object1->get('news_date'));
    $this->assertEqual($found->get('id'), $object1->getId());

    //testing convenient alias
    $found = TestOneTableObject :: findOne(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object1->getId());
    $this->assertEqual($found->get('annotation'), $object1->get('annotation'));
    $this->assertEqual($found->get('content'), $object1->get('content'));
    $this->assertEqual($found->get('news_date'), $object1->get('news_date'));
    $this->assertEqual($found->get('id'), $object1->getId());
  }

  function testFindAllRecordsNoCriteria()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $object = new TestOneTableObject();
    $rs = $object->findAllRecords();
    $rs->rewind();
    $this->assertEqual($object1->getId(), $rs->current()->get('id'));
    $rs->next();
    $this->assertEqual($object2->getId(), $rs->current()->get('id'));
  }

  function testFildAllRecordsWithCriteria()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $object = new TestOneTableObject();
    $rs = $object->findAllRecords(new lmbSQLFieldCriteria('id', $object2->getId()));
    $rs->rewind();
    $this->assertEqual($object2->getId(), $rs->current()->get('id'));
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testFindAllNoCriteria()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $rs = lmbActiveRecord :: find('TestOneTableObject');
    $rs->rewind();
    $this->assertEqual($object1->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertEqual($object2->getId(), $rs->current()->getId());

    //testing convenient alias
    $rs = TestOneTableObject :: find();
    $rs->rewind();
    $this->assertEqual($object1->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertEqual($object2->getId(), $rs->current()->getId());
  }

  function testFindAllWithCriteria()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $rs = lmbActiveRecord :: find('TestOneTableObject', array('criteria' => new lmbSQLFieldCriteria('id', $object2->getId())));
    $rs->rewind();
    $this->assertEqual($object2->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertFalse($rs->valid());

    //testing convenient alias
    $rs = TestOneTableObject :: find(array('criteria' => new lmbSQLFieldCriteria('id', $object2->getId())));
    $rs->rewind();
    $this->assertEqual($object2->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testFindConvertObjectToCriteria()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $rs = lmbActiveRecord :: find('TestOneTableObject', new lmbSQLFieldCriteria('id', $object2->getId()));
    $rs->rewind();
    $this->assertEqual($object2->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertFalse($rs->valid());

    //testing convenient alias
    $rs = TestOneTableObject :: find(new lmbSQLFieldCriteria('id', $object2->getId()));
    $rs->rewind();
    $this->assertEqual($object2->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testFindConvertStringToCriteria()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $rs = lmbActiveRecord :: find('TestOneTableObject', lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object2->getId());
    $rs->rewind();
    $this->assertEqual($object2->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertFalse($rs->valid());

    //testing convenient alias
    $rs = TestOneTableObject :: find(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object2->getId());
    $rs->rewind();
    $this->assertEqual($object2->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testFindConvertArrayToCriteria()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $rs = lmbActiveRecord :: find('TestOneTableObject', array(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=?', $object2->getId()));
    $rs->rewind();
    $this->assertEqual($object2->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertFalse($rs->valid());

    //testing convenient alias
    $rs = TestOneTableObject :: find(array(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=?', $object2->getId()));
    $rs->rewind();
    $this->assertEqual($object2->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testFindWithIntegerCallsFindById()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $object = lmbActiveRecord :: find('TestOneTableObject', $object2->getId());
    $this->assertEqual($object2->getId(), $object->getId());

    //testing convenient alias
    $object = TestOneTableObject :: find($object2->getId());
    $this->assertEqual($object2->getId(), $object->getId());
  }

  function testFindWithIntegerDoesNotThrowException()
  {
    $this->assertNull(lmbActiveRecord :: find('TestOneTableObject', -10000));

    //testing convenient alias
    $this->assertNull(TestOneTableObject :: find(-10000));
  }

  function testFindAllWithSortParams()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $rs = lmbActiveRecord :: find('TestOneTableObject', array('sort' => array('id' => 'DESC')));
    $arr = $rs->getArray();
    $this->assertEqual($arr[0]->get('id'), $object2->getId());
    $this->assertEqual($arr[1]->get('id'), $object1->getId());

    //testing convenient alias
    $rs = TestOneTableObject :: find(array('sort' => array('id' => 'DESC')));
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

    //testing convenient alias
    $rs = TestOneTableObject :: find(array('sort' => array('id' => 'DESC')));
    $arr = $rs->getArray();
    $this->assertEqual($arr[0]->get('id'), $object2->getId());
    $this->assertEqual($arr[1]->get('id'), $object1->getId());
  }

  function testFindWithRelatedObjects_UsingWithParam()
  {
    $course1 = $this->creator->createCourse();
    $course2 = $this->creator->createCourse();
    $alt_course1 = $this->creator->createCourse();
    $alt_course2 = $this->creator->createCourse();
    $lecture1 = $this->creator->createLecture($course1, $alt_course1);
    $lecture2 = $this->creator->createLecture($course2, $alt_course2);
    $lecture3 = $this->creator->createLecture($course1, $alt_course2);

    $rs = lmbActiveRecord :: find('LectureForTest', array('join' => 'course, alt_course'));
    $arr = $rs->getArray();

    //make sure we really eager fetching
    $this->db->delete('course_for_test');

    $this->assertEqual($arr[0]->getId(), $lecture1->getId());
    $this->assertEqual($arr[0]->getCourse()->getTitle(), $course1->getTitle());
    $this->assertEqual($arr[0]->getAltCourse()->getTitle(), $alt_course1->getTitle());

    $this->assertEqual($arr[1]->getId(), $lecture2->getId());
    $this->assertEqual($arr[1]->getCourse()->getTitle(), $course2->getTitle());
    $this->assertEqual($arr[1]->getAltCourse()->getTitle(), $alt_course2->getTitle());

    $this->assertEqual($arr[2]->getId(), $lecture3->getId());
    $this->assertEqual($arr[2]->getCourse()->getTitle(), $course1->getTitle());
    $this->assertEqual($arr[2]->getAltCourse()->getTitle(), $alt_course2->getTitle());
  }

  function testFindAttachRelatedObjects_HasMany()
  {
    $course1 = $this->creator->createCourse();
    $course2 = $this->creator->createCourse();

    $lecture1 = $this->creator->createLecture($course1, null, 'ZZZ');
    $lecture2 = $this->creator->createLecture($course2, null, 'CCC');
    $lecture3 = $this->creator->createLecture($course1, null, 'AAA');
    $lecture4 = $this->creator->createLecture($course1, null, 'BBB');

    $rs = lmbActiveRecord :: find('CourseForTest', array('attach' => array('lectures' => array('sort' => array('title' => 'ASC')))));
    $arr = $rs->getArray();

    //make sure we really eager fetching
    $this->db->delete('lecture_for_test');

    $this->assertIsA($arr[0], 'CourseForTest');
    $this->assertEqual($arr[0]->getTitle(), $course1->getTitle());
    $lectures = $arr[0]->getLectures();
    $this->assertEqual(count($lectures), 3);
    $this->assertEqual($lectures[0]->getId(), $lecture3->getId());
    $this->assertEqual($lectures[0]->getTitle(), 'AAA');
    $this->assertEqual($lectures[1]->getId(), $lecture4->getId());
    $this->assertEqual($lectures[1]->getTitle(), 'BBB');
    $this->assertEqual($lectures[2]->getId(), $lecture1->getId());
    $this->assertEqual($lectures[2]->getTitle(), 'ZZZ');

    $this->assertIsA($arr[1], 'CourseForTest');
    $this->assertEqual($arr[1]->getTitle(), $course2->getTitle());
    $lectures = $arr[1]->getLectures();
    $this->assertEqual(count($lectures), 1);
    $this->assertEqual($lectures[0]->getId(), $lecture2->getId());
    $this->assertEqual($lectures[0]->getTitle(), 'CCC');
  }

  function testFindBySql()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $rs = lmbActiveRecord :: findBySql('TestOneTableObject', 'select * from ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("test_one_table_object") . ' order by ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . ' desc');
    $rs->rewind();
    $this->assertEqual($object2->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertEqual($object1->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertFalse($rs->valid());

    $this->assertEqual($rs->getIds(), array($object2->getId(), $object1->getId()));

    //testing convenient alias
    $rs = TestOneTableObject :: findBySql('select * from ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("test_one_table_object") . ' order by ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . ' desc');
    $rs->rewind();
    $this->assertEqual($object2->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertEqual($object1->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testFindFirstBySql()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $object = lmbActiveRecord :: findFirstBySql('TestOneTableObject', 'select * from ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("test_one_table_object") . ' order by ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . ' desc');
    $this->assertEqual($object2->getId(), $object->getId());

    //testing convenient alias
    $object = TestOneTableObject :: findFirstBySql('select * from ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("test_one_table_object") . ' order by ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . ' desc');
    $this->assertEqual($object2->getId(), $object->getId());
  }

  function testFindOneBySqlAlias()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $object = lmbActiveRecord :: findOneBySql('TestOneTableObject', 'select * from ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("test_one_table_object") . ' order by ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . ' desc');
    $this->assertEqual($object2->getId(), $object->getId());

    //testing convenient alias
    $object = TestOneTableObject :: findOneBySql('select * from ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("test_one_table_object") . ' order by ' . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . ' desc');
    $this->assertEqual($object2->getId(), $object->getId());
  }

  function testFindByIds()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();
    $object3 = $this->creator->createOneTableObject();

    $rs = lmbActiveRecord :: findByIds('TestOneTableObject',
                                       array($object1->getId(), $object3->getId()),
                                       array('sort' => array('id' => 'asc')));
    $rs->rewind();
    $this->assertEqual($object1->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertEqual($object3->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertFalse($rs->valid());

    //testing convenient alias
    $rs = TestOneTableObject :: findByIds(array($object1->getId(), $object3->getId()), array('sort' => array('id' => 'asc')));
    $rs->rewind();
    $this->assertEqual($object1->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertEqual($object3->getId(), $rs->current()->getId());
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testFindByIdsReturnEmptyIteratorIfNoIds()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $rs = lmbActiveRecord :: findByIds('TestOneTableObject', array());
    $rs->rewind();
    $this->assertFalse($rs->valid());

    //testing convenient alias
    $rs = TestOneTableObject :: findByIds(array());
    $rs->rewind();
    $this->assertFalse($rs->valid());
  }

  function testGetDatasetActsAsStaticFind()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $ds = $object2->getDataset();
    $this->assertEqual($ds->at(0)->getId(), $object1->getId());
    $this->assertEqual($ds->at(1)->getId(), $object2->getId());
  }

  function testDelete()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    lmbActiveRecord :: delete('TestOneTableObject');
    $this->assertEqual($this->db->count('test_one_table_object'), 0);
  }

  function testDeleteShort()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    TestOneTableObject :: delete();
    $this->assertEqual($this->db->count('test_one_table_object'), 0);
  }

  function testDeleteCallsDestroy()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    ob_start();
    lmbActiveRecord :: delete('TestOneTableObjectWithCustomDestroy');
    $contents = ob_get_contents();
    ob_end_clean();

    $this->assertEqual($contents, 'destroyed!destroyed!');
    $this->assertEqual($this->db->count('test_one_table_object'), 0);
  }

  function testDeleteShortCallsDestroy()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    ob_start();
    TestOneTableObjectWithCustomDestroy :: delete();
    $contents = ob_get_contents();
    ob_end_clean();

    $this->assertEqual($contents, 'destroyed!destroyed!');
    $this->assertEqual($this->db->count('test_one_table_object'), 0);
  }

  function testDeleteByCriteria()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $criteria = new lmbSQLFieldCriteria('id', $object2->getId());
    lmbActiveRecord :: delete('TestOneTableObject', $criteria);

    $this->assertEqual($this->db->count('test_one_table_object'), 1);

    $found = lmbActiveRecord :: findById('TestOneTableObject', $object1->getId());
    $this->assertEqual($found->getContent(), $object1->getContent());
  }

  function testDeleteShortByCriteria()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $criteria = new lmbSQLFieldCriteria('id', $object2->getId());
    TestOneTableObject :: delete($criteria);

    $this->assertEqual($this->db->count('test_one_table_object'), 1);

    $found = TestOneTableObject :: findById($object1->getId());
    $this->assertEqual($found->getContent(), $object1->getContent());
  }

  function testDeleteRaw()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    lmbActiveRecord :: deleteRaw('TestOneTableObject');

    $this->assertEqual($this->db->count('test_one_table_object'), 0);
  }

  function testDeleteShortRaw()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    TestOneTableObject :: deleteRaw();

    $this->assertEqual($this->db->count('test_one_table_object'), 0);
  }

  function testDeleteRawDoesntCallDestroy()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    ob_start();
    lmbActiveRecord :: deleteRaw('TestOneTableObjectWithCustomDestroy');
    $contents = ob_get_contents();
    ob_end_clean();

    $this->assertEqual($contents, '');
    $this->assertEqual($this->db->count('test_one_table_object'), 0);
  }

  function testDeleteShortRawDoesntCallDestroy()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    ob_start();
    TestOneTableObjectWithCustomDestroy :: deleteRaw();
    $contents = ob_get_contents();
    ob_end_clean();

    $this->assertEqual($contents, '');
    $this->assertEqual($this->db->count('test_one_table_object'), 0);
  }

  function testDeleteRawByCriteria()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $criteria = new lmbSQLFieldCriteria('id', $object2->getId());
    lmbActiveRecord :: deleteRaw('TestOneTableObject', $criteria);

    $this->assertEqual($this->db->count('test_one_table_object'), 1);

    $found = lmbActiveRecord :: findById('TestOneTableObject', $object1->getId());
    $this->assertEqual($found->getContent(), $object1->getContent());
  }

  function testDeleteShortRawByCriteria()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    $criteria = new lmbSQLFieldCriteria('id', $object2->getId());
    TestOneTableObject :: deleteRaw($criteria);

    $this->assertEqual($this->db->count('test_one_table_object'), 1);

    $found = TestOneTableObject :: findById($object1->getId());
    $this->assertEqual($found->getContent(), $object1->getContent());
  }

  function testUpdateRawAllWithArraySet()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    lmbActiveRecord :: updateRaw('TestOneTableObject', array('content' => 'blah'));

    $rs = lmbActiveRecord :: find('TestOneTableObject');
    $rs->rewind();
    $this->assertEqual($rs->current()->getContent(), 'blah');
    $rs->next();
    $this->assertEqual($rs->current()->getContent(), 'blah');
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testUpdateShortRawAllWithArraySet()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    TestOneTableObject :: updateRaw(array('content' => 'blah'));

    $rs = TestOneTableObject :: find();
    $rs->rewind();
    $this->assertEqual($rs->current()->getContent(), 'blah');
    $rs->next();
    $this->assertEqual($rs->current()->getContent(), 'blah');
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testUpdateRawAllWithRawValues()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    lmbActiveRecord :: updateRaw('TestOneTableObject', lmbActiveRecord::getDefaultConnection()->quoteIdentifier("ordr") . '=1');

    $rs = lmbActiveRecord :: find('TestOneTableObject');
    $rs->rewind();
    $this->assertEqual($rs->current()->getOrdr(), 1);
    $rs->next();
    $this->assertEqual($rs->current()->getOrdr(), 1);
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testUpdateShortRawAllWithRawValues()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    TestOneTableObject :: updateRaw(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("ordr") . '=1');

    $rs = TestOneTableObject :: find();
    $rs->rewind();
    $this->assertEqual($rs->current()->getOrdr(), 1);
    $rs->next();
    $this->assertEqual($rs->current()->getOrdr(), 1);
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testUpdateRawWithCriteria()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    lmbActiveRecord :: updateRaw('TestOneTableObject', array('content' => 'blah'), lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object2->getId());

    $rs = lmbActiveRecord :: find('TestOneTableObject');
    $rs->rewind();
    $this->assertEqual($rs->current()->getContent(), $object1->getContent());
    $rs->next();
    $this->assertEqual($rs->current()->getContent(), 'blah');
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testUpdateShortRawWithCriteria()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();

    TestOneTableObject :: updateRaw(array('content' => 'blah'), lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '=' . $object2->getId());

    $rs = TestOneTableObject :: find();
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
}

