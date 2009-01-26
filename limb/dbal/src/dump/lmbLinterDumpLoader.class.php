<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/fs/src/exception/lmbFileNotFoundException.class.php');

/**
 * class lmbLinterDumpLoader.
 *
 * @package dbal
 * @version $Id: $
 */
class lmbLinterDumpLoader
{
  protected $affected_tables = array();
  protected $statements = array();

  function __construct($file_path = null)
  {
    if($file_path)
      $this->loadFile($file_path);
  }

  function getStatements()
  {
    return $this->statements;
  }

  function cleanTables($connection)
  {
    $db = new lmbSimpleDb($connection);

    foreach($this->affected_tables as $table)
      $db->delete($table);
  }

  function getAffectedTables()
  {
    return $this->affected_tables;
  }

  function execute($connection, $regex = '')
  {
    foreach($this->statements as $sql)
    {
      if($regex && !preg_match($regex, $sql, $m))
        continue;

      $stmt = $connection->newStatement($sql);
      $stmt->execute();
    }
  }

  function loadFile($file_path)
  {
    if(!file_exists($file_path))
      throw new lmbFileNotFoundException($file_path);

    $this->loadStatements(file_get_contents($file_path));
  }

  function loadStatements($sql)
  {
    $this->statements = $this->_retrieveStatements($sql);
    $this->affected_tables = $this->_getAffectedTables($this->statements);
  }

  protected function _getAffectedTables($stmts)
  {
    $affected_tables = array();
    foreach($stmts as $sql)
    {
      if(preg_match("|insert\s+?into\s+?([^\s]+)|i", $sql, $matches))
      {
        if(!in_array($matches[1], $affected_tables))
        {
          $affected_tables[] = $this->_processTableName($matches[1]);
        }
      }
    }
    return $affected_tables;
  }

  protected function _processTableName($name)
  {
    return $name;
  }

  protected function _retrieveStatements($raw_sql)
  {
    //naive implementation
    $stmts = preg_split('/;\s*\n/', $raw_sql);
    $processed = array();
    foreach($stmts as $stmt)
    {
      if($stmt = $this->_processStatement($stmt))
        $processed[] = $stmt;
    }
    return $processed;
  }

  protected function _processStatement($sql)
  {
    return $sql;
  }
}

