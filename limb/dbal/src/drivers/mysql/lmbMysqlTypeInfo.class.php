<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMysqlTypeInfo.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */

lmb_require('limb/dbal/src/drivers/lmbDbTypeInfo.class.php');

class lmbMysqlTypeInfo extends lmbDbTypeInfo
{
  function getNativeToColumnTypeMapping()
  {
    return array(
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
      'date' => LIMB_DB_TYPE_DATE,
      'time' => LIMB_DB_TYPE_TIME,
      'year' => LIMB_DB_TYPE_INTEGER,
      'datetime' => LIMB_DB_TYPE_TIMESTAMP,
      'timestamp' => LIMB_DB_TYPE_TIMESTAMP,
      'tinyblob' => LIMB_DB_TYPE_BLOB,
      'blob' => LIMB_DB_TYPE_BLOB,
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
?>