<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/drivers/lmbDbInfo.class.php');
lmb_require('limb/dbal/src/drivers/linter/lmbLinterTableInfo.class.php');

/**
 * class lmbLinterDbInfo.
 *
 * @package dbal
 * @version $Id: $
 */
class lmbLinterDbInfo extends lmbDbInfo
{
  protected $connection;
  protected $isExisting = false;
  protected $isTablesLoaded = false;

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

      $result = $this->connection->execute("select TABLE_NAME from TABLES WHERE TABLE_TYPE='TABLE' ORDER BY TABLE_NAME");

      while(is_array($row = linter_fetch_array($result)))
        $this->tables[$row['TABLE_NAME']] = $row['TABLE_NAME'];

      $this->connection->closeCursor($result);
      $this->isTablesLoaded = true;
    }
  }

  function getTable($name)
  {
    if(!$this->hasTable($name))
      throw new lmbDbException('Table does not exist ' . $name);

    if(!is_object($this->tables[$name]))
      $this->tables[$name] = new lmbLinterTableInfo($this, $name, true, $this->tables[$name]);

    return $this->tables[$name];
  }
}


