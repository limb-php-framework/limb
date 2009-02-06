<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/lmbARAggregatedObjectTest.class.php');
require_once(dirname(__FILE__) . '/lmbActiveRecordTest.class.php');

class lmbARDirtyTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('lecture_for_test', 'course_for_test', 'test_one_table_object', 'member_for_test');

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

  function testUpdateOnlyDirtyFieldsInDbForNotNewObject()
  {
    $object = new TestOneTableObject();
    $object->setAnnotation('some annotation');
    $object->setContent($initial_content = 'some content');
    $object->save();

    $object->setAnnotation('some other annotation');
    $object->setContent('some other content');

    $object->resetPropertyDirtiness('content'); // suppose we don't want to save this field

    $object->save();

    $loaded_object = lmbActiveRecord :: findById('TestOneTableObject', $object->getId());
    $this->assertEqual($loaded_object->getAnnotation(), $object->getAnnotation());
    $this->assertEqual($loaded_object->getContent(), $initial_content);
  }

  function testUpdateWhileNoDirtyFields()
  {
    $object = new TestOneTableObject();
    $object->setAnnotation($initial_annotation = 'some annotation');
    $object->setContent($initial_content = 'some content');
    $object->save();

    $object->setAnnotation('some other annotation');
    $object->setContent('some other content');

    $object->resetPropertyDirtiness('content');
    $object->resetPropertyDirtiness('annotation');

    $object->save();

    $loaded_object = lmbActiveRecord :: findById('TestOneTableObject', $object->getId());
    $this->assertEqual($loaded_object->getAnnotation(), $initial_annotation);
    $this->assertEqual($loaded_object->getContent(), $initial_content);
  }

  function testSettingSameTablePropertyValueDoesntMakeObjectDirty()
  {
    $object = new TestOneTableObject();
    $object->setContent('whatever');
    $object->save();
    $this->assertFalse($object->isDirty());

    $object->setContent($object->getContent());
    $this->assertFalse($object->isDirty());

    $object->setContent('whatever else');
    $this->assertTrue($object->isDirty());
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

  function testSettingAggregatedObjectDoesNotMakesObjectDirty()
  {
    $member = new MemberForTest();

    $member->setName(new NameForAggregateTest());
    $this->assertFalse($member->isDirty());
  }

  function testAggregatedObjectFieldsAreCheckedForDirtinessOnSaveOnly()
  {
    $name = new NameForAggregateTest();
    $name->setFirst('name');

    $member = new MemberForTest();
    $member->setName($name);
    $member->save();

    $member2 = new MemberForTest($member->getId());
    $this->assertFalse($member->isDirty());

    $member2->getName()->setFirst('other name');
    $this->assertFalse($member2->isDirty());
    $member2->save();

    $member3 = new MemberForTest($member->getId());
    $this->assertEqual($member3->getName()->getFirst(), 'other name');
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

