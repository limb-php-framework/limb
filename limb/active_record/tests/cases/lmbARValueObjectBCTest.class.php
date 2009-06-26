<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

// This test ensures that old value object functionality is still supported.

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

class TestingNullValueObject extends TestingValueObject
{
  function getValue()
  {
    return 'i\'m a null';
  }
}

class LessonForBCTest extends lmbActiveRecord
{
  protected $_db_table_name = 'lesson_for_test';

  protected $_composed_of = array('date_start' => array('field' => 'date_start',
                                                        'class' => 'TestingValueObject',
                                                        'getter' => 'getValue'),
                                  'date_end' => array('field' => 'date_end',
                                                      'class' => 'TestingValueObject',
                                                      'getter' => 'getValue'),
                                  'not_required_date' => array('field' => 'date_end',
                                                               'class' => 'TestingValueObject',
                                                               'getter' => 'getValue',
                                                               'can_be_null' => true));
}

class LessonWithNullObjectForBCTest extends LessonForBCTest
{
  protected $_db_table_name = 'lesson_for_test';

  function getNotRequiredDate()
  {
    $null_object = new TestingValueObject('null');
    return $this->get('not_required_date', $null_object);
  }
}

class LazyLessonForBCTest extends lmbActiveRecord
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

class lmbARValueObjectBCTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('lesson_for_test');

  function testNewObjectReturnsNullValueObjects()
  {
    $lesson = new LessonForBCTest();
    $this->assertNull($lesson->getDateStart());
    $this->assertNull($lesson->getDateEnd());
  }

  function testSaveLoadValueObjects()
  {
    $lesson = new LessonForBCTest();

    $lesson->setDateStart(new TestingValueObject($v1 = time()));
    $lesson->setDateEnd(new TestingValueObject($v2 = time() + 100));

    $lesson->save();

    $lesson2 = lmbActiveRecord :: findById('LessonForBCTest', $lesson->getId());
    $this->assertEqual($lesson2->getDateStart()->getValue(), $v1);
    $this->assertEqual($lesson2->getDateEnd()->getValue(), $v2);
  }

  function testGenericGetReturnsAlreadyExistingObject()
  {
    $lesson = new LessonForBCTest();

    $lesson->setDateStart(new TestingValueObject($v1 = time() - 100));
    $lesson->setDateEnd(new TestingValueObject($v2 = time() + 100));

    $this->assertEqual($lesson->get('date_start')->getValue(), $v1);
    $this->assertEqual($lesson->get('date_end')->getValue(), $v2);
  }

  function testLazyValueObjects()
  {
    $lesson = new LessonForBCTest();

    $lesson->setDateStart(new TestingValueObject($v1 = time()));
    $lesson->setDateEnd(new TestingValueObject($v2 = time() + 100));

    $lesson->save();

    $lesson2 = new LazyLessonForBCTest($lesson->getId());

    $this->assertEqual($lesson2->getDateStart()->getValue(), $v1);
    $this->assertEqual($lesson2->getDateEnd()->getValue(), $v2);
  }

  function testValueObjectsAreImportedAndExportedProperly()
  {
    $lesson = new LessonForBCTest();
    $lesson->setDateStart(new TestingValueObject($v1 = time()));
    $lesson->setDateEnd(new TestingValueObject($v2 = time() + 100));

    $lesson2 = new LessonForBCTest($lesson->export());

    $this->assertEqual($lesson2->getDateStart()->getValue(), $v1);
    $this->assertEqual($lesson2->getDateEnd()->getValue(), $v2);
  }

  function testImportValueObjectsAreImportedProperly()
  {
    $lesson = new LessonForBCTest();

    $imported = array(
      'date_start' => new TestingValueObject($v1 = time()),
      'date_end' => new TestingValueObject($v2 = (time() + 100))
    );

    $lesson->import($imported);

    $lesson2 = new LessonForBCTest($lesson->export());

    $this->assertEqual($lesson2->getDateStart()->getValue(), $v1);
    $this->assertEqual($lesson2->getDateEnd()->getValue(), $v2);
  }

  function testValueObjectsAreImportedNotFromObjects() {

    $lesson = new LessonForBCTest();

    $imported = array(
      'date_start' => time(),
      'date_end' => time() + 300
    );

    $lesson->import($imported);

    $lesson2 = new LessonForBCTest($lesson->export());

    $this->assertEqual($lesson2->getDateStart()->getValue(), $imported['date_start']);
    $this->assertEqual($lesson2->getDateEnd()->getValue(), $imported['date_end']);

  }

  function testAllowNullValuesForValuesObjects()
  {
    $lesson = new LessonForBCTest();
    $lesson->not_required_date = null;
    $this->assertNull($lesson->getNotRequiredDate());
  }

  function testGetDefaultObject()
  {
    $lesson = new LessonWithNullObjectForBCTest();
    $this->assertIdentical($lesson->getNotRequiredDate()->getValue(), 'null');
    $lesson->not_required_date = new TestingValueObject('not_null');
    $this->assertIdentical($lesson->getNotRequiredDate()->getValue(), 'not_null');
  }

  function testEmptyValueForValuesObjects()
  {
    $lesson = new LessonForBCTest();
    $lesson->not_required_date = '';
    $this->assertIdentical($lesson->getNotRequiredDate(), '');

    $lesson->not_required_date = 0;
    $this->assertIdentical($lesson->getNotRequiredDate(), 0);
  }

  function testProperWrapForScalrValueWhithNotRequiredFlagForValueObject()
  {
    $lesson = new LessonForBCTest();
    $lesson->not_required_date = 'test';
    $this->assertIsA($lesson->getNotRequiredDate(), 'TestingValueObject');
  }
}


