<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/exception/lmbDbException.class.php');

/**
 * abstract class lmbDbInfo.
 *
 * @package dbal
 * @version $Id: lmbDbInfo.class.php 8072 2010-01-20 08:33:41Z korchasa $
 */
abstract class lmbDbInfo
{
  protected $tables = array();
  protected $name;
  protected $isTablesLoaded = false;

  function __construct($name)
  {
    $this->name = $name;
  }

  function getName()
  {
    return $this->name;
  }

  function getTable($name)
  {
    if(!$this->hasTable($name))
      throw new lmbDbException("Table '$name' does not exist");

    return $this->tables[$name];
  }

  function hasTable($name)
  {
    if(!$this->isTablesLoaded)
      $this->loadTables();
    return array_key_exists($name, $this->tables);
  }

  function getTableList()
  {
    if(!$this->isTablesLoaded)
      $this->loadTables();
    return array_keys($this->tables);
  }

  function getTables()
  {
    $tables = array();
    foreach ($this->getTableList() as $table_name)
      $tables[$table_name] = $this->getTable($table_name);
    return $tables;
  }

  abstract function loadTables();
}

