<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActiveRecordSubclassingTest.class.php 4984 2007-02-08 15:35:02Z pachanga $
 * @package    active_record
 */
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');

class TestOneTableTypedObject extends lmbActiveRecord
{
  protected $_db_table_name = 'test_one_table_typed_object';
}

class FooOneTableTestObject extends TestOneTableTypedObject{}
class BarOneTableTestObject extends TestOneTableTypedObject{}

class BaseOneTableTestObject extends TestOneTableTypedObject
{
  protected $_base_class = __CLASS__;
}

class TypedLectureForTest extends lmbActiveRecord
{
  protected $_db_table_name = 'lecture_for_typed_test';
  protected $_belongs_to = array('course' => array('field' => 'course_id',
                                                   'class' => 'CourseForTestForTypedLecture'));
}

class FooLectureForTest extends TypedLectureForTest{}
class BarLectureForTest extends TypedLectureForTest{}

class CourseForTestForTypedLecture extends lmbActiveRecord
{
  protected $_db_table_name = 'course_for_typed_test';
  protected $_has_many = array('lectures' => array('field' => 'course_id',
                                                   'class' => 'TypedLectureForTest'));
}

class lmbActiveRecordSubclassingTest extends UnitTestCase
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
    lmbActiveRecord :: delete('TestOneTableTypedObject');
    lmbActiveRecord :: delete('CourseForTestForTypedLecture');
    lmbActiveRecord :: delete('TypedLectureForTest');
  }

  function testCreate()
  {
    $object1 = new FooOneTableTestObject();
    $object1->setTitle('Some title');
    $object1->save();

    $object2 = new FooOneTableTestObject($object1->getId());
    $this->assertEqual($object2->title, $object1->title);

    //parents are supertypes
    $object3 = new TestOneTableTypedObject($object1->getId());

    try
    {
      new BarOneTableTestObject($object1->getId());
      $this->assertTrue(false);
    }
    catch(lmbARException $e){}
  }

  function testParentDelete()
  {
    $foo = new FooOneTableTestObject();
    $foo->setTitle('Some title');
    $foo->save();

    $bar = new BarOneTableTestObject();
    $bar->setTitle('Another title');
    $bar->save();

    lmbActiveRecord :: delete('TestOneTableTypedObject');

    $rs = lmbActiveRecord :: find('FooOneTableTestObject');
    $this->assertEqual($rs->count(), 0);

    $rs = lmbActiveRecord :: find('BarOneTableTestObject');
    $this->assertEqual($rs->count(), 0);
  }

  function testTypedDelete()
  {
    $foo = new FooOneTableTestObject();
    $foo->setTitle('Some title');
    $foo->save();

    $bar = new BarOneTableTestObject();
    $bar->setTitle('Another title');
    $bar->save();

    lmbActiveRecord :: delete('FooOneTableTestObject');

    $rs = lmbActiveRecord :: find('FooOneTableTestObject');
    $this->assertEqual($rs->count(), 0);

    $rs = lmbActiveRecord :: find('BarOneTableTestObject');
    $this->assertEqual($rs->count(), 1);
  }

  function testParentFind()
  {
    $object1 = new FooOneTableTestObject();
    $object1->setTitle('Some title');
    $object1->save();

    $object2 = new BarOneTableTestObject();
    $object2->setTitle('Some other title');
    $object2->save();

    $rs = lmbActiveRecord :: find('TestOneTableTypedObject');
    $this->assertEqual($rs->count(), 2);
    $this->assertIsA($rs->at(0), 'FooOneTableTestObject');
    $this->assertIsA($rs->at(1), 'BarOneTableTestObject');
  }

  function testOverrideBaseClass()
  {
    $object1 = new FooOneTableTestObject();
    $object1->setTitle('Some title');
    $object1->save();

    $object2 = new BarOneTableTestObject();
    $object2->setTitle('Some other title');
    $object2->save();

    $rs = lmbActiveRecord :: find('BaseOneTableTestObject');
    $this->assertEqual($rs->count(), 2);
    $this->assertIsA($rs->at(0), 'FooOneTableTestObject');
    $this->assertIsA($rs->at(1), 'BarOneTableTestObject');
  }

  function testTypedFind()
  {
    $object1 = new FooOneTableTestObject();
    $object1->setTitle('Some title');
    $object1->save();

    $object2 = new BarOneTableTestObject();
    $object2->setTitle('Some other title');
    $object2->save();

    $rs = lmbActiveRecord :: find('FooOneTableTestObject');
    $this->assertEqual($rs->count(), 1);
    $this->assertIsA($rs->at(0), 'FooOneTableTestObject');

    $rs = lmbActiveRecord :: find('BarOneTableTestObject');
    $this->assertEqual($rs->count(), 1);
    $this->assertIsA($rs->at(0), 'BarOneTableTestObject');
  }

  function testTypedRelationFind()
  {
    $course = new CourseForTestForTypedLecture();
    $course->setTitle('Source1');
    $course->save();

    $lecture1 = new FooLectureForTest();
    $lecture1->setTitle('Some title');
    $lecture1->setCourse($course);
    $lecture1->save();

    $lecture2 = new BarLectureForTest();
    $lecture2->setTitle('Some other title');
    $lecture2->setCourse($course);
    $lecture2->save();

    $course->getLectures()->add($lecture1);
    $course->getLectures()->add($lecture2);

    $course2 = new CourseForTestForTypedLecture($course->getId());

    $this->assertEqual($course2->getLectures()->count(), 2);
    $this->assertIsA($course2->getLectures()->at(0), 'FooLectureForTest');
    $this->assertIsA($course2->getLectures()->at(1), 'BarLectureForTest');

    $foo_lectures = $course2->getLectures()->find(array('class' => 'FooLectureForTest'));
    $this->assertEqual($foo_lectures->count(), 1);
    $this->assertIsA($foo_lectures->at(0), 'FooLectureForTest');

    $bar_lectures = $course2->getLectures()->find(array('class' => 'BarLectureForTest'));
    $this->assertEqual($bar_lectures->count(), 1);
    $this->assertIsA($bar_lectures->at(0), 'BarLectureForTest');
  }
}

?>
