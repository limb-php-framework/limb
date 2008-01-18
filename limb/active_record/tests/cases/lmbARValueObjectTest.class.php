<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

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

class TestingNullValueObject extends TestingValueObject {
  function getValue()
  {
    return 'i\'m a null';
  }
}

class LessonForTest extends lmbActiveRecord
{
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

class LessonWithNullObject extends LessonForTest 
{
  protected $_db_table_name = 'lesson_for_test';
  function getNotRequiredDate()
  {
    $null_object = new TestingValueObject('null');
    return $this->get('not_required_date', $null_object);
  }
}

class NotRequiredDateNullObject {}

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

class lmbARValueObjectTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('lesson_for_test');
  
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

  function testValueObjectsAreImportedProperly()
  {
    $lesson = new LessonForTest();
    $lesson->setDateStart(new TestingValueObject($v1 = time()));
    $lesson->setDateEnd(new TestingValueObject($v2 = time() + 100));

    $lesson2 = new LessonForTest($lesson->export());

    $this->assertEqual($lesson2->getDateStart()->getValue(), $v1);
    $this->assertEqual($lesson2->getDateEnd()->getValue(), $v2);
  }
  
  function testAllowNullValuesForValuesObjects()
  {
    $lesson = new LessonForTest();    
    $lesson->not_required_date = null;
    $this->assertNull($lesson->getNotRequiredDate());
  }
  
  function testGetDefaultObject()
  {
    $lesson = new LessonWithNullObject();    
    $this->assertIdentical($lesson->getNotRequiredDate()->getValue(), 'null');
    $lesson->not_required_date = new TestingValueObject('not_null');
    $this->assertIdentical($lesson->getNotRequiredDate()->getValue(), 'not_null');    
  }

  function testEmptyValueForValuesObjects()
  {
    $lesson = new LessonForTest();
    $lesson->not_required_date = '';
    $this->assertIdentical($lesson->getNotRequiredDate(), '');

    $lesson->not_required_date = 0;
    $this->assertIdentical($lesson->getNotRequiredDate(), 0);
  }

  function testProperWrapForScalrValueWhithNotRequiredFlagForValueObject()
  {
    $lesson = new LessonForTest();    
    $lesson->not_required_date = 'test';
    $this->assertIsA($lesson->getNotRequiredDate(), 'TestingValueObject');
  }
}


