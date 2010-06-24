<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once(dirname(__FILE__) . '/../DriverStatementTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbLinterStatementTest extends DriverStatementTestBase
{
  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverLinterSetup($this->connection->getConnectionId());
    parent::setUp();
  }


  function setTypedValue($type, $column, $value)
  {
    $setterList = lmbDbTypeInfo::getColumnTypeAccessors();
    $setter = $setterList[$type];
    $this->assertNotNull($setter);

    $sql = '
          INSERT INTO standard_types (
              "'.$column.'"
          ) VALUES (
              :'.$column.':
          )';
    $stmt = $this->connection->newStatement($sql);

    $stmt->$setter($column, $value);

    $id = $stmt->insertId('id');

    $sql = 'SELECT * FROM standard_types WHERE "id" = :id:';
    $stmt = $this->connection->newStatement($sql);
    $stmt->setInteger('id', $id);
    $record = $stmt->getOneRecord();

    return $record;
  }


  function checkSmallIntValue($value)
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setSmallInt('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_SMALLINT, 'type_smallint', $value);
    $this->assertIdentical($record->getInteger('type_smallint'), $value);
    $this->assertEqual($record->get('type_smallint'), $value);
  }

  function checkIntegerValue($value)
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setInteger('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_INTEGER, 'type_integer', $value);
    $this->assertIdentical($record->getInteger('type_integer'), $value);
    $this->assertEqual($record->get('type_integer'), $value);
  }

  function checkBooleanValue($value)
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setBoolean('literal', $value);
    $this->assertEqual($stmt->getOneValue(), is_null($value) ? null : ($value ? 'TRUE' : 'FALSE'));

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_BOOLEAN, 'type_boolean', $value);

    if(is_null($value))
      $this->assertIdentical($record->getBoolean('type_boolean'), null);
    else
      $this->assertIdentical($record->getBoolean('type_boolean'), (boolean) $value);
  }

  function checkFloatValue($value)
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setFloat('literal', $value);
    $this->assertEqual($stmt->getOneValue(), (float) $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_FLOAT, 'type_float', $value);

    if(is_null($value))
      $this->assertIdentical($record->getFloat('type_float'), null);
    else
      $this->assertEqual(round($record->getFloat('type_float'), 2), round((float) $value, 2));

    $this->assertEqual(round($record->get('type_float'), 2), round($value, 2));
  }

  function checkDoubleValue($value)
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setDouble('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_DOUBLE, 'type_double', $value);

    if(is_string($value))
      $this->assertEqual($record->getStringFixed('type_double'), $value);
    else
      $this->assertEqual(round($record->getFloat('type_double'), 2), round($value, 2));

    $this->assertEqual(round($record->get('type_double'), 2), round($value, 2));
  }

  function checkDecimalValue($value)
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setDecimal('literal', $value);
    $this->assertEqual(round($stmt->getOneValue(), 2), is_null($value) ? 'null' : round($value, 2));

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_DECIMAL, 'type_decimal', $value);
    $this->assertEqual($record->getStringFixed('type_decimal'), is_null($value) ? 0 : $value);
    $this->assertEqual(round($record->get('type_decimal'), 2), is_null($value) ? 0 : round($value, 2));
  }

  function testSetDecimal()
  {
    $this->checkDecimalValue(0);
    $this->checkDecimalValue((float) 0);
    $this->checkDecimalValue(null);
    $this->checkDecimalValue(3.14);
    $this->checkDecimalValue('3.14');
    $this->checkDecimalValue('123456789012345678.01'); // To big for float
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
      $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_CHAR, 'type_char', $value);
      //some databases fill char fields with spaces and we have to trim values
      $this->assertIdentical(trim($record->getString('type_char')), $value);
      $this->assertEqual(trim($record->get('type_char')), $value);
    }

    $value = null;
    $stmt->setChar('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $value = null;
    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_CHAR, 'type_char', $value);
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
      $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_VARCHAR, 'type_varchar', $value);
      $this->assertIdentical($record->getString('type_varchar'), $value);
      $this->assertEqual($record->get('type_varchar'), $value);
    }

    $value = null;
    $stmt->setVarChar('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $value = null;
    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_VARCHAR, 'type_varchar', $value);
    $this->assertIdentical($record->getString('type_varchar'), $value);
    $this->assertEqual($record->get('type_varchar'), $value);

    $value = ' trim ';
    $stmt->setVarChar('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

  }


  function testBlob()
  {
    //$stmt = $this->connection->newStatement('SELECT :literal:');

    $string_list = array("Hello 'World!'",
          '"', '\'', '\\', '\\"', '\\\'', '\\0', '\\1',
          "%", "_", '&', '<', '>', '$', '`');

    foreach($string_list as $value)
    {
      $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_BLOB, 'type_blob', $value);
      $this->assertIdentical($record->getString('type_blob'), $value);
      $this->assertEqual($record->get('type_blob'), $value);
    }

    $value = null;
    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_BLOB, 'type_blob', $value);
    $this->assertIdentical($record->getString('type_blob'), is_null($value) ? '' : $value);
    $this->assertEqual($record->get('type_blob'), $value);

  }

  function testCharset()
  {
    $string_list = array("Текст", "ЁЁЁЁЁЁЁ");

    foreach($string_list as $value)
    {
      $value = mb_convert_encoding($value, $this->connection->getMbCharset(), 'UTF-8');

      $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_VARCHAR, 'type_varchar', $value);
      $this->assertIdentical($record->getString('type_varchar'), $value);
      $this->assertEqual($record->get('type_varchar'), $value);

      $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_BLOB, 'type_blob', $value);
      $this->assertIdentical($record->getString('type_blob'), $value);
      $this->assertEqual($record->get('type_blob'), $value);

    }

  }


  function testSetDate()
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');

    $value = null;
    $stmt->setDate('literal', $value);
    $this->assertEqual($stmt->getOneValue(), null);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_DATE, 'type_date', $value);
    $this->assertIdentical($record->getStringDate('type_date'), null);
    $this->assertEqual($record->get('type_date'), null);

    $value = '2009-12-28';

    $stmt->setDate('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_DATE, 'type_date', $value);
    $this->assertIdentical($record->getStringDate('type_date'), $value);
    $this->assertEqual($record->get('type_date'), $value . " 00:00:00");

    $value = '1941-12-07';

    $stmt->setDate('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_DATE, 'type_date', $value);
    $this->assertIdentical($record->getStringDate('type_date'), $value);
    $this->assertEqual($record->get('type_date'), $value . " 00:00:00");

    $value = 'Bad Date Value';
  }

  function testSetTime()
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');

    $value = null;
    $stmt->setTime('literal', $value);
    $this->assertEqual($stmt->getOneValue(), null);

    $value = null;
    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_TIME, 'type_time', $value);
    $this->assertIdentical($record->getString('type_time'), $value);
    $this->assertEqual($record->get('type_time'), $value);

    $value = '06:01:01';

    $stmt->setTime('literal', $value);
    $this->assertEqual($stmt->getOneValue(), date('Y-m-d') . " " . $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_TIME, 'type_time', $value);
    $this->assertIdentical($record->getStringTime('type_time'), $value);
    $this->assertEqual($record->get('type_time'), date('Y-m-d') . " " . $value);

    $value = '18:01:01';

    $stmt->setTime('literal', $value);
    $this->assertEqual($stmt->getOneValue(), date('Y-m-d') . " " . $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_TIME, 'type_time', $value);
    $this->assertIdentical($record->getStringTime('type_time'), $value);
    $this->assertEqual($record->get('type_time'), date('Y-m-d') . " " . $value);

    $value = 'Bad Time Value';
  }

  function testSetTimeStamp()
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');

    $value = null;
    $stmt->setTime('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_TIMESTAMP, 'type_timestamp', $value);
    $this->assertIdentical($record->getStringTimeStamp('type_timestamp'), $value);
    $this->assertIdentical($record->getIntegerTimeStamp('type_timestamp'), $value);
    $this->assertEqual($record->get('type_timestamp'), $value);

    $value = '2009-12-28 18:01:01';
    $stmt->setTime('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_TIMESTAMP, 'type_timestamp', $value);
    $this->assertIdentical($record->getStringTimeStamp('type_timestamp'), $value);
    $this->assertEqual($record->get('type_timestamp'), $value);

    $value = '2009-12-28 06:01:01';
    $stmt->setTime('literal', $value);
    $this->assertEqual($stmt->getOneValue(), $value);

    $record = $this->setTypedValue(lmbDbTypeInfo::TYPE_TIMESTAMP, 'type_timestamp', $value);
    $this->assertIdentical($record->getStringTimeStamp('type_timestamp'), $value);
    $this->assertIdentical($record->getIntegerTimeStamp('type_timestamp'),
          mktime(6, 1, 1, 12, 28, 2009));
    $this->assertEqual($record->get('type_timestamp'), $value);

    $value = 'Bad TimeStamp Value';
  }


  function testSetNull()
  {
    $stmt = $this->connection->newStatement('SELECT :literal:');
    $stmt->setNull('literal');
    $this->assertIdentical($stmt->getOneValue(), null);

    $sql = '
          INSERT INTO standard_types (
              "type_smallint",
              "type_integer",
              "type_boolean",
              "type_char",
              "type_varchar",
              "type_clob",
              "type_float",
              "type_double",
              "type_decimal",
              "type_timestamp",
              "type_date",
              "type_time",
              "type_blob"
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
          )';
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

    $sql = 'SELECT * FROM standard_types WHERE "id" = :id:';
    $stmt = $this->connection->newStatement($sql);
    $stmt->setInteger('id', $id);
    $record = $stmt->getOneRecord();

    /* generic gets */
    $this->assertEqual($record->get('type_smallint'), 0);
    $this->assertEqual($record->get('type_integer'), 0);
    $this->assertEqual($record->get('type_boolean'), 0);
    $this->assertEqual($record->get('type_char'), 0);
    $this->assertEqual($record->get('type_varchar'), 0);
    $this->assertEqual($record->get('type_clob'), 0);
    $this->assertEqual($record->get('type_float'), 0);
    $this->assertEqual($record->get('type_double'), 0);
    $this->assertEqual($record->get('type_decimal'), 0);
    $this->assertEqual($record->get('type_timestamp'), 0);
    $this->assertEqual($record->get('type_date'), 0);
    $this->assertEqual($record->get('type_time'), 0);
    $this->assertEqual($record->get('type_blob'), 0);

    /* typed gets */
    $this->assertEqual($record->getInteger('type_smallint'), 0);
    $this->assertEqual($record->getInteger('type_integer'), 0);
    $this->assertEqual($record->getBoolean('type_boolean'), 0);
    $this->assertEqual($record->getString('type_char'), 0);
    $this->assertEqual($record->getString('type_varchar'), 0);
    $this->assertEqual($record->getString('type_clob'), 0);
    $this->assertEqual($record->getFloat('type_float'), 0);
    $this->assertEqual($record->getStringFixed('type_double'), 0);
    $this->assertEqual($record->getStringFixed('type_decimal'), 0);
    $this->assertEqual($record->getStringTimeStamp('type_timestamp'), 0);
    $this->assertEqual($record->getStringDate('type_date'), 0);
    $this->assertEqual($record->getStringTime('type_time'), 0);
    $this->assertEqual($record->getString('type_blob'), 0);
  }
}
