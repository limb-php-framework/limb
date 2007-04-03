<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActiveRecordDirtyTest.class.php 4984 2007-02-08 15:35:02Z pachanga $
 * @package    active_record
 */
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/dbal/src/lmbTableGateway.class.php');
require_once(dirname(__FILE__) . '/lmbActiveRecordTest.class.php');
require_once(dirname(__FILE__) . '/lmbActiveRecordOneToManyRelationsTest.class.php');
require_once(dirname(__FILE__) . '/lmbActiveRecordValueObjectTest.class.php');
require_once(dirname(__FILE__) . '/lmbActiveRecordOneToOneRelationsTest.class.php');

class lmbActiveRecordDirtyTest extends UnitTestCase
{
  var $conn = null;
  var $db = null;

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
    lmbActiveRecord :: delete('TestOneTableObject');
    lmbActiveRecord :: delete('LessonForTest');
    lmbActiveRecord :: delete('CourseForTest');
    lmbActiveRecord :: delete('LectureForTest');
  }

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
    $this->assertEqual($str, '|on_before_save||on_before_create||on_create||on_after_create||on_after_save|');

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
?>
