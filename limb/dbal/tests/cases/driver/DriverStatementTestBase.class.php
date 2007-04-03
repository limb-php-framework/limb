<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: DriverStatementTestBase.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
require_once('limb/dbal/src/drivers/lmbDbTypeInfo.class.php');

abstract class DriverStatementTestBase extends UnitTestCase
{
  function setTypedValue($type, $column, $value)
  {
    $setterList = lmbDbTypeInfo::getColumnTypeAccessors();
    $setter = $setterList[$type];
    $this->assertNotNull($setter);

    $sql = "
          INSERT INTO standard_types (
              $column
          ) VALUES (
              :$column:
          )";
    $stmt = $this->connection->newStatement($sql);

    $stmt->$setter($column, $value);

    $id = $stmt->insertId('id');

    $sql = "SELECT * FROM standard_types WHERE id = :id:";
    $stmt = $this->connection->newStatement($sql);
    $stmt->setInteger('id', $id);
    $record = $stmt->getOneRecord();

    return $record;
  }

  function testSetNull()
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setNull('literal');
    $this->assertIdentical($stmt->getOneValue(), null);

    $sql = "
          INSERT INTO standard_types (
              type_smallint,
              type_integer,
              type_boolean,
              type_char,
              type_varchar,
              type_clob,
              type_float,
              type_double,
              type_decimal,
              type_timestamp,
              type_date,
              type_time,
              type_blob
          ) VALUES (
              :type_smallint:,
              :type_integer:,
              :type_boolean:,
              :type_char:,
              :type_varchar:,
              :type_clob:,
              :type_float:,
              :type_double:,
              :type_decimal:,
              :type_timestamp:,
              :type_date:,
              :type_time:,
              :type_blob:
          )";
    $stmt = $this->connection->newStatement($sql);

    $stmt->setNull('type_smallint');
    $stmt->setNull('type_integer');
    $stmt->setNull('type_boolean');
    $stmt->setNull('type_char');
    $stmt->setNull('type_varchar');
    $stmt->setNull('type_clob');
    $stmt->setNull('type_float');
    $stmt->setNull('type_double');
    $stmt->setNull('type_decimal');
    $stmt->setNull('type_timestamp');
    $stmt->setNull('type_date');
    $stmt->setNull('type_time');
    $stmt->setNull('type_blob');

    $id = $stmt->insertId('id');

    $sql = "SELECT * FROM standard_types WHERE id = :id:";
    $stmt = $this->connection->newStatement($sql);
    $stmt->setInteger('id', $id);
    $record = $stmt->getOneRecord();

    /* generic gets */
    $this->assertNull($record->get('type_smallint'));
    $this->assertNull($record->get('type_integer'));
    $this->assertNull($record->get('type_boolean'));
    $this->assertNull($record->get('type_char'));
    $this->assertNull($record->get('type_varchar'));
    $this->assertNull($record->get('type_clob'));
    $this->assertNull($record->get('type_float'));
    $this->assertNull($record->get('type_double'));
    $this->assertNull($record->get('type_decimal'));
    $this->assertNull($record->get('type_timestamp'));
    $this->assertNull($record->get('type_date'));
    $this->assertNull($record->get('type_time'));
    $this->assertNull($record->get('type_blob'));

    /* typed gets */
    $this->assertNull($record->getInteger('type_smallint'));
    $this->assertNull($record->getInteger('type_integer'));
    $this->assertNull($record->getBoolean('type_boolean'));
    $this->assertNull($record->getString('type_char'));
    $this->assertNull($record->getString('type_varchar'));
    $this->assertNull($record->getString('type_clob'));
    $this->assertNull($record->getFloat('type_float'));
    $this->assertNull($record->getStringFixed('type_double'));
    $this->assertNull($record->getStringFixed('type_decimal'));
    $this->assertNull($record->getStringTimeStamp('type_timestamp'));
    $this->assertNull($record->getStringDate('type_date'));
    $this->assertNull($record->getStringTime('type_time'));
    $this->assertNull($record->getString('type_blob'));
  }

  function checkSmallIntValue($value)
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setSmallInt('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(LIMB_DB_TYPE_SMALLINT, 'type_smallint', $value);
    $this->assertIdentical($record->getInteger('type_smallint'), $value);
    $this->assertEqual($record->get('type_smallint'), $value);
  }

  function testSetSmallInt()
  {
    $this->checkSmallIntValue(1);
    $this->checkSmallIntValue(0);
    $this->checkSmallIntValue(null);
    $this->checkSmallIntValue(32767);
    $this->checkSmallIntValue(-32767);
  }

  function checkIntegerValue($value)
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setInteger('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(LIMB_DB_TYPE_INTEGER, 'type_integer', $value);
    $this->assertIdentical($record->getInteger('type_integer'), $value);
    $this->assertEqual($record->get('type_integer'), $value);
  }

  function testSetInteger()
  {
    $this->checkIntegerValue(1);
    $this->checkIntegerValue(0);
    $this->checkIntegerValue(null);
    $this->checkIntegerValue(99999);
    $this->checkIntegerValue(-99999);
  }

  function checkBooleanValue($value)
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setBoolean('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(LIMB_DB_TYPE_BOOLEAN, 'type_boolean', $value);
    if(is_null($value))
    {
      $this->assertNull($record->getBoolean('type_boolean'));
    }
    else
    {
      $this->assertIdentical($record->getBoolean('type_boolean'), (boolean) $value);
    }
  }

  function testSetBoolean()
  {
    $this->checkBooleanValue(null);
    $this->checkBooleanValue(true);
    $this->checkBooleanValue(false);
    $this->checkBooleanValue(1);
    $this->checkBooleanValue(0);
  }

  function checkFloatValue($value)
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setFloat('literal', $value);
    $this->assertEqual($stmt->getOneValue(), (float) $value);

    $record = $this->setTypedValue(LIMB_DB_TYPE_FLOAT, 'type_float', $value);
    if(is_null($value))
    {
      $this->assertNull($record->getFloat('type_float'));
    }
    else
    {
      $this->assertIdentical($record->getFloat('type_float'), (float) $value);
    }
    $this->assertEqual($record->get('type_float'), $value);
  }

  function testSetFloat()
  {
    $this->checkFloatValue((float) 0);
    $this->checkFloatValue(null);
    $this->checkFloatValue(3.14);
    $this->checkFloatValue('3.14');
  }

  function checkDoubleValue($value)
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setDouble('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(LIMB_DB_TYPE_DOUBLE, 'type_double', $value);
    if(is_string($value))
    {
      $this->assertEqual($record->getStringFixed('type_double'), $value);
    }
    else
    {
      $this->assertEqual($record->getFloat('type_double'), $value);
    }
    $this->assertEqual($record->get('type_double'), $value);
  }

  function testSetDouble()
  {
    $this->checkDoubleValue(0);
    $this->checkDoubleValue((float) 0);
    $this->checkDoubleValue(null);
    $this->checkDoubleValue(3.14);
    $this->checkDoubleValue('3.14');
  }

  function checkDecimalValue($value)
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setDecimal('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(LIMB_DB_TYPE_DECIMAL, 'type_decimal', $value);
    $this->assertEqual($record->getStringFixed('type_decimal'), $value);
    $this->assertEqual($record->get('type_decimal'), $value);
  }

  function testSetDecimal()
  {
    $this->checkDecimalValue(0);
    $this->checkDecimalValue((float) 0);
    $this->checkDecimalValue(null);
    $this->checkDecimalValue(3.14);
    $this->checkDecimalValue('3.14');
    $this->checkDecimalValue('1234567890123456789.01'); // To big for float
  }

  function testSetChar()
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');

    $string_list = array("Hello 'World!'",
          '"', '\'', '\\', '\\"', '\\\'', '\\0', '\\1',
          "%", "_", '&', '<', '>', '$', '`');
    foreach($string_list as $value)
    {
      $stmt->setChar('literal', $value);
      $this->assertIdentical($stmt->getOneValue(), $value);
    }

    foreach($string_list as $value)
    {
      $record = $this->setTypedValue(LIMB_DB_TYPE_CHAR, 'type_char', $value);
      //some databases fill char fields with spaces and we have to trim values
      $this->assertIdentical(trim($record->getString('type_char')), $value);
      $this->assertEqual(trim($record->get('type_char')), $value);
    }

    $value = null;
    $stmt->setChar('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $value = null;
    $record = $this->setTypedValue(LIMB_DB_TYPE_CHAR, 'type_char', $value);
    $this->assertIdentical($record->getString('type_char'), $value);
    $this->assertEqual($record->get('type_char'), $value);

    $value = ' trim ';
    $value = null;
    $stmt->setChar('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);
  }

  function testSetVarChar()
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');

    $string_list = array("Hello 'World!'",
          '"', '\'', '\\', '\\"', '\\\'', '\\0', '\\1',
          "%", "_", '&', '<', '>', '$', '`');
    foreach($string_list as $value)
    {
      $stmt->setVarChar('literal', $value);
      $this->assertIdentical($stmt->getOneValue(), $value);
    }

    foreach($string_list as $value)
    {
      $record = $this->setTypedValue(LIMB_DB_TYPE_VARCHAR, 'type_varchar', $value);
      $this->assertIdentical($record->getString('type_varchar'), $value);
      $this->assertEqual($record->get('type_varchar'), $value);
    }

    $value = null;
    $stmt->setVarChar('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $value = null;
    $record = $this->setTypedValue(LIMB_DB_TYPE_VARCHAR, 'type_varchar', $value);
    $this->assertIdentical($record->getString('type_varchar'), $value);
    $this->assertEqual($record->get('type_varchar'), $value);

    $value = ' trim ';
    $stmt->setVarChar('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    //$value = ' trim ';
    //$record = $this->setTypedValue(LIMB_DB_TYPE_VARCHAR, 'type_varchar', $value);
    //$this->assertIdentical($record->getString('type_varchar'), rtrim($value));
    //$this->assertEqual($record->get('type_varchar'), rtrim($value));
  }

  function testSetDate()
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');

    $value = null;
    $stmt->setDate('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(LIMB_DB_TYPE_DATE, 'type_date', $value);
    $this->assertIdentical($record->getStringDate('type_date'), $value);
    $this->assertEqual($record->get('type_date'), $value);

    $value = '2009-12-28';

    $stmt->setDate('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(LIMB_DB_TYPE_DATE, 'type_date', $value);
    $this->assertIdentical($record->getStringDate('type_date'), $value);
    $this->assertEqual($record->get('type_date'), $value);

    $value = '1941-12-07';

    $stmt->setDate('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(LIMB_DB_TYPE_DATE, 'type_date', $value);
    $this->assertIdentical($record->getStringDate('type_date'), $value);
    $this->assertEqual($record->get('type_date'), $value);

    $value = 'Bad Date Value';
    // What should the expected behavior be?
  }

  function testSetTime()
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');

    $value = null;
    $stmt->setTime('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $value = null;
    $record = $this->setTypedValue(LIMB_DB_TYPE_TIME, 'type_time', $value);
    $this->assertIdentical($record->getString('type_time'), $value);
    $this->assertEqual($record->get('type_time'), $value);

    $value = '06:01:01';

    $stmt->setDate('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(LIMB_DB_TYPE_TIME, 'type_time', $value);
    $this->assertIdentical($record->getStringDate('type_time'), $value);
    $this->assertEqual($record->get('type_time'), $value);

    $value = '18:01:01';

    $stmt->setDate('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(LIMB_DB_TYPE_TIME, 'type_time', $value);
    $this->assertIdentical($record->getStringDate('type_time'), $value);
    $this->assertEqual($record->get('type_time'), $value);

    $value = 'Bad Time Value';
    // What should the expected behavior be?
  }

  function testSetTimeStamp()
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');

    $value = null;
    $stmt->setTime('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(LIMB_DB_TYPE_TIMESTAMP, 'type_timestamp', $value);
    $this->assertIdentical($record->getStringTimeStamp('type_timestamp'), $value);
    $this->assertIdentical($record->getIntegerTimeStamp('type_timestamp'), $value);
    $this->assertEqual($record->get('type_timestamp'), $value);

    $value = '2009-12-28 18:01:01';
    $stmt->setTime('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(LIMB_DB_TYPE_TIMESTAMP, 'type_timestamp', $value);
    $this->assertIdentical($record->getStringTimeStamp('type_timestamp'), $value);
    $this->assertEqual($record->get('type_timestamp'), $value);

    $value = '2009-12-28 06:01:01';
    $stmt->setTime('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(LIMB_DB_TYPE_TIMESTAMP, 'type_timestamp', $value);
    $this->assertIdentical($record->getStringTimeStamp('type_timestamp'), $value);
    $this->assertIdentical($record->getIntegerTimeStamp('type_timestamp'),
          mktime(6, 1, 1, 12, 28, 2009));
    $this->assertEqual($record->get('type_timestamp'), $value);

    $value = 'Bad TimeStamp Value';
    // What should the expected behavior be?
  }
}

?>
