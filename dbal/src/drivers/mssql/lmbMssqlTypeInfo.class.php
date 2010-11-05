<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/dbal/src/drivers/lmbDbTypeInfo.class.php');

/**
 * class lmbMssqlTypeInfo.
 *
 * @package dbal
 * @version $Id: lmbMssqlTypeInfo.class.php,v 1.1.1.1 2009/06/08 11:57:21 mike Exp $
 */
class lmbMssqlTypeInfo extends lmbDbTypeInfo
{
  function getNativeToColumnTypeMapping()
  {
    return array(
      'bit' => LIMB_DB_TYPE_BIT,
      'tinyint' => LIMB_DB_TYPE_SMALLINT,
      'smallint' => LIMB_DB_TYPE_SMALLINT,
      'mediumint' => LIMB_DB_TYPE_INTEGER,
      'int' => LIMB_DB_TYPE_INTEGER,
      'integer' => LIMB_DB_TYPE_INTEGER,
      'bigint' => LIMB_DB_TYPE_DECIMAL,
      'int24' => LIMB_DB_TYPE_INTEGER,
      'real' => LIMB_DB_TYPE_FLOAT,
      'float' => LIMB_DB_TYPE_FLOAT,
      'decimal' => LIMB_DB_TYPE_DECIMAL,
      'numeric' => LIMB_DB_TYPE_DECIMAL,
      'double' => LIMB_DB_TYPE_DOUBLE,
      'char' => LIMB_DB_TYPE_CHAR,
      'varchar' => LIMB_DB_TYPE_VARCHAR,
      'nvarchar' => LIMB_DB_TYPE_VARCHAR,
      'date' => LIMB_DB_TYPE_DATE,
      'time' => LIMB_DB_TYPE_TIME,
      'year' => LIMB_DB_TYPE_INTEGER,
      'datetime' => LIMB_DB_TYPE_DATE,
      'smalldatetime' => LIMB_DB_TYPE_DATE,
      'timestamp' => LIMB_DB_TYPE_TIMESTAMP,
      'tinyblob' => LIMB_DB_TYPE_BLOB,
      'blob' => LIMB_DB_TYPE_BLOB,
      'binary' => LIMB_DB_TYPE_BLOB,
      'image' => LIMB_DB_TYPE_BLOB,
      'mediumblob' => LIMB_DB_TYPE_BLOB,
      'longblob' => LIMB_DB_TYPE_BLOB,
      'tinytext' => LIMB_DB_TYPE_CLOB,
      'mediumtext' => LIMB_DB_TYPE_CLOB,
      'text' => LIMB_DB_TYPE_CLOB,
      'longtext' => LIMB_DB_TYPE_CLOB,
      'enum' => LIMB_DB_TYPE_CHAR,
      'set' => LIMB_DB_TYPE_CHAR,
      );
  }
}

