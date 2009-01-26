<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/drivers/lmbDbTypeInfo.class.php');

/**
 * class lmbOciTypeInfo.
 * @package dbal
 * @version $Id: lmbOciTypeInfo.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbOciTypeInfo extends lmbDbTypeInfo
{
  function getNativeToColumnTypeMapping()
  {
    return array(
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
            'clob' => self::TYPE_CLOB,
            'tinytext' => self::TYPE_CLOB,
            'mediumtext' => self::TYPE_CLOB,
            'text' => self::TYPE_CLOB,
            'longtext' => self::TYPE_CLOB,
            'enum' => self::TYPE_CHAR,
            'set' => self::TYPE_CHAR,
            'bool' => self::TYPE_BOOLEAN,
            'raw' => self::TYPE_CLOB
         );
  }

  function getColumnToNativeTypeMapping()
  {
    return array(
            self::TYPE_SMALLINT => 'number',
            self::TYPE_INTEGER => 'number',
            self::TYPE_BOOLEAN => 'number',
            self::TYPE_CHAR => 'char',
            self::TYPE_VARCHAR => 'varchar',
            self::TYPE_FLOAT => 'float',
            self::TYPE_DOUBLE => 'number',
            self::TYPE_DECIMAL => 'number',
            self::TYPE_TIMESTAMP => 'datetime',
            self::TYPE_DATE => 'date',
            self::TYPE_TIME => 'time',
            self::TYPE_BLOB => 'blob',
            self::TYPE_CLOB => 'clob',
        );
  }
}

