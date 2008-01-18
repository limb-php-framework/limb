<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/lmbARValueObjectTest.class.php');

class lmbARDirtyTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('lecture_for_test', 'course_for_test', 'test_one_table_object', 'lesson_for_test'); 
  
  function testJustFoundObjectIsNotDirty()
  {
    $object = new TestOneTableObject();
    $object->setContent('test');
    $object->save();

    $object2 = lmbActiveRecord :: find('TestOneTableObject', $object->getId());
    $this->assertFalse($object2->isDirty());
  }

  function testJustLoadedByIdObjectIsNotDirty()
  {
    $object = new TestOneTableObject();
    $object->setContent('test');
    $object->save();

    $object2 = new TestOneTableObject($object->getId());
    $this->assertFalse($object2->isDirty());
  }

  function testMarkDirty()
  {
    $object = new TestOneTableObject();
    $this->assertFalse($object->isDirty());
    $object->markDirty();
    $this->assertTrue($object->isDirty());
  }

  function testObjectBecomesDirtyIfAttributeIsSetWithSetter()
  {
    $object = new TestOneTableObject();
    $this->assertFalse($object->isDirty());
    $object->setContent('hey');
    $this->assertTrue($object->isDirty());
  }

  function testDirtyObjectBecomesCleanOnceSaved()
  {
    $object = new TestOneTableObject();
    $object->setContent('whatever');
    $this->assertTrue($object->isDirty());
    $object->save();
    $this->assertFalse($object->isDirty());
  }

  function testNonDirtyObjectIsNotUpdated()
  {
    $object = new TestOneTableObjectWithHooks();
    $object->setContent('whatever');

    ob_start();
    $object->save();
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, '|on_before_save||on_before_create||on_validate||on_save||on_create||on_after_create||on_after_save|');

    ob_start();
    $object->save();
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, '|on_before_save||on_after_save|');
  }

  function testSettingNewParentObjectDoesntMakeNewObjectDirty()
  {
    $course = new CourseForTest();

    $lecture = new LectureForTest();
    $lecture->setCourse($course);

    $this->assertTrue($lecture->isNew());
    $this->assertFalse($lecture->isDirty());
  }

  function testParentIsSavedEvenForCleanObject()
  {
    $course = new CourseForTest();
    $course->setTitle('course');
    $course->save();

    $lecture = new LectureForTest();
    $lecture->setCourse($course);
    $lecture->save();

    $lecture2 = new LectureForTest($lecture->getId());
    $this->assertEqual($lecture2->getCourse()->getTitle(), 'course');
  }

  function testChangingSavedParentObjectDoesntMakeObjectDirty()
  {
    $course = new CourseForTest();
    $course->setTitle('course');
    $course->save();

    $lecture = new LectureForTest();
    $lecture->setCourse($course);
    $lecture->save();

    $lecture2 = new LectureForTest($lecture->getId());
    $this->assertFalse($lecture2->isDirty());

    $course2 = $lecture2->getCourse();

    $course2->setTitle('changed_course');
    $this->assertFalse($lecture2->isDirty());
  }

  function testSettingExistingParentMakesNewObjectDirty()
  {
    $course = new CourseForTest();
    $course->setTitle('course');
    $course->save();

    $lecture = new LectureForTest();
    $lecture->setCourse($course);
    $this->assertTrue($lecture->isDirty());
    $lecture->save();

    $lecture2 = new LectureForTest($lecture->getId());
    $this->assertEqual($lecture2->getCourse()->getTitle(), $course->getTitle());
  }

  function testSettingExistingParentMakesExistingObjectDirty()
  {
    $course = new CourseForTest();
    $course->setTitle('course');
    $course->save();

    $lecture = new LectureForTest();
    $lecture->setTitle('test');
    $lecture->save();

    $lecture->setCourse($course);
    $this->assertTrue($lecture->isDirty());
    $lecture->save();

    $lecture2 = new LectureForTest($lecture->getId());
    $this->assertEqual($lecture2->getCourse()->getTitle(), $course->getTitle());
  }

  function testAddingToCollectionDoesntMakeNewObjectDirty()
  {
    $course = new CourseForTest();

    $lecture = new LectureForTest();

    $course->addToLectures($lecture);
    $this->assertFalse($course->isDirty());
  }

  function testAddingToCollectionDoesntMakeExistingObjectDirty()
  {
    $course = new CourseForTest();
    $course->setTitle('course');
    $course->save();

    $lecture = new LectureForTest();

    $course->addToLectures($lecture);
    $this->assertFalse($course->isDirty());
  }

  function testGettingCollectionDoesntMakeObjectDirty()
  {
    $course = new CourseForTest();
    $lectures = $course->getLectures();
    $this->assertFalse($course->isDirty());
  }

  function testSettingValueObjectMakesObjectDirty()
  {
    $lesson = new LessonForTest();

    $lesson->setDateStart(new TestingValueObject(time()));
    $this->assertTrue($lesson->isDirty());
  }

  function testSettingValueObjectMakesExistingObjectDirty()
  {
    $lesson = new LessonForTest();
    $lesson->setDateStart(new TestingValueObject(time()));
    $lesson->setDateStart(new TestingValueObject(time() + 30));
    $lesson->save();

    $lesson2 = new LessonForTest($lesson->getId());
    $this->assertFalse($lesson->isDirty());
    $lesson->setDateStart(new TestingValueObject(time() + 10));
    $this->assertTrue($lesson->isDirty());
  }

  function testUnsettingOneToOneChildObjectMakesPropertyDirty()
  {
    $person = new PersonForTest();
    $person->setName('Jim');
    $number = new SocialSecurityForTest();
    $number->setCode('099123');

    $person->setSocialSecurity($number);
    $person->save();

    $person->setSocialSecurity(null);
    $this->assertTrue($person->isDirtyProperty('social_security'));
  }
}

