<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActiveRecordValueObjectTest.class.php 4984 2007-02-08 15:35:02Z pachanga $
 * @package    active_record
 */
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');

class TestingValueObject
{
  var $value;

  function __construct($value)
  {
    $this->value = $value;
  }

  function getValue()
  {
    return $this->value;
  }
}

class LessonForTest extends lmbActiveRecord
{
  protected $_composed_of = array('date_start' => array('field' => 'date_start',
                                                        'class' => 'TestingValueObject',
                                                        'getter' => 'getValue'),
                                  'date_end' => array('field' => 'date_end',
                                                      'class' => 'TestingValueObject',
                                                      'getter' => 'getValue'));
}

class LazyLessonForTest extends lmbActiveRecord
{
  protected $_db_table_name = 'lesson_for_test';
  protected $_lazy_attributes = array('date_start');
  protected $_composed_of = array('date_start' => array('field' => 'date_start',
                                                        'class' => 'TestingValueObject',
                                                        'getter' => 'getValue'),
                                  'date_end' => array('field' => 'date_end',
                                                      'class' => 'TestingValueObject',
                                                      'getter' => 'getValue'));

}

class lmbActiveRecordValueObjectTest extends UnitTestCase
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
    lmbActiveRecord :: delete('LessonForTest');
  }

  function testNewObjectReturnsNullValueObjects()
  {
    $lesson = new LessonForTest();
    $this->assertNull($lesson->getDateStart());
    $this->assertNull($lesson->getDateEnd());
  }

  function testSaveLoadValueObjects()
  {
    $lesson = new LessonForTest();

    $lesson->setDateStart(new TestingValueObject($v1 = time()));
    $lesson->setDateEnd(new TestingValueObject($v2 = time() + 100));

    $lesson->save();

    $lesson2 = lmbActiveRecord :: findById('LessonForTest', $lesson->getId());
    $this->assertEqual($lesson2->getDateStart()->getValue(), $v1);
    $this->assertEqual($lesson2->getDateEnd()->getValue(), $v2);
  }

  function testGenericGetReturnsAlreadyExistingObject()
  {
    $lesson = new LessonForTest();

    $lesson->setDateStart(new TestingValueObject($v1 = time() - 100));
    $lesson->setDateEnd(new TestingValueObject($v2 = time() + 100));

    $this->assertEqual($lesson->get('date_start')->getValue(), $v1);
    $this->assertEqual($lesson->get('date_end')->getValue(), $v2);
  }

  function testLazyValueObjects()
  {
    $lesson = new LessonForTest();

    $lesson->setDateStart(new TestingValueObject($v1 = time()));
    $lesson->setDateEnd(new TestingValueObject($v2 = time() + 100));

    $lesson->save();

    $lesson2 = new LazyLessonForTest($lesson->getId());

    $this->assertEqual($lesson2->getDateStart()->getValue(), $v1);
    $this->assertEqual($lesson2->getDateEnd()->getValue(), $v2);
  }
}

?>
