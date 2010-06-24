<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/drivers/lmbDbInfo.class.php');
lmb_require('limb/dbal/src/drivers/mysqli/lmbMysqliTableInfo.class.php');

/**
 * class lmbMysqliDbInfo.
 *
 * @package dbal
 * @version $Id: lmbMysqliDbInfo.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbMysqliDbInfo extends lmbDbInfo
{
  protected $connection;
  protected $isExisting = false;

  function __construct($connection, $name, $isExisting = false)
  {
    $this->connection = $connection;
    $this->isExisting = $isExisting;
    parent::__construct($name);
  }

  function getConnection()
  {
    return $this->connection;
  }

  function loadTables()
  {
    if($this->isExisting)
    {
      $queryId = $this->connection->execute("SHOW TABLES FROM `" . $this->name . "`");
      while(is_array($row = Mysqli_fetch_row($queryId)))
      {
        $this->tables[$row[0]] = null;
      }
      Mysqli_free_result($queryId);
      $this->isTablesLoaded = true;
    }
    else
    {
      $this->tables = array();
    }
  }

  function getTable($name)
  {
    if(!$this->hasTable($name))
    {
      throw new lmbDbException("Table does not exist '$name'");
    }
    if(is_null($this->tables[$name]))
    {
      $this->tables[$name] = new lmbMysqliTableInfo($this, $name, true);
    }
    return $this->tables[$name];
  }
}


