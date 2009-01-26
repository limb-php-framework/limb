<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbDbTypeInfo.
 *
 * @package dbal
 * @version $Id: lmbDbTypeInfo.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbDbTypeInfo
{
  const TYPE_SMALLINT =    10;
  const TYPE_INTEGER =     11;

  const TYPE_BOOLEAN =     20;

  const TYPE_CHAR =        30;
  const TYPE_VARCHAR =     31;

  const TYPE_FLOAT =       40;
  const TYPE_DOUBLE =      41;
  const TYPE_DECIMAL =     42;

  const TYPE_TIMESTAMP =   50;
  const TYPE_DATE =        51;
  const TYPE_TIME =        52;

  const TYPE_BLOB =        60;
  const TYPE_CLOB =        70;

  const TYPE_BIT =         80;

  function getColumnTypeList()
  {
    return array(
      self::TYPE_BIT,
      self::TYPE_SMALLINT,
      self::TYPE_INTEGER,
      self::TYPE_BOOLEAN,
      self::TYPE_CHAR,
      self::TYPE_VARCHAR,
      self::TYPE_FLOAT,
      self::TYPE_DOUBLE,
      self::TYPE_DECIMAL,
      self::TYPE_TIMESTAMP,
      self::TYPE_DATE,
      self::TYPE_TIME,
      self::TYPE_BLOB,
      self::TYPE_CLOB
    );
  }

  function getNativeToColumnTypeMapping()
  {
    return array(
      'bit' => self::TYPE_BIT,
      'tinyint' => self::TYPE_SMALLINT,
      'smallint' => self::TYPE_SMALLINT,
      'mediumint' => self::TYPE_INTEGER,
      'number' => self::TYPE_INTEGER,
      'int' => self::TYPE_INTEGER,
      'int2' => self::TYPE_INTEGER,
      'int4' => self::TYPE_INTEGER,
      'int8' => self::TYPE_INTEGER,
      'integer' => self::TYPE_INTEGER,
      'bigint' => self::TYPE_DECIMAL,
      'real' => self::TYPE_FLOAT,
      'float' => self::TYPE_FLOAT,
      'float4' => self::TYPE_FLOAT,
      'float8' => self::TYPE_FLOAT,
      'cash' => self::TYPE_FLOAT,
      'money' => self::TYPE_FLOAT,
      'decimal' => self::TYPE_DECIMAL,
      'numeric' => self::TYPE_DECIMAL,
      'double' => self::TYPE_DOUBLE,
      'char' => self::TYPE_CHAR,
      'varchar' => self::TYPE_VARCHAR,
      'varchar2' => self::TYPE_VARCHAR,
      'nvarchar2' => self::TYPE_VARCHAR,
      'date' => self::TYPE_DATE,
      'time' => self::TYPE_TIME,
      'year' => self::TYPE_INTEGER,
      'datetime' => self::TYPE_TIMESTAMP,
      'timestamp' => self::TYPE_TIMESTAMP,
      'blob' => self::TYPE_BLOB,
      'tinytext' => self::TYPE_CLOB,
      'mediumtext' => self::TYPE_CLOB,
      'text' => self::TYPE_CLOB,
      'longtext' => self::TYPE_CLOB,
      'enum' => self::TYPE_CHAR,
      'set' => self::TYPE_CHAR,
      'bool' => self::TYPE_BOOLEAN
     );
  }

  function getColumnTypeAccessors()
  {
    return array(
      self::TYPE_BIT => 'setBit',
      self::TYPE_SMALLINT => 'setInteger',
      self::TYPE_INTEGER => 'setInteger',
      self::TYPE_BOOLEAN => 'setBoolean',
      self::TYPE_CHAR => 'setChar',
      self::TYPE_VARCHAR => 'setChar',
      self::TYPE_FLOAT => 'setFloat',
      self::TYPE_DOUBLE => 'setDouble',
      self::TYPE_DECIMAL => 'setDecimal',
      self::TYPE_TIMESTAMP => 'setTimeStamp',
      self::TYPE_DATE => 'setDate',
      self::TYPE_TIME => 'setTime',
      self::TYPE_BLOB => 'setBlob',
      self::TYPE_CLOB => 'setClob',
    );
  }

  function getColumnTypeGetters()
  {
    return array(
      self::TYPE_BIT => 'getBit',
      self::TYPE_SMALLINT => 'getInteger',
      self::TYPE_INTEGER => 'getInteger',
      self::TYPE_BOOLEAN => 'getBoolean',
      self::TYPE_CHAR => 'getString',
      self::TYPE_VARCHAR => 'getString',
      self::TYPE_FLOAT => 'getFloat',
      self::TYPE_DOUBLE => 'getFloat',
      self::TYPE_DECIMAL => 'getFloat',
      self::TYPE_TIMESTAMP => 'getIntegerTimeStamp',
      self::TYPE_DATE => 'getStringDate',
      self::TYPE_TIME => 'getStringTime',
      self::TYPE_BLOB => 'getBlob',
      self::TYPE_CLOB => 'getClob',
    );
  }
}

