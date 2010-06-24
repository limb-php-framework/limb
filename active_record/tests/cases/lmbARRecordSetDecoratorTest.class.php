<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/active_record/src/lmbARRecordSetDecorator.class.php');
require_once(dirname(__FILE__) . '/lmbAROneToManyRelationsTest.class.php');

class lmbARRecordSetDecoratorTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('lecture_for_test', 'course_for_test'); 
  
  function testCreateActiveRecordFromCurrentRecord()
  {
    $course = $this->_createCourseWithTwoLectures();

    $db = new lmbSimpleDb(lmbToolkit :: instance()->getDefaultDbConnection());
    $decorated = $db->select('lecture_for_test');

    $iterator = new lmbARRecordSetDecorator($decorated, 'LectureForTest');
    $iterator->rewind();

    $lecture1 = $iterator->current();
    $this->assertEqual($lecture1->getCourse()->getTitle(), $course->getTitle());

    $iterator->next();
    $lecture2 = $iterator->current();
    $this->assertEqual($lecture2->getCourse()->getTitle(), $course->getTitle());
  }

  function testGetOffsetIsDecorated()
  {
    $course = $this->_createCourseWithTwoLectures();

    $db = new lmbSimpleDb(lmbToolkit :: instance()->getDefaultDbConnection());
    $decorated = $db->select('lecture_for_test');

    $iterator = new lmbARRecordSetDecorator($decorated, 'LectureForTest');

    $this->assertEqual($iterator->at(0)->getCourse()->getTitle(), $course->getTitle());
    $this->assertEqual($iterator[0]->getCourse()->getTitle(), $course->getTitle());

    $this->assertEqual($iterator->at(1)->getCourse()->getTitle(), $course->getTitle());
    $this->assertEqual($iterator[1]->getCourse()->getTitle(), $course->getTitle());
  }

  function testIfRecordIsEmpty()
  {
    $iterator = new lmbARRecordSetDecorator(new lmbCollection(), 'LectureForTest');
    $iterator->rewind();
    $this->assertFalse($iterator->valid());
  }

  function _createCourseWithTwoLectures()
  {
    $course = new CourseForTest();
    $course->setTitle($title = 'General Course');

    $l1 = new LectureForTest();
    $l1->setTitle('Physics');
    $l2 = new LectureForTest();
    $l2->setTitle('Math');

    $course->addToLectures($l1);
    $course->addToLectures($l2);
    $course->save();

    return $course;
  }
}

