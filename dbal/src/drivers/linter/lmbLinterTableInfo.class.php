<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/drivers/lmbDbTableInfo.class.php');
lmb_require('limb/dbal/src/drivers/linter/lmbLinterColumnInfo.class.php');

/**
 * class lmbLinterTableInfo.
 *
 * @package dbal
 * @version $Id: $
 */
class lmbLinterTableInfo extends lmbDbTableInfo
{
  protected $database;

  protected $isColumnsLoaded = false;


  function __construct($database, $name)
  {
    parent::__construct($name);
    $this->database = $database;
  }

  //Based on code from Creole
  function loadColumns()
  {
      $connection = $this->database->getConnection();

      $result = $connection->execute(sprintf("select * from COLUMNS where TABLE_NAME='%s'", $this->name));

      while(is_array($row = linter_fetch_array($result)))
      {
        $column = new lmbLinterColumnInfo($this, $row['COLUMN_NAME'], str_replace(" AUTOINC", "", $row['TYPE_NAME']), $row['COLUMN_SIZE'],
                      $row['DECIMAL_DIGITS'], $row['NULLABLE'], null, (strpos($row['TYPE_NAME'], 'AUTOINC') !== false)
        );
        $name = $row['COLUMN_NAME'];

        $this->columns[$name] = $column;
      }

      $this->isColumnsLoaded = true;
  }

  function getPrimaryKey()
  {
	  $sql = 'SELECT * FROM PRIMARY_KEYS WHERE TABLE_NAME = \'%s\'';
		$sql = sprintf($sql, $this->name);
		$stmt = $this->database->getConnection()->newStatement($sql);
		$rs = $stmt->getRecordset();
		$keys = array();

		foreach ($rs as $k => $record)
		  $keys[] = $record->get('COLUMN_NAME');

		$rs->freeQuery();
		return $keys;

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


