<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbARRecordSetDecoratorTest.class.php 4984 2007-02-08 15:35:02Z pachanga $
 * @package    active_record
 */
lmb_require('limb/active_record/src/lmbARRecordSetDecorator.class.php');
lmb_require('limb/datasource/src/lmbEmptyIterator.class.php');
require_once(dirname(__FILE__) . '/lmbActiveRecordOneToManyRelationsTest.class.php');

class lmbARRecordSetDecoratorTest extends UnitTestCase
{
  function setUp()
  {
    $this->_dbCleanUp();
  }

  function tearDown()
  {
    $this->_dbCleanUp();
  }

  function _dbCleanUp()
  {
    lmbActiveRecord :: delete('CourseForTest');
    lmbActiveRecord :: delete('LectureForTest');
  }

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

  function testSetActiveRecordClassPathWithSetter()
  {
    $course = $this->_createCourseWithTwoLectures();

    $db = new lmbSimpleDb(lmbToolkit :: instance()->getDefaultDbConnection());
    $decorated = $db->select('lecture_for_test');

    $iterator = new lmbARRecordSetDecorator($decorated);
    $iterator->setClassPath('LectureForTest');
    $iterator->rewind();

    $lecture1 = $iterator->current();
    $this->assertEqual($lecture1->getCourse()->getTitle(), $course->getTitle());

    $iterator->next();
    $lecture2 = $iterator->current();
    $this->assertEqual($lecture2->getCourse()->getTitle(), $course->getTitle());
  }

  function testThrowExceptionIfClassPathIsNotDefined()
  {
    $course = $this->_createCourseWithTwoLectures();

    $db = new lmbSimpleDb(lmbToolkit :: instance()->getDefaultDbConnection());
    $decorated = $db->select('lecture_for_test');

    $iterator = new lmbARRecordSetDecorator($decorated);
    $iterator->rewind();
    try
    {
      $iterator->current();
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testIfRecordIsEmpty()
  {
    $iterator = new lmbARRecordSetDecorator(new lmbEmptyIterator());
    $iterator->setClassPath('LectureForTest');
    $iterator->rewind();
    $this->assertNull($iterator->current());
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
?>
