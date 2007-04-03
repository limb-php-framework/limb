<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDbTypeInfo.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
define('LIMB_DB_TYPE_SMALLINT',   10);
define('LIMB_DB_TYPE_INTEGER',    11);

define('LIMB_DB_TYPE_BOOLEAN',    20);

define('LIMB_DB_TYPE_CHAR',       30);
define('LIMB_DB_TYPE_VARCHAR',    31);

define('LIMB_DB_TYPE_FLOAT',      40);
define('LIMB_DB_TYPE_DOUBLE',     41);
define('LIMB_DB_TYPE_DECIMAL',    42);

define('LIMB_DB_TYPE_TIMESTAMP',  50);
define('LIMB_DB_TYPE_DATE',       51);
define('LIMB_DB_TYPE_TIME',       52);

define('LIMB_DB_TYPE_BLOB',       60);
define('LIMB_DB_TYPE_CLOB',       70);

class lmbDbTypeInfo
{
  function getColumnTypeList()
  {
    return array(
      LIMB_DB_TYPE_SMALLINT,
      LIMB_DB_TYPE_INTEGER,
      LIMB_DB_TYPE_BOOLEAN,
      LIMB_DB_TYPE_CHAR,
      LIMB_DB_TYPE_VARCHAR,
      LIMB_DB_TYPE_FLOAT,
      LIMB_DB_TYPE_DOUBLE,
      LIMB_DB_TYPE_DECIMAL,
      LIMB_DB_TYPE_TIMESTAMP,
      LIMB_DB_TYPE_DATE,
      LIMB_DB_TYPE_TIME,
      LIMB_DB_TYPE_BLOB,
      LIMB_DB_TYPE_CLOB
    );
  }

  function getNativeToColumnTypeMapping()
  {
    return array(
      'tinyint' => LIMB_DB_TYPE_SMALLINT,
      'smallint' => LIMB_DB_TYPE_SMALLINT,
      'mediumint' => LIMB_DB_TYPE_INTEGER,
      'number' => LIMB_DB_TYPE_INTEGER,
      'int' => LIMB_DB_TYPE_INTEGER,
      'int2' => LIMB_DB_TYPE_INTEGER,
      'int4' => LIMB_DB_TYPE_INTEGER,
      'int8' => LIMB_DB_TYPE_INTEGER,
      'integer' => LIMB_DB_TYPE_INTEGER,
      'bigint' => LIMB_DB_TYPE_DECIMAL,
      'real' => LIMB_DB_TYPE_FLOAT,
      'float' => LIMB_DB_TYPE_FLOAT,
      'float4' => LIMB_DB_TYPE_FLOAT,
      'float8' => LIMB_DB_TYPE_FLOAT,
      'cash' => LIMB_DB_TYPE_FLOAT,
      'money' => LIMB_DB_TYPE_FLOAT,
      'decimal' => LIMB_DB_TYPE_DECIMAL,
      'numeric' => LIMB_DB_TYPE_DECIMAL,
      'double' => LIMB_DB_TYPE_DOUBLE,
      'char' => LIMB_DB_TYPE_CHAR,
      'varchar' => LIMB_DB_TYPE_VARCHAR,
      'varchar2' => LIMB_DB_TYPE_VARCHAR,
      'nvarchar2' => LIMB_DB_TYPE_VARCHAR,
      'date' => LIMB_DB_TYPE_DATE,
      'time' => LIMB_DB_TYPE_TIME,
      'year' => LIMB_DB_TYPE_INTEGER,
      'datetime' => LIMB_DB_TYPE_TIMESTAMP,
      'timestamp' => LIMB_DB_TYPE_TIMESTAMP,
      'blob' => LIMB_DB_TYPE_BLOB,
      'tinytext' => LIMB_DB_TYPE_CLOB,
      'mediumtext' => LIMB_DB_TYPE_CLOB,
      'text' => LIMB_DB_TYPE_CLOB,
      'longtext' => LIMB_DB_TYPE_CLOB,
      'enum' => LIMB_DB_TYPE_CHAR,
      'set' => LIMB_DB_TYPE_CHAR,
      'bool' => LIMB_DB_TYPE_BOOLEAN
     );
  }

  function getColumnTypeAccessors()
  {
    return array(
      LIMB_DB_TYPE_SMALLINT => 'setInteger',
      LIMB_DB_TYPE_INTEGER => 'setInteger',
      LIMB_DB_TYPE_BOOLEAN => 'setBoolean',
      LIMB_DB_TYPE_CHAR => 'setChar',
      LIMB_DB_TYPE_VARCHAR => 'setChar',
      LIMB_DB_TYPE_FLOAT => 'setFloat',
      LIMB_DB_TYPE_DOUBLE => 'setDouble',
      LIMB_DB_TYPE_DECIMAL => 'setDecimal',
      LIMB_DB_TYPE_TIMESTAMP => 'setTimeStamp',
      LIMB_DB_TYPE_DATE => 'setDate',
      LIMB_DB_TYPE_TIME => 'setTime',
      LIMB_DB_TYPE_BLOB => 'setBlob',
      LIMB_DB_TYPE_CLOB => 'setClob',
    );
  }

  function getColumnTypeGetters()
  {
    return array(
      LIMB_DB_TYPE_SMALLINT => 'getInteger',
      LIMB_DB_TYPE_INTEGER => 'getInteger',
      LIMB_DB_TYPE_BOOLEAN => 'getBoolean',
      LIMB_DB_TYPE_CHAR => 'getString',
      LIMB_DB_TYPE_VARCHAR => 'getString',
      LIMB_DB_TYPE_FLOAT => 'getFloat',
      LIMB_DB_TYPE_DOUBLE => 'getFloat',
      LIMB_DB_TYPE_DECIMAL => 'getFloat',
      LIMB_DB_TYPE_TIMESTAMP => 'getIntegerTimeStamp',
      LIMB_DB_TYPE_DATE => 'getStringDate',
      LIMB_DB_TYPE_TIME => 'getStringTime',
      LIMB_DB_TYPE_BLOB => 'getBlob',
      LIMB_DB_TYPE_CLOB => 'getClob',
    );
  }
}
?>