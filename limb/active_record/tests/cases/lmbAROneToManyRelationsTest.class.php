<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/active_record/src/lmbAROneToManyCollection.class.php');

class LecturesForTestCollectionStub extends lmbAROneToManyCollection{}

class CourseForTestWithCustomCollection extends lmbActiveRecord
{
  protected $_db_table_name = 'course_for_test';
  protected $_has_many = array('lectures' => array('field' => 'course_id',
                                                   'class' => 'LectureForTest',
                                                   'collection' => 'LecturesForTestCollectionStub'));
}

class CourseForTestWithNullifyRelationProperty extends lmbActiveRecord
{
  protected $_db_table_name = 'course_for_test';
  protected $_has_many = array('lectures' => array('field' => 'course_id',
                                                   'class' => 'LectureForTest',
                                                   'nullify' => true));
}

class CourseWithNullableLectures extends lmbActiveRecord
{
  protected $_db_table_name = 'course_for_test';
  protected $_has_many = array('lectures' => array('field' => 'course_id',
                                                   'class' => 'LectureIndependentFromCourse',
                                                   'nullify' => true),
                               );
}

class LectureIndependentFromCourse extends lmbActiveRecord
{
  protected $_db_table_name = 'lecture_for_test';
  protected $_many_belongs_to = array('course' => array('field' => 'course_id',
                                                        'class' => 'CourseWithNullableLectures',
                                                        'can_be_null' => true,
                                                        'throw_exception_on_not_found' => false),
                                      );
}

Mock :: generate('LectureForTest', 'MockLectureForTest');

class lmbAROneToManyRelationsTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('course_for_test', 'lecture_for_test');
  
  function testHas()
  {
    $lecture = new LectureForTest();
    $this->assertTrue(isset($lecture['course']));
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
    $this->assertEqual(sizeof($lectures), 2);
    $this->assertEqual($lectures[0]->getTitle(), $l1->getTitle());
    $this->assertEqual($lectures[1]->getTitle(), $l2->getTitle());
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

  function testLoadingNonExistingParentThrowsExceptionByDefault()
  {
    $course = $this->_initCourse();

    $lecture = new LectureForTest();
    $lecture->setTitle('Physics');
    $lecture->setCourse($course);
    $lecture->save();
    
    $this->db->delete('course_for_test', 'id = '. $course->getId());

    $lecture2 = lmbActiveRecord :: findById('LectureForTest', $lecture->getId());
    try
    {
      $lecture2->getCourse();
      $this->assertTrue(false);
    }
    catch(lmbARNotFoundException $e)
    {
      $this->assertTrue(true);
    }
  }
  
  function testLoadingNonExistingParent_NOT_ThrowsException_IfSpecialFlagUsedForRelationDefinition()
  {
    $course = $this->_initCourse();

    $lecture = new LectureIndependentFromCourse();
    $lecture->setTitle('Physics');
    $lecture->setCourse($course);
    $lecture->save();
    
    $this->db->delete('course_for_test', 'id = '. $course->getId());

    $lecture2 = lmbActiveRecord :: findById('LectureIndependentFromCourse', $lecture->getId());
    $this->assertNull($lecture2->getCourse());
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

  function testSavingChildForExistingParentDoesntSaveParent()
  {
    $course = $this->_initCourse();

    $this->assertEqual($course->save_calls, 0);

    $course->save();

    $this->assertEqual($course->save_calls, 1);

    $lecture = new LectureForTest();
    $lecture->setTitle('Physics');
    $lecture->setAltCourse($course);
    $lecture->save();

    $this->assertEqual($course->save_calls, 1);
  }

  function testChangingParentIdRelationFieldDirectly()
  {
    $course1 = $this->_initCourse();
    $course1->save();

    $course2 = $this->_initCourse();
    $course2->save();

    $lecture = new LectureForTest();
    $lecture->setTitle('Physics');
    $lecture->setCourse($course1);
    $lecture->save();

    $lecture2 = new LectureForTest($lecture->getId());
    $this->assertEqual($lecture2->getCourse()->getId(), $course1->getId());

    $lecture2->set('course_id', $course2->getId());
    $lecture2->save();

    $lecture3 = new LectureForTest($lecture->getId());
    $this->assertEqual($lecture3->getCourse()->getId(), $course2->getId());
  }

  function testChangingParentIdRelationFieldDirectlyDoesNotWorkIfParentObjectIsDirty()
  {
    $course1 = $this->_initCourse();
    $course1->save();

    $course2 = $this->_initCourse();
    $course2->save();

    $lecture = new LectureForTest();
    $lecture->setTitle('Physics');
    $lecture->setCourse($course1);
    $lecture->save();

    $lecture2 = new LectureForTest($lecture->getId());
    $this->assertEqual($lecture2->getCourse()->getId(), $course1->getId());

    $lecture2->set('course_id', $course2->getId());
    $lecture2->setCourse($course1);
    $lecture2->save();

    $lecture3 = new LectureForTest($lecture->getId());
    $this->assertEqual($lecture3->getCourse()->getId(), $course1->getId());
  }

  function testOwnerSetAutomaticallyForChildAddedToCollection()
  {
    $course = $this->_initCourse();

    $lecture = new LectureForTest();
    $lecture->setTitle('Physics');

    $course->getLectures()->add($lecture);

    $this->assertReference($lecture->getCourse(), $course);
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

    $this->assertNull(lmbActiveRecord :: findFirst('LectureForTest', array('criteria' => lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '= ' . $l1->getId())));
    $this->assertNull(lmbActiveRecord :: findFirst('LectureForTest', array('criteria' => lmbActiveRecord::getDefaultConnection()->quoteIdentifier("id") . '= ' . $l2->getId())));
  }

  function testNullifyOnDestroy()
  {
    $course = new CourseForTestWithNullifyRelationProperty();
    $course->setTitle('Super course');

    $l1 = new LectureForTest();
    $l1->setTitle('Physics');
    $l2 = new LectureForTest();
    $l2->setTitle('Math');

    $course->addToLectures($l1);
    $course->addToLectures($l2);

    $course->save();

    $course2 = new CourseForTestWithNullifyRelationProperty($course->getId());
    $course2->destroy();

    $lectures = lmbActiveRecord :: find('LectureForTest')->getArray();
    $this->assertEqual(sizeof($lectures), 2);
    $this->assertNull($lectures[0]->getCourseId());
    $this->assertNull($lectures[0]->getCourseId());
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

  function testErrorListIsSharedWithCollection()
  {
    $course = $this->_initCourse();

    $l = new LectureForTest();
    $validator = new lmbValidator();
    $validator->addRequiredRule('title');
    $l->setValidator($validator);

    $course->addToLectures($l);

    $error_list = new lmbErrorList();
    $this->assertFalse($course->trySave($error_list));
  }
  
  function testFetchWithRelatedObjects_UsingJoinMethod()
  {
    $course = $this->creator->createCourse();
    
    $alt_course1 = $this->creator->createCourse();
    $alt_course2 = $this->creator->createCourse();
    
    $lecture1 = $this->creator->createLecture($course, $alt_course1);
    $lecture2 = $this->creator->createLecture($course, $alt_course2);
    $lecture3 = $this->creator->createLecture($course, $alt_course1);
    
    $lectures = $course->getLectures()->join('course')->join('alt_course');
    $arr = $lectures->getArray();
    
    //make sure we really eager fetching
    $this->db->delete('course_for_test');
    
    $this->assertIsA($arr[0], 'LectureForTest');
    $this->assertEqual($arr[0]->getTitle(), $lecture1->getTitle());
    $this->assertIsA($arr[0]->getCourse(), 'CourseForTest');
    $this->assertEqual($arr[0]->getCourse()->getTitle(), $course->getTitle());
    $this->assertIsA($arr[0]->getAltCourse(), 'CourseForTest');
    $this->assertEqual($arr[0]->getAltCourse()->getTitle(), $alt_course1->getTitle());

    $this->assertIsA($arr[1], 'LectureForTest');
    $this->assertEqual($arr[1]->getTitle(), $lecture2->getTitle());
    $this->assertIsA($arr[1]->getCourse(), 'CourseForTest');
    $this->assertEqual($arr[1]->getCourse()->getTitle(), $course->getTitle());
    $this->assertIsA($arr[1]->getAltCourse(), 'CourseForTest');
    $this->assertEqual($arr[1]->getAltCourse()->getTitle(), $alt_course2->getTitle());

    $this->assertIsA($arr[2], 'LectureForTest');
    $this->assertEqual($arr[2]->getTitle(), $lecture3->getTitle());
    $this->assertIsA($arr[2]->getCourse(), 'CourseForTest');
    $this->assertEqual($arr[2]->getCourse()->getTitle(), $course->getTitle());
    $this->assertIsA($arr[2]->getAltCourse(), 'CourseForTest');
    $this->assertEqual($arr[2]->getAltCourse()->getTitle(), $alt_course1->getTitle());
  }
  
  function testFetchFirstWithRelationObjectsUsingAttach_AndThenSave()
  {
    $course1 = $this->creator->createCourse();
    $course2 = $this->creator->createCourse();
    
    $lecture1 = $this->creator->createLecture($course1);
    $lecture2 = $this->creator->createLecture($course2);
    $lecture3 = $this->creator->createLecture($course2);
    
    $course2_loaded = lmbActiveRecord :: findFirst('CourseForTest', array('criteria' => 'course_for_test.id = '. $course2->getId(), 'attach' => 'lectures'));
    
    $course2_loaded->setTitle('Some other title');
    
    $course2_loaded->save();
    
    $course2_loaded2 = lmbActiveRecord :: findFirst('CourseForTest', array('criteria' => 'course_for_test.id = '. $course2->getId(), 'attach' => 'lectures'));
    $lectures = $course2_loaded2->getLectures();
    $this->assertEqual(count($lectures), 2);
  }
  
  function testImportAndSaveNullableRelataions()
  {
    $course = new CourseWithNullableLectures();
    $course->setTitle("Title");
    $lecture1 = new LectureIndependentFromCourse();
    $lecture1->setTitle("Lecture 1");
    $lecture2 = new LectureIndependentFromCourse();
    $lecture2->setTitle("Lecture 2");
    $lecture3 = new LectureIndependentFromCourse();
    $lecture3->setTitle("Lecture 3");
    $course->setLectures(array($lecture1, $lecture2, $lecture3));
    $course->save();
    $this->assertEqual(lmbActiveRecord :: find("LectureForTest")->count(), 3);
    
    $course_arr = $course->export();
    $lect_arr = $course->getLectures()->getIds();
    array_pop($lect_arr);
    $course_arr['lectures'] = $lect_arr;
    $course->import($course_arr);
    $course->save();
    $this->assertEqual(lmbActiveRecord :: find("LectureForTest")->count(), 3);
  }
  
  function testSwapNullableRelations()
  {
    $course1 = new CourseWithNullableLectures();
    $lectA = new LectureIndependentFromCourse();
    $lectA->setTitle("Lecture A");
    $lectB = new LectureIndependentFromCourse();
    $lectB->setTitle("Lecture B");
    $course1->setLectures(array($lectA, $lectB));
    $course1->setTitle("Course 1");
    $course2 = new CourseWithNullableLectures();
    $lectC = new LectureIndependentFromCourse();
    $lectC->setTitle("Lecture C");
    $lectD = new LectureIndependentFromCourse();
    $lectD->setTitle("Lecture D");
    $course2->setLectures(array($lectC, $lectD));
    $course2->setTitle("Course 2");
    
    $course1->save();
    $course2->save();
    $c1 = $course1->export();
    $c2 = $course2->export();
    $c1['lectures'] = $course2->getLectures()->getIds();
    $c2['lectures'] = $course1->getLectures()->getIds();
    
    try 
    {
      $course1->import($c1);
      $course1->save();
      $course2 = new CourseWithNullableLectures($course2->getId());
      $course2->import($c2);
      $c2 = $course2->save();
    }
    catch (lmbARException $e){ }
    $this->assertEqual(lmbActiveRecord :: find("LectureForTest")->count(), 4);
  }

  function _initCourse()
  {
    $course = new CourseForTest();
    $course->setTitle('Course'. mt_rand());
    return $course;
  }
  
  function testCorrectUsageCrossRelations()
  {
    $program = new ProgramForTest();
    $program->setTitle('Program');
    $program->save();
    
    $course = new CourseForTest();
    $course->setProgram($program);
    $course->save();
    
    $lecture = new LectureForTest();
    $lecture->setCourse($course);
    $lecture->setCachedProgram($program);
    $lecture->save();
    
    try
    {
      $finded_lectures = $program->getCachedLectures()->find(array(
        'join' => array('course'),  
      ))->getArray();
    } 
    catch (lmbException $e) 
    {
      $this->assertTrue(false);  
    }
    
  }
}

 
