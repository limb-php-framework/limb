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
    $valid_object1 = new BarFooOneTableTestObject();
    $valid_object1->setTitle('title1');
    $valid_object1->save();

    $valid_object2 = new BarFooOneTableTestObject();
    $valid_object2->setTitle('title2');
    $valid_object2->save();

    $wrong_class_object = new FooOneTableTestObject();
    $wrong_class_object->setTitle('title1');
    $wrong_class_object->save();

    $wrong_title_object = new FooOneTableTestObject();
    $wrong_title_object->setTitle('wrong_title');
    $wrong_title_object->save();

    $criteria = new lmbSQLCriteria();
    $criteria->add(lmbSQLCriteria::equal('title', 'title1'));
    $criteria->addOr(lmbSQLCriteria::equal('title','title2'));

    $records = lmbActiveRecord :: find('BarFooOneTableTestObject', $criteria)->sort(array('id'))->getArray();
    $this->assertEqual(count($records), 2);
    $this->assertEqual($records[0]->title, $valid_object1->title);
    $this->assertEqual($records[1]->title, $valid_object2->title);
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


