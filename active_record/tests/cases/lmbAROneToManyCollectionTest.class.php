<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/active_record/src/lmbAROneToManyCollection.class.php');
require_once('limb/core/src/lmbCollectionDecorator.class.php');
require_once(dirname(__FILE__) . '/lmbAROneToManyRelationsTest.class.php');

Mock :: generate('LectureForTest', 'MockLectureForTest');

class lmbARTestingDSDecorator extends lmbCollectionDecorator
{
  protected $value;

  function setValue($value)
  {
    $this->value = $value;
  }

  protected function _processRecord($record)
  {
    $record->set('value', $this->value);
  }

  function current()
  {
    $record = parent :: current();
    $this->_processRecord($record);
    return $record;
  }

  function at($pos)
  {
    $record = parent :: at($pos);
    $this->_processRecord($record);
    return $record;
  }
}

class LectureForTestStub extends LectureForTest
{
  var $save_calls = 0;

  function save($error_list = null)
  {
    parent :: save($error_list);
    $this->save_calls++;
  }
}


class SpecialCourseForTest extends CourseForTest
{
  protected $_has_many = array('lectures' => array('field' => 'course_id',
                                                   'class' => 'LectureForTest',
                                                   'sort_params' => array('id' => 'DESC')));
}

class VerySpecialCourseForTest extends CourseForTest
{
  protected $_has_many = array('lectures' => array('field' => 'course_id',
                                                   'class' => 'SpecialLectureForTest'));
}

class SpecialLectureForTest extends LectureForTest
{
  protected $_default_sort_params = array('id' => 'DESC');
}

class lmbAROneToManyCollectionTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('lecture_for_test', 'course_for_test'); 
  
  function testAddToWithExistingOwner()
  {
    $course = $this->_createCourseAndSave();

    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();

    $collection = new lmbAROneToManyCollection('lectures', $course);
    $collection->add($l1);
    $collection->add($l2);

    $arr = $collection->getArray();

    $this->assertEqual($arr[0]->getTitle(), $l1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $l2->getTitle());
    $this->assertEqual(sizeof($arr), 2);

    $collection2 = new lmbAROneToManyCollection('lectures', $course);
    $arr = $collection2->getArray();

    $this->assertEqual($arr[0]->getTitle(), $l1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $l2->getTitle());
    $this->assertEqual(sizeof($arr), 2);
  }

  function testResetCollectionOnAddForExistingOwner()
  {
    $course = $this->_createCourseAndSave();

    $lectures = $course->getLectures();
    $lectures->rewind();
    $this->assertFalse($lectures->valid());

    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();

    $course->addToLectures($l1);
    $course->addToLectures($l2);

    $lectures = $course->getLectures();
    $lectures->rewind();
    $lecture = $lectures->current();
    $this->assertEqual($lecture->getTitle(), $l1->getTitle());
  }

  function testAddToWithNonSavedOwner()
  {
    $course = $this->_createCourse();

    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();

    $collection = new lmbAROneToManyCollection('lectures', $course);
    $collection->add($l1);
    $collection->add($l2);

    $arr = $collection->getArray();
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $l1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $l2->getTitle());

    $collection2 = new lmbAROneToManyCollection('lectures', $course);
    $arr = $collection2->getArray();

    $this->assertEqual(sizeof($arr), 0);
  }

  function testSaveWithExistingOwnerDoesNothing()
  {
    $l1 = new MockLectureForTest();
    $l2 = new MockLectureForTest();

    $course = $this->_createCourseAndSave();

    $collection = new lmbAROneToManyCollection('lectures', $course);
    $collection->add($l1);
    $collection->add($l2);

    $l1->expectNever('save');
    $l2->expectNever('save');

    $collection->save();
  }

  function testSaveWithNonSavedOwner()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();

    $course = $this->_createCourse();

    $collection = new lmbAROneToManyCollection('lectures', $course);
    $collection->add($l1);
    $collection->add($l2);

    $collection2 = new lmbAROneToManyCollection('lectures', $course);
    $this->assertEqual(sizeof($collection2->getArray()), 0);

    $course->save();
    $collection->save();

    $collection3 = new lmbAROneToManyCollection('lectures', $course);
    $arr = $collection3->getArray();
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $l1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $l2->getTitle());
  }

  function testSavingOwnerDoesntAffectCollection()
  {
    $l1 = new LectureForTestStub();
    $l1->setTitle('Physics');
    $l2 = new LectureForTestStub();
    $l2->setTitle('Math');

    $course = $this->_createCourse();

    $collection = new lmbAROneToManyCollection('lectures', $course);
    $collection->add($l1);

    $course->save();

    $collection->add($l2);

    //items in memory
    $arr = $collection->getArray();
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $l1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $l2->getTitle());
    $this->assertEqual($l1->save_calls, 0);
    $this->assertEqual($l2->save_calls, 0);

    //...and not db yet
    $collection2 = new lmbAROneToManyCollection('lectures', $course);
    $this->assertEqual(sizeof($collection2->getArray()), 0);

    $collection->save();

    $collection3 = new lmbAROneToManyCollection('lectures', $course);
    $arr = $collection3->getArray();
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $l1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $l2->getTitle());

    //check items not saved twice
    $collection->save();

    $this->assertEqual($l1->save_calls, 1);
    $this->assertEqual($l2->save_calls, 1);

    $collection4 = new lmbAROneToManyCollection('lectures', $course);
    $arr = $collection4->getArray();
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $l1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $l2->getTitle());
  }

  function testLoadOnlyProperRecordsWithExistingOwner()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();

    $course1 = $this->_createCourseAndSave(array($l1, $l2));

    $l3 = $this->_createLecture();
    $l4 = $this->_createLecture();

    $course2 = $this->_createCourseAndSave(array($l3, $l4));

    $collection1 = new lmbAROneToManyCollection('lectures', $course1);
    $this->assertEqual($collection1->count(), 2);
    $arr = $collection1->getArray();
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $l1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $l2->getTitle());

    $collection2 = new lmbAROneToManyCollection('lectures', $course2);
    $this->assertEqual($collection2->count(), 2);
    $arr = $collection2->getArray();
    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $l3->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $l4->getTitle());
  }

  function testCountWithExistingOwner()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();

    $course = $this->_createCourseAndSave();

    $collection = new lmbAROneToManyCollection('lectures', $course);
    $collection->add($l1);
    $collection->add($l2);

    $this->assertEqual($collection->count(), 2);
  }

  function testCountWithNonSavedOwner()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();

    $course = $this->_initCourse();

    $collection = new lmbAROneToManyCollection('lectures', $course);
    $this->assertEqual($collection->count(), 0);

    $collection->add($l1);
    $collection->add($l2);

    $this->assertEqual($collection->count(), 2);
  }

  function testImplementsCountable()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();

    $course = $this->_initCourse();
    $collection = new lmbAROneToManyCollection('lectures', $course);

    $this->assertEqual(sizeof($collection), 0);

    $collection->add($l1);
    $collection->add($l2);

    $this->assertEqual(sizeof($collection), 2);
  }

  function testPartiallyImplementsArrayAccess()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();

    $course = $this->_initCourse();
    $collection = new lmbAROneToManyCollection('lectures', $course);

    $collection[] = $l1;
    $collection[] = $l2;

    $this->assertEqual($collection[0]->getId(), $l1->getId());
    $this->assertEqual($collection[1]->getId(), $l2->getId());
    $this->assertNull($collection[2]);

    $this->assertTrue(isset($collection[0]));
    $this->assertTrue(isset($collection[1]));
    $this->assertFalse(isset($collection[2]));

    //we can't really implement just every php array use case
    $this->assertNull($collection['foo']);
    $this->assertFalse(isset($collection['foo']));
    $collection[3] = 'foo';
    $this->assertNull($collection[3]);
  }

  function testRemoveAllWithExistingOwner()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();

    $course = $this->_createCourseAndSave(array($l1, $l2));

    $collection = new lmbAROneToManyCollection('lectures', $course);
    $collection->removeAll();

    $course2 = lmbActiveRecord :: findById('CourseForTest', $course->getId());

    $collection = new lmbAROneToManyCollection('lectures', $course2);
    $this->assertEqual(sizeof($collection->getArray()), 0);
  }

  function testRemoveAllWithNonSavedOwner()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();

    $course = $this->_createCourse();

    $collection = new lmbAROneToManyCollection('lectures', $course);
    $collection->add($l1);
    $collection->add($l2);
    $collection->removeAll();

    $this->assertEqual($collection->count(), 0);
  }

  function testPaginateWithNonSavedOwner()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();
    $l3 = $this->_createLecture();

    $course = $this->_createCourse();

    $collection = new lmbAROneToManyCollection('lectures', $course);
    $collection->add($l1);
    $collection->add($l2);
    $collection->add($l3);

    $collection->paginate($offset = 0, $limit = 2);

    $this->assertEqual($collection->count(), 3);
    $arr = $collection->getArray();

    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $l1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $l2->getTitle());
  }

  function testPaginateWithExistingOwner()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();
    $l3 = $this->_createLecture();

    $course = $this->_createCourseAndSave(array($l1, $l2, $l3));

    $collection = new lmbAROneToManyCollection('lectures', $course);
    $collection->paginate($offset = 0, $limit = 2);

    $this->assertEqual($collection->count(), 3);
    $arr = $collection->getArray();

    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]->getTitle(), $l1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $l2->getTitle());
  }

  function testSortWithExistingOwner()
  {
    $l1 = $this->_createLecture('A-Lecture');
    $l2 = $this->_createLecture('B-Lecture');
    $l3 = $this->_createLecture('C-Lecture');

    $course = $this->_createCourseAndSave(array($l1, $l2, $l3));

    $collection = new lmbAROneToManyCollection('lectures', $course);
    $collection->sort(array('title' => 'DESC'));

    $this->assertEqual($collection->count(), 3);
    $arr = $collection->getArray();

    $this->assertEqual(sizeof($arr), 3);
    $this->assertEqual($arr[0]->getTitle(), $l3->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $l2->getTitle());
    $this->assertEqual($arr[2]->getTitle(), $l1->getTitle());
  }

  function testSortWithNonSavedOwner()
  {
    $l1 = $this->_createLecture('A-Lecture');
    $l2 = $this->_createLecture('B-Lecture');
    $l3 = $this->_createLecture('C-Lecture');

    $course = $this->_createCourse();

    $collection = new lmbAROneToManyCollection('lectures', $course);
    $collection->add($l1);
    $collection->add($l2);
    $collection->add($l3);

    $collection->sort(array('title' => 'DESC'));
    $this->assertEqual($collection->at(0)->getTitle(), 'C-Lecture');
    $this->assertEqual($collection->at(1)->getTitle(), 'B-Lecture');
    $this->assertEqual($collection->at(2)->getTitle(), 'A-Lecture');
  }

  function testFindFirstWithSortParamsForExistingOwner()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();
    $course = $this->_createCourseAndSave(array($l1, $l2));

    $lecture = $course->getLectures()->findFirst(array('sort' => array('id' => 'DESC')));
    $this->assertEqual($lecture->getTitle(), $l2->getTitle());
  }

  function testFindForExistingOwnerAppliesSortParamsFromRelationInfo()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();

    $course = new SpecialCourseForTest();// lectures relation info has sort_params
    $course->setTitle('Special cource');
    $course->addToLectures($l1);
    $course->addToLectures($l2);
    $course->save();

    $lectures = $course->getLectures()->find();
    $this->assertEqual($lectures->at(0)->getTitle(), $l2->getTitle());
    $this->assertEqual($lectures->at(1)->getTitle(), $l1->getTitle());
  }

  function testFindForExistingOwnerAppliesSortParamsFromChildObjectByDefault()
  {
    $l1 = new SpecialLectureForTest();
    $l1->setTitle('lecture1');
    $l2 = new SpecialLectureForTest();
    $l2->setTitle('lecture2');

    $course = new VerySpecialCourseForTest();// lectures relation info has sort_params
    $course->setTitle('Special cource');
    $course->addToLectures($l1);
    $course->addToLectures($l2);
    $course->save();

    $lectures = $course->getLectures()->find();
    $this->assertEqual($lectures->at(0)->getTitle(), $l2->getTitle());
    $this->assertEqual($lectures->at(1)->getTitle(), $l1->getTitle());
  }

  function testIterateAlsoAppliesSortParamsFromRelationInfo()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();

    $course = new SpecialCourseForTest();// lectures relation info has sort_params
    $course->setTitle('Special cource');
    $course->addToLectures($l1);
    $course->addToLectures($l2);
    $course->save();

    $lectures = $course->getLectures();
    $this->assertEqual($lectures->at(0)->getTitle(), $l2->getTitle());
    $this->assertEqual($lectures->at(1)->getTitle(), $l1->getTitle());
  }

  function testAtWithExistingOwner()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();
    $l3 = $this->_createLecture();

    $course = $this->_createCourseAndSave(array($l1, $l2, $l3));
    $collection = new lmbAROneToManyCollection('lectures', $course);

    $this->assertEqual($collection->at(0)->getTitle(), $l1->getTitle());
    $this->assertEqual($collection->at(2)->getTitle(), $l3->getTitle());
    $this->assertEqual($collection->at(1)->getTitle(), $l2->getTitle());
  }

  function testFindWithExistingOwner()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();
    $l3 = $this->_createLecture();

    $course = $this->_createCourseAndSave(array($l1, $l2, $l3));

    $lectures = $course->getLectures()->find(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . "=" . $l1->getId());
    $this->assertEqual($lectures->count(), 1);
    $this->assertEqual($lectures->at(0)->getTitle(), $l1->getTitle());
  }

  function testFindWithNonSavedOwner_TODO()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();
    $course = $this->_createCourse(array($l1, $l2));

    try
    {
      $lectures = $course->getLectures()->find(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . "=" . $l1->getId());
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testFindFirstWithExistingOwner()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();
    $l3 = $this->_createLecture();

    $course = $this->_createCourseAndSave(array($l1, $l2, $l3));

    $lecture = $course->getLectures()->findFirst(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . "=" . $l1->getId() . " OR " . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . "=" . $l2->getId());
    $this->assertEqual($lecture->getTitle(), $l1->getTitle());
  }

  function testFindFirstWithNonSavedOwner_TODO()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();
    $course = $this->_createCourse(array($l1, $l2));

    try
    {
      $lecture = $course->getLectures()->findFirst(lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . "=" . $l1->getId() . " OR " . lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . "=" . $l2->getId());
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testApplyDecoratorWithParams()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();
    $course = $this->_createCourseAndSave(array($l1, $l2));
    $lectures = $course->getLectures();
    $lectures->addDecorator('lmbARTestingDSDecorator', array('value' => 'my_value'));

    $this->assertEqual($lectures->at(0)->get('value'), 'my_value');
    $this->assertEqual($lectures->at(1)->get('value'), 'my_value');

    $lectures->rewind();
    $record = $lectures->current();
    $this->assertEqual($record->get('value'), 'my_value');
  }

  function testSet()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();
    $course = $this->_createCourseAndSave(array($l1, $l2));

    $collection = new lmbAROneToManyCollection('lectures', $course);
    $collection->set(array($l2));

    $this->assertEqual($collection->count(), 1);
    $this->assertEqual($collection->at(0)->getTitle(), $l2->getTitle());
  }

  function testSetExistringRelatedObjectIsSaved()
  {
    $l1 = $this->_createLecture();
    $l2 = $this->_createLecture();
    $course = $this->_createCourseAndSave(array($l1, $l2));

    $collection = new lmbAROneToManyCollection('lectures', $course);
    $l2->setTitle('new_title');
    $collection->set(array($l2));

    $this->assertEqual($collection->count(), 1);
    $this->assertEqual($collection->at(0)->getTitle(), 'new_title');
  }

  function testGetRelatedObjectWithAdditionCriteria()
  {
    $l1 = $this->_createLecture('foo1');
    $l2 = $this->_createLecture('foo2');
    $l3 = $this->_createLecture('bar1');
    $l4 = $this->_createLecture('bar2');

    $course = $this->_createCourseAndSave(array($l1, $l2,$l3,$l4));

    $this->assertEqual(count($course->getFooLectures()), 2);
  }

  protected function _initCourse($lectures = array())
  {
    $course = new CourseForTest();
    $course->setTitle('Course' . mt_rand());

    if(count($lectures))
    {
      foreach($lectures as $lecture)
        $course->addToLectures($lecture);
    }

    return $course;
  }

  protected function _createCourse($lectures = array())
  {
    $course = $this->_initCourse($lectures);
    return $course;
  }

  protected function _createCourseAndSave($lectures = array())
  {
    $course = $this->_createCourse($lectures);
    $course->save();
    return $course;
  }

  protected function _createLecture($title = '')
  {
    $title = $title ? $title : 'Lecture' . mt_rand();

    $l = new LectureForTest();
    $l->setTitle($title);
    return $l;
  }
}


