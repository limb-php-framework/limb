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
 * class lmbLinterTypeInfo.
 *
 * @package dbal
 * @version $Id: $
 */
class lmbLinterTypeInfo extends lmbDbTypeInfo
{
  function getNativeToColumnTypeMapping()
  {
    return array(
      'SMALLINT' => self::TYPE_SMALLINT,
      'INTEGER' => self::TYPE_INTEGER,
      'INT' => self::TYPE_INTEGER,
      'BIGINT' => self::TYPE_DECIMAL,
      'BYTE' => self::TYPE_CHAR,
      'NCHAR VARYING' => self::TYPE_VARCHAR,
      'NCHAR_VARYING' => self::TYPE_VARCHAR,
      'NCHAR' => self::TYPE_VARCHAR,
      'REAL' => self::TYPE_FLOAT,
      'FLOAT' => self::TYPE_FLOAT,
      'cash' => self::TYPE_FLOAT,
      'money' => self::TYPE_FLOAT,
      'DECIMAL' => self::TYPE_DECIMAL,
      'NUMERIC' => self::TYPE_DECIMAL,
      'DOUBLE' => self::TYPE_DOUBLE,
      'CHAR' => self::TYPE_CHAR,
      'CHARACTER' => self::TYPE_CHAR,
      'VARCHAR' => self::TYPE_VARCHAR,
      'DATE' => self::TYPE_DATE,
      'BLOB' => self::TYPE_BLOB,
      'BOOLEAN' => self::TYPE_BOOLEAN
   );
  }
}

