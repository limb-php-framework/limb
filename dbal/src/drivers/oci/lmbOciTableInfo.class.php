<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/drivers/lmbDbTableInfo.class.php');
lmb_require(dirname(__FILE__) . '/lmbOciColumnInfo.class.php');

/**
 * class lmbOciTableInfo.
 *
 * @package dbal
 * @version $Id: lmbOciTableInfo.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbOciTableInfo extends lmbDbTableInfo
{
  protected $database;

  protected $isExisting = false;
  protected $isColumnsLoaded = false;

  protected $schema;

  function __construct($database, $name, $schema, $isExisting = false)
  {
    parent::__construct($name);
    $this->schema = $schema;
    $this->database = $database;
    $this->isExisting = $isExisting;
  }

  //Based on code from Creole
  function loadColumns()
  {
    if($this->isExisting && !$this->isColumnsLoaded)
    {
      $sql = "SELECT COLUMN_NAME, DATA_TYPE, DATA_PRECISION, DATA_LENGTH, DATA_DEFAULT, NULLABLE, DATA_SCALE
              FROM ALL_TAB_COLUMNS
              WHERE TABLE_NAME = '" . strtoupper($this->name) . "' AND OWNER = '" . strtoupper($this->schema) . "'";

      $connection = $this->database->getConnection();
      $result = $connection->execute($sql);

      while($row = oci_fetch_array($result, OCI_ASSOC + OCI_RETURN_NULLS))
      {
        $this->columns[strtolower($row['COLUMN_NAME'])] =
          new lmbOciColumnInfo($this,
             strtolower($row['COLUMN_NAME']),
             strtolower($row['DATA_TYPE']),
             $row['DATA_LENGTH'],
             $row['DATA_SCALE'] ,
             $row['NULLABLE'] ,
             $row['DATA_DEFAULT']);
      }
      $this->isColumnsLoaded = true;
    }
  }

  function getDatabase()
  {
    return $this->database;
  }

  function loadIndexes()
  {
    lmb_require('limb/core/src/exception/lmbNotYetImplementedException.class.php');
    throw new lmbNotYetImplementedException();
  }
}


