<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActiveRecordOneToManyRelationsTest.class.php 4984 2007-02-08 15:35:02Z pachanga $
 * @package    active_record
 */
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/active_record/src/lmbAROneToManyCollection.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');

class CourseForTest extends lmbActiveRecord
{
  protected $_db_table_name = 'course_for_test';
  protected $_has_many = array('lectures' => array('field' => 'course_id',
                                                   'class' => 'LectureForTest'),
                               'alt_lectures' => array('field' => 'alt_course_id',
                                                       'class' => 'LectureForTest'));
}

class LectureForTest extends lmbActiveRecord
{
  protected $_db_table_name = 'lecture_for_test';
  protected $_many_belongs_to = array('course' => array('field' => 'course_id',
                                                        'class' => 'CourseForTest'),
                                      'alt_course' => array('field' => 'alt_course_id',
                                                            'class' => 'CourseForTest',
                                                            'can_be_null' => true));
}

class LecturesForTestCollectionStub extends lmbAROneToManyCollection{}

class CourseForTestWithCustomCollection extends lmbActiveRecord
{
  protected $_db_table_name = 'course_for_test';
  protected $_has_many = array('lectures' => array('field' => 'course_id',
                                                   'class' => 'LectureForTest',
                                                   'collection' => 'LecturesForTestCollectionStub'));
}

Mock :: generate('LectureForTest', 'MockLectureForTest');

class lmbActiveRecordOneToManyRelationsTest extends UnitTestCase
{
  protected $db;

  function setUp()
  {
    $this->db = new lmbSimpleDb(lmbToolkit :: instance()->getDefaultDbConnection());
    $this->_dbCleanUp();
  }

  function tearDown()
  {
    $this->_dbCleanUp();
  }

  function _dbCleanUp()
  {
    $this->db->delete('course_for_test');
    $this->db->delete('lecture_for_test');
  }

  function testMapPropertyToField()
  {
    $course = new CourseForTest();
    $this->assertEqual('lectures', $course->mapFieldToProperty('course_id'));
    $this->assertNull($course->mapFieldToProperty('blah'));

    $lecture = new LectureForTest();
    $this->assertEqual('course', $lecture->mapFieldToProperty('course_id'));
    $this->assertNull($lecture->mapFieldToProperty('blah'));
  }

  function testNewObjectReturnsEmptyCollection()
  {
    $course = new CourseForTest();
    $lectures = $course->getLectures();
    $lectures->rewind();
    $this->assertFalse($lectures->valid());
  }

  function testNewObjectReturnsNullParent()
  {
    $lecture = new LectureForTest();
    $this->assertNull($lecture->getCourse());
  }

  function testAddToCollection()
  {
    $course = $this->_initCourse();

    $l1 = new LectureForTest();
    $l1->setTitle('Physics');
    $l2 = new LectureForTest();
    $l2->setTitle('Math');

    $course->addToLectures($l1);
    $course->addToLectures($l2);

    $rs = $course->getLectures();

    $rs->rewind();
    $this->assertEqual($rs->current()->getTitle(), $l1->getTitle());
    $rs->next();
    $this->assertEqual($rs->current()->getTitle(), $l2->getTitle());
  }

  function testSetingCollectionDirectlyCallsAddToMethod()
  {
    $course = $this->_initCourse();

    $l1 = new LectureForTest();
    $l1->setTitle('Physics');
    $l2 = new LectureForTest();
    $l2->setTitle('Math');

    $course->setLectures(array($l1, $l2));
    $lectures = $course->getLectures();
    $this->assertEqual($lectures->at(0), $l1);
    $this->assertEqual($lectures->at(1), $l2);
  }

  function testSetFlushesPreviousCollection()
  {
    $course = $this->_initCourse();

    $l1 = new LectureForTest();
    $l1->setTitle('Physics');
    $l2 = new LectureForTest();
    $l2->setTitle('Math');

    $course->addToLectures($l1);
    $course->addToLectures($l2);

    $course->setLectures(array($l1));
    $lectures = $course->getLectures()->getArray();
    $this->assertEqual($lectures[0]->getTitle(), $l1->getTitle());
    $this->assertEqual(sizeof($lectures), 1);
  }

  function testSaveCollection()
  {
    $course = $this->_initCourse();

    $l1 = new LectureForTest();
    $l1->setTitle('Physics');
    $l2 = new LectureForTest();
    $l2->setTitle('Math');

    $course->addToLectures($l1);
    $course->addToLectures($l2);

    $course->save();

    $course2 = lmbActiveRecord :: findById('CourseForTest', $course->getId());
    $rs = $course2->getLectures();

    $rs->rewind();
    $this->assertEqual($rs->current()->getTitle(), $l1->getTitle());
    $rs->next();
    $this->assertEqual($rs->current()->getTitle(), $l2->getTitle());
  }

  function testGenericGetLoadsCollection()
  {
    $course = $this->_initCourse();

    $l1 = new LectureForTest();
    $l1->setTitle('Physics');
    $l2 = new LectureForTest();
    $l2->setTitle('Math');

    $course->addToLectures($l1);
    $course->addToLectures($l2);

    $course->save();

    $course2 = lmbActiveRecord :: findById('CourseForTest', $course->getId());
    $rs = $course2->get('lectures');

    $rs->rewind();
    $this->assertEqual($rs->current()->getTitle(), $l1->getTitle());
    $rs->next();
    $this->assertEqual($rs->current()->getTitle(), $l2->getTitle());
  }

  function testParentObjectCanBeNull()
  {
    $course = $this->_initCourse();

    $lecture = new LectureForTest();
    $lecture->setTitle('Physics');
    $lecture->setCourse($course);
    $lecture->save();

    $lecture2 = lmbActiveRecord :: findById('LectureForTest', $lecture->getId());
    $this->assertEqual($lecture2->getCourse()->getTitle(), $course->getTitle());
    $this->assertNull($lecture2->getAltCourse());

    $lecture2->setAltCourse($course);
    $lecture2->save();

    $lecture3 = lmbActiveRecord :: findById('LectureForTest', $lecture2->getId());
    $this->assertEqual($lecture3->getCourse()->getTitle(), $course->getTitle());
    $this->assertEqual($lecture3->getAltCourse()->getTitle(), $course->getTitle());
  }

  function testSettingNullParentObject()
  {
    $course = $this->_initCourse();

    $lecture = new LectureForTest();
    $lecture->setTitle('Physics');
    $lecture->setAltCourse($course);
    $lecture->save();

    $this->assertEqual($course->getAltLectures()->count(), 1);

    $lecture2 = lmbActiveRecord :: findById('LectureForTest', $lecture->getId());

    $lecture2->setAltCourse(null);
    $lecture2->save();

    $this->assertEqual($course->getAltLectures()->count(), 0);

    $lecture3 = lmbActiveRecord :: findById('LectureForTest', $lecture2->getId());
    $this->assertNull($lecture3->getAltCourse());
  }

  function testDeleteCollection()
  {
    $course = $this->_initCourse();

    $l1 = new LectureForTest();
    $l1->setTitle('Physics');
    $l2 = new LectureForTest();
    $l2->setTitle('Math');

    $course->addToLectures($l1);
    $course->addToLectures($l2);

    $course->save();

    $course2 = lmbActiveRecord :: findById('CourseForTest', $course->getId());
    $course2->destroy();

    $this->assertNull(lmbActiveRecord :: findFirst('LectureForTest', array('criteria' => 'id = ' . $l1->getId())));
    $this->assertNull(lmbActiveRecord :: findFirst('LectureForTest', array('criteria' => 'id = ' . $l2->getId())));
  }

  function testUseCustomCollection()
  {
    $course = new CourseForTestWithCustomCollection();
    $this->assertTrue($course->getLectures() instanceof LecturesForTestCollectionStub);
  }

  function testSetFlushesPreviousCollectionInDatabaseToo()
  {
    $course = $this->_initCourse();

    $l1 = new LectureForTest();
    $l1->setTitle('Physics');
    $l2 = new LectureForTest();
    $l2->setTitle('Math');

    $course->addToLectures($l1);
    $course->addToLectures($l2);

    $course->save();

    $course2 = lmbActiveRecord :: findById('CourseForTest', $course->getId());

    $l3 = new LectureForTest();
    $l3->setTitle('Math');

    $course2->setLectures(array($l3));
    $course2->save();

    $course3 = lmbActiveRecord :: findById('CourseForTest', $course->getId());

    $lectures = $course3->getLectures();
    $this->assertEqual($lectures->count(), 1);
    $this->assertEqual($lectures->at(0)->getTitle(), $l3->getTitle());
  }

  function _initCourse()
  {
    $course = new CourseForTest();
    $course->setTitle('Course'. mt_rand());
    return $course;
  }
}

?>
