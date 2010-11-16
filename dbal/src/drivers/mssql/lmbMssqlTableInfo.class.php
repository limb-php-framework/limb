<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/drivers/lmbDbTableInfo.class.php');
lmb_require('limb/dbal/src/drivers/mssql/lmbMssqlColumnInfo.class.php');

/**
 * class lmbMssqlTableInfo.
 *
 * @package dbal
 * @version $Id: lmbMssqlTableInfo.class.php,v 1.1.1.1 2009/06/08 11:57:21 mike Exp $
 */
class lmbMssqlTableInfo extends lmbDbTableInfo
{
  protected $isExisting = false;
  protected $isColumnsLoaded = false;
  protected $database;

  function __construct($database, $name, $isExisting = false)
  {
    parent::__construct($name);
    $this->database = $database;
    $this->isExisting = $isExisting;
  }

  function getDatabase()
  {
    return $this->database;
  }

  //Based on code from Creole
  function loadColumns()
  {
    if($this->isExisting && !$this->isColumnsLoaded)
    {
      $connection = $this->database->getConnection();
      //$queryId = $connection->execute("SHOW COLUMNS FROM `" . $this->name . "`");
      $queryId = $connection->execute("select * from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME='".$this->name."'");

      while($row = mssql_fetch_assoc($queryId))
      {
        $name = $row['COLUMN_NAME'];
        $isNullable =($row['IS_NULLABLE'] == 'YES');
        $isAutoIncrement =false;//(strpos($row['Extra'], 'auto_increment') !== false);
        $size = !empty($row['NUMERIC_PRECISION']) ? $row['NUMERIC_PRECISION'] : $row['CHARACTER_OCTET_LENGTH'];
        $precision = $row['NUMERIC_PRECISION_RADIX'];
        $nativeType = $row['DATA_TYPE'];


        // BLOBs can't have any default values in MySQL
        //$default = preg_match('~blob|text~', $nativeType) ?  null : $row['Default'];
        $default = $row['COLUMN_DEFAULT'];

        $this->columns[$name] = new lmbMssqlColumnInfo($this,
                    $name, $nativeType, $size, $precision, $isNullable, $default, $isAutoIncrement);
      }
      $this->isColumnsLoaded = true;
    }
  }
}


