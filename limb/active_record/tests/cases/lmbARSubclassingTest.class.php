<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class TestOneTableTypedObject extends lmbActiveRecord
{
  protected $_db_table_name = 'test_one_table_typed_object';
}

class FooOneTableTestObject extends TestOneTableTypedObject{}
class BarFooOneTableTestObject extends FooOneTableTestObject{}

class TypedLectureForTest extends lmbActiveRecord
{
  protected $_db_table_name = 'lecture_for_typed_test';
  protected $_belongs_to = array('course' => array('field' => 'course_id',
                                                   'class' => 'CourseForTestForTypedLecture'));
}

class FooLectureForTest extends TypedLectureForTest{}
class BarFooLectureForTest extends FooLectureForTest{}

class CourseForTestForTypedLecture extends lmbActiveRecord
{
  protected $_db_table_name = 'course_for_typed_test';
  protected $_has_many = array('lectures' => array('field' => 'course_id',
                                                   'class' => 'TypedLectureForTest'),
                               'foo_lectures' => array('field' => 'course_id',
                                                       'class' => 'FooLectureForTest'));
}

class lmbARSubclassingTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('lecture_for_typed_test', 'course_for_typed_test', 'test_one_table_typed_object');

  function testCreate()
  {
    $object1 = new FooOneTableTestObject();
    $object1->setTitle('Some title');
    $object1->save();

    $object2 = new FooOneTableTestObject($object1->getId());
    $this->assertEqual($object2->getTitle(), $object1->getTitle());

    //parents are supertypes..
    $object3 = new TestOneTableTypedObject($object1->getId());
    $this->assertEqual($object3->getTitle(), $object1->getTitle());

    try
    {
      //..while deeper subclasses are not
      new BarFooOneTableTestObject($object1->getId());
      $this->assertTrue(false);
    }
    catch(lmbARException $e){}
  }

  function testSupertypeDelete()
  {
    $foo = new FooOneTableTestObject();
    $foo->setTitle('Some title');
    $foo->save();

    $bar = new BarFooOneTableTestObject();
    $bar->setTitle('Another title');
    $bar->save();

    lmbActiveRecord :: delete('TestOneTableTypedObject');

    $rs = lmbActiveRecord :: find('FooOneTableTestObject');
    $this->assertEqual($rs->count(), 0);

    $rs = lmbActiveRecord :: find('BarFooOneTableTestObject');
    $this->assertEqual($rs->count(), 0);
  }

  function testTypedDelete()
  {
    $foo = new FooOneTableTestObject();
    $foo->setTitle('Some title');
    $foo->save();

    $bar = new BarFooOneTableTestObject();
    $bar->setTitle('Another title');
    $bar->save();

    lmbActiveRecord :: delete('BarFooOneTableTestObject');//removing subclass

    $rs = lmbActiveRecord :: find('BarFooOneTableTestObject');
    $this->assertEqual($rs->count(), 0);

    $rs = lmbActiveRecord :: find('FooOneTableTestObject');//supertype stays
    $this->assertEqual($rs->count(), 1);

    lmbActiveRecord :: delete('FooOneTableTestObject');//removing supertype

    $rs = lmbActiveRecord :: find('FooOneTableTestObject');
    $this->assertEqual($rs->count(), 0);
  }

  function testFind()
  {
    $object1 = new FooOneTableTestObject();
    $object1->setTitle('Some title');
    $object1->save();

    $object2 = new BarFooOneTableTestObject();
    $object2->setTitle('Some other title');
    $object2->save();

    $rs = lmbActiveRecord :: find('FooOneTableTestObject');//supertype
    $this->assertEqual($rs->count(), 2);
    $this->assertIsA($rs->at(0), 'FooOneTableTestObject');
    $this->assertIsA($rs->at(1), 'BarFooOneTableTestObject');

    $rs = lmbActiveRecord :: find('BarFooOneTableTestObject');//subclass
    $this->assertEqual($rs->count(), 1);
    $this->assertIsA($rs->at(0), 'BarFooOneTableTestObject');
  }

  function testFindWithKind()
  {
    $object1 = new FooOneTableTestObject();
    $object1->setTitle('Some title');
    $object1->save();

    $object2 = new FooOneTableTestObject();
    $object2->setTitle('Some other title');
    $object2->save();

    $object3 = new BarFooOneTableTestObject();
    $object3->setTitle('Some other title');
    $object3->save();

    $object4 = new BarFooOneTableTestObject();
    $object4->setTitle('Some other title2');
    $object4->save();

    $criteria1 = lmbSQlCriteria::equal('title','Some other title2');
    $criteria2 = lmbSQlCriteria::equal('title','Some other title');

    $criteria = new lmbSQlCriteria();
    $criteria->add($criteria1->addOr($criteria2));

    $rs = lmbActiveRecord :: find('BarFooOneTableTestObject',array('criteria'=>$criteria));

    $this->assertEqual($rs->count(), 2);
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

    $lecture2 = new BarFooLectureForTest();
    $lecture2->setTitle('Some other title');
    $lecture2->setCourse($course);
    $lecture2->save();

    $course->getLectures()->add($lecture1);
    $course->getLectures()->add($lecture2);

    $course2 = new CourseForTestForTypedLecture($course->getId());

    $this->assertEqual($course2->getLectures()->count(), 2);//supertype by default
    $this->assertIsA($course2->getLectures()->at(0), 'FooLectureForTest');
    $this->assertIsA($course2->getLectures()->at(1), 'BarFooLectureForTest');

    //narrowing selection but again its supertype for BarFooLectureForTest
    $lectures = $course2->getLectures()->find(array('class' => 'FooLectureForTest'));

    $this->assertEqual($lectures->count(), 2);
    $this->assertIsA($lectures->at(0), 'FooLectureForTest');
    $this->assertIsA($lectures->at(1), 'BarFooLectureForTest');

    //narrowing more
    $lectures = $course2->getLectures()->find(array('class' => 'BarFooLectureForTest'));
    $this->assertEqual($lectures->count(), 1);
    $this->assertIsA($lectures->at(0), 'BarFooLectureForTest');
  }
}


