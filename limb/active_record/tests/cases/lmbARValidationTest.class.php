<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/validation/src/lmbErrorList.class.php');
require_once(dirname(__FILE__) . '/lmbActiveRecordTest.class.php');//need TestOneTableObjectFailing

Mock :: generate('lmbValidator', 'MockValidator');
Mock :: generate('lmbErrorList', 'MockErrorList');

class lmbActiveRecordValidationStub extends lmbActiveRecord
{
  protected $_db_table_name = 'test_one_table_object';
  protected $_insert_validator;
  protected $_update_validator;

  function setInsertValidator($validator)
  {
    $this->_insert_validator = $validator;
  }

  function setUpdateValidator($validator)
  {
    $this->_update_validator = $validator;
  }

  protected function _createInsertValidator()
  {
    return is_object($this->_insert_validator) ? $this->_insert_validator : new lmbValidator();
  }

  protected function _createUpdateValidator()
  {
    return is_object($this->_update_validator) ? $this->_update_validator : new lmbValidator();
  }
}

class lmbARValidationTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('test_one_table_object');
  
  function testGetErrorListReturnDefaultErrorList()
  {
    $object = $this->_createActiveRecord();
    $this->assertIsA($object->getErrorList(), 'lmbErrorList');
  }

  function testValidateNew()
  {
    $error_list = new lmbErrorList();
    $insert_validator = new MockValidator();
    $update_validator = new MockValidator();

    $object = $this->_createActiveRecord();
    $object->setInsertValidator($insert_validator);
    $object->setUpdateValidator($update_validator);

    $object->set('annotation', 'blah-blah');

    $insert_validator->expectOnce('setErrorList', array($error_list));
    $insert_validator->expectOnce('validate', array(new ReferenceExpectation($object)));
    $insert_validator->setReturnValue('validate', true);

    $update_validator->expectNever('setErrorList');
    $update_validator->expectNever('validate');

    $this->assertTrue($object->validate($error_list));
  }

  function testGetErrorListReturnLastErrorListUsed()
  {
    $error_list = new lmbErrorList();
    $insert_validator = new MockValidator();
    $object = $this->_createActiveRecord();
    $object->setInsertValidator($insert_validator);
    $insert_validator->setReturnValue('validate', true);
    $object->validate($error_list);

    $this->assertReference($object->getErrorList(), $error_list);
  }

  function testValidateNewFailed()
  {
    $error_list = new lmbErrorList();
    $insert_validator = new MockValidator();

    $object = $this->_createActiveRecord();
    $object->setInsertValidator($insert_validator);

    $insert_validator->expectOnce('setErrorList', array($error_list));
    $insert_validator->expectOnce('validate', array(new ReferenceExpectation($object)));
    $error_list->addError('foo');//simulating validation error

    $this->assertFalse($object->validate($error_list));
  }

  function testValidateExisting()
  {
    $error_list = new lmbErrorList();
    $insert_validator = new MockValidator();
    $update_validator = new MockValidator();

    $object = $this->_createActiveRecordWithDataAndSave();
    $object->setInsertValidator($insert_validator);
    $object->setUpdateValidator($update_validator);

    $update_validator->expectOnce('setErrorList', array($error_list));
    $update_validator->expectOnce('validate', array(new ReferenceExpectation($object)));
    $update_validator->setReturnValue('validate', true);

    $insert_validator->expectNever('setErrorList');
    $insert_validator->expectNever('validate');

    $this->assertTrue($object->validate($error_list));
  }

  function testValidateExistingFailed()
  {
    $error_list = new lmbErrorList();
    $update_validator = new MockValidator();

    $object = $this->_createActiveRecordWithDataAndSave();
    $object->setUpdateValidator($update_validator);

    $update_validator->expectOnce('setErrorList', array($error_list));
    $update_validator->expectOnce('validate', array(new ReferenceExpectation($object)));
    $error_list->addError('foo');//simulating validation error

    $this->assertFalse($object->validate($error_list));
  }

  function testDontInsertOnValidationError()
  {
    $object = $this->_createActiveRecord();

    $error_list = new lmbErrorList();

    $validator = new MockValidator();

    $object->setInsertValidator($validator);

    $object->set('annotation', $annotation = 'Super annotation');
    $object->set('content', $content = 'Super content');
    $object->set('news_date', $news_date = '2005-01-10');

    $validator->expectOnce('setErrorList', array($error_list));
    $validator->expectOnce('validate', array(new ReferenceExpectation($object)));
    $error_list->addError('foo');//simulating validation error

    try
    {
      $object->save($error_list);
      $this->assertTrue(false);
    }
    catch(lmbValidationException $e)
    {
      $this->assertEqual($e->getErrorList(), $error_list);
    }

    $this->assertEqual($this->db->count('test_one_table_object'), 0);
  }

  function testInsertOnValidationSuccess()
  {
    $object = $this->_createActiveRecord();

    $error_list = new lmbErrorList();

    $validator = new MockValidator();
    $object->setInsertValidator($validator);

    $object->set('annotation', $annotation = 'Super annotation');
    $object->set('content', $content = 'Super content');
    $object->set('news_date', $news_date = '2005-01-10');

    $validator->expectOnce('setErrorList', array($error_list));
    $validator->expectOnce('validate', array(new ReferenceExpectation($object)));

    $object->save($error_list);

    $this->assertEqual($this->db->count('test_one_table_object'), 1);
  }

  function testDoubleInsert_FirstSaveValidationError_But_SecondSaveIsOk()
  {
    $object = $this->_createActiveRecord();

    $validator = new MockValidator();
    $object->setInsertValidator($validator);

    $object->set('annotation', $annotation = 'Super annotation');
    $object->set('content', $content = 'Super content');
    $object->set('news_date', $news_date = '2005-01-10');

    $error_list = new MockErrorList();
    $error_list->setReturnValueAt(0, 'isValid', false);
    $error_list->setReturnValueAt(1, 'isValid', true);
    
    try
    {
      $object->save($error_list);
      $this->assertTrue(false);
    }
    catch(lmbValidationException $e)
    {
      $this->assertTrue(true);
    }

    $this->assertEqual($this->db->count('test_one_table_object'), 0);
    
    $object->save($error_list);

    $this->assertEqual($this->db->count('test_one_table_object'), 1);
  }
  
  function testDontUpdateOnValidationError()
  {
    $object = $this->_createActiveRecordWithDataAndSave();
    $old_annotation = $object->get('annotation');

    $error_list = new lmbErrorList();

    $validator = new MockValidator();
    $object->setUpdateValidator($validator);

    $object->set('annotation', $annotation = 'New annotation ' . time());

    $validator->expectOnce('setErrorList', array($error_list));
    $validator->expectOnce('validate', array(new ReferenceExpectation($object)));
    $error_list->addError('foo');//simulating validation error

    try
    {
      $object->save($error_list);
      $this->assertTrue(false);
    }
    catch(lmbValidationException $e)
    {
      $this->assertEqual($e->getErrorList(), $error_list);
    }

    $record = $this->db->selectRecord('test_one_table_object');
    $this->assertEqual($record->get('annotation'), $old_annotation);
  }

  function testUpdateOnValidationSuccess()
  {
    $object = $this->_createActiveRecordWithDataAndSave();

    $error_list = new lmbErrorList();

    $validator = new MockValidator();
    $object->setUpdateValidator($validator);

    $object->set('annotation', $annotation = 'New annotation ' . time());

    $validator->expectOnce('setErrorList', array($error_list));
    $validator->expectOnce('validate', array(new ReferenceExpectation($object)));
    $validator->setReturnValue('validate', true);

    $object->save($error_list);

    $record = $this->db->selectRecord('test_one_table_object');
    $this->assertEqual($record->get('annotation'), $annotation);
  }
  
  function testDoubleUpdate_FirstSaveValidationError_But_SecondSaveIsOk()
  {
    $object = $this->_createActiveRecordWithDataAndSave();

    $validator = new MockValidator();
    $object->setUpdateValidator($validator);

    $object->set('annotation', $annotation = 'Other annotation');

    $error_list = new MockErrorList();
    $error_list->setReturnValueAt(0, 'isValid', false);
    $error_list->setReturnValueAt(1, 'isValid', true);
    
    try
    {
      $object->save($error_list);
      $this->assertTrue(false);
    }
    catch(lmbValidationException $e)
    {
      $this->assertTrue(true);
    }

    $record = $this->db->selectRecord('test_one_table_object');
    $this->assertNotEqual($record->get('annotation'), $annotation);
    
    $object->save($error_list);

    $record = $this->db->selectRecord('test_one_table_object');
    $this->assertEqual($record->get('annotation'), $annotation);
  }  

  function testSaveSkipValidation()
  {
    $object = $this->_createActiveRecordWithDataAndSave();

    $validator = new MockValidator();
    $object->setUpdateValidator($validator);

    $object->set('annotation', $annotation = 'New annotation ' . time());

    $validator->expectNever('validate');

    $object->saveSkipValidation();

    $record = $this->db->selectRecord('test_one_table_object');
    $this->assertEqual($record->get('annotation'), $annotation);
  }

  function testIsValid()
  {
    $object = $this->_createActiveRecordWithDataAndSave();
    $this->assertTrue($object->isValid());
  }

  function testIsNotValid()
  {
    $error_list = new lmbErrorList();

    $object = $this->_createActiveRecordWithDataAndSave();
    $this->assertTrue($object->isValid());

    $error_list->addError('whatever');//actually it's a dirty simulation but that's how it works really

    $object->save($error_list);
    $this->assertFalse($object->isValid());
  }

  function testValidationExceptionIsNotAddedToErrorList()
  {
    $error_list = new lmbErrorList();

    $object = new TestOneTableObjectFailing();
    $object->setContent('A-a-a-a');
    $object->fail = new lmbValidationException('foo', $error_list);

    $this->assertFalse($object->trySave($error_list));
    $this->assertTrue($error_list->isEmpty());
  }

  function testNonValidationExceptionIsAddedToErrorList()
  {
    $error_list = new lmbErrorList();

    $object = new TestOneTableObjectFailing();
    $object->setContent('A-a-a-a');
    $object->fail = new Exception('yo-yo');

    $this->assertFalse($object->trySave($error_list));
    $this->assertFalse($error_list->isEmpty());
    $this->assertEqual(sizeof($error_list), 1);
    $this->assertPattern('~yo-yo~', $error_list[0]['message']);
  }

  function _createActiveRecord()
  {
    $object = new lmbActiveRecordValidationStub();
    return $object;
  }

  protected function _createActiveRecordWithDataAndSave()
  {
    $object = $this->_createActiveRecord();
    $object->set('annotation', 'Annotation ' . time());
    $object->set('content', 'Content ' . time());
    $object->set('news_date', date("Y-m-d", time()));
    $object->save();
    return $object;
  }
}

