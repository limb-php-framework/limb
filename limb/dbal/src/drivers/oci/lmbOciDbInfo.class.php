<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/drivers/lmbDbInfo.class.php');
lmb_require(dirname(__FILE__) . '/lmbOciTableInfo.class.php');

/**
 * class lmbOciDbInfo.
 *
 * @package dbal
 * @version $Id: lmbOciDbInfo.class.php 8072 2010-01-20 08:33:41Z korchasa $
 */
class lmbOciDbInfo extends lmbDbInfo
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
      $config = $this->connection->getConfig();
      $schema = strtoupper($config['user']);
      $result = $this->connection->execute("SELECT TABLE_NAME FROM ALL_TABLES WHERE OWNER = '$schema'");

      while($row = oci_fetch_assoc($result))
      {
        $this->tables[strtolower($row['TABLE_NAME'])] = 1;
      }

      oci_free_statement($result);
      $this->isTablesLoaded = true;
    }
  }

  function getTable($name)
  {
    if(!$this->hasTable($name))
    {
      throw new lmbDbException('Table does not exist ' . $name);
    }
    if(!is_object($this->tables[$name]))
    {
      $config = $this->connection->getConfig();
      $this->tables[$name] = new lmbOciTableInfo($this, $name, $config['user'], true);
    }
    return $this->tables[$name];
  }
}


