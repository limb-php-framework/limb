<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/dbal/src/drivers/lmbDbManipulationStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbLinterStatement.class.php');

/**
 * class lmbLinterManipulationStatement.
 *
 * @package dbal
 * @version $Id: $
 */
class lmbLinterManipulationStatement extends lmbLinterStatement implements lmbDbManipulationStatement
{
  protected $queryId;
  protected $hasBlobs = false;
  protected $blobs = array();
  protected $tbl = null;
  protected $isUpdateQuery = false;
  protected $tableName;
  protected $whereCondition;
  protected $queryChecked = false;

  function getAffectedRowCount()
  {
    if($this->queryId > 0)
      return linter_get_cursor_opt($this->queryId, CO_ROW_COUNT);
  }


  function execute()
  {
    $this->checkQuery();
    if ($this->isUpdateQuery && $this->hasBlobs)
    {
      $pk = $this->tbl->getPrimaryKey();
      $sql = 'select * from "' . $this->tableName . '" ' . $this->whereCondition.';';
      $stmt = $this->connection->newStatement($sql);
      if (preg_match_all("#:(.+):#Us", $this->whereCondition, $matches, PREG_SET_ORDER))
      {
        foreach ($matches as $match)
        {
          if (isset($this->parameters[$match[1]]))
            $stmt->set($match[1], $this->parameters[$match[1]]);
        }
      }
      $rs = $stmt->getRecordset();
      if ($rs->count() > 1 && count($pk))
      {
        foreach ($rs as $k => $record)
        {
          $sql = 'UPDATE "' . $this->tableName . '" SET';

          foreach ($this->blobs as $name=>$value)
            $sql .= '"' . $name . '" = :' . $name . ':, ';

          $sql = substr($sql, 0, strlen($sql)-2);
          $sql .= ' WHERE ';

          foreach ($pk as $key)
            $sql .= '"' . $key . '" = :' . $key . ': AND ';

          $sql = substr($sql, 0, strlen($sql)-4);
          $s = $this->connection->newStatement($sql);

          foreach ($this->blobs as $name => $value)
            $s->set($name, $value);

          foreach ($pk as $key)
            $s->set($key, $record->get($key));

          $s->execute();
        }
      }
    }
    $this->queryId = parent :: execute();
    return $this->queryId;
  }

  protected function checkQuery()
  {
    if (!$this->queryChecked)
    {
      if (preg_match("#^\s*update\s+(\S+)\s+set.+(WHERE.+)?$#siU", $this->sql, $m))
      {
        $this->isUpdateQuery = true;
        $this->tableName = str_replace('"', '', $m[1]);

        if (isset($m[2]))
          $this->whereCondition = $m[2];

        $this->getTable();
      }
      $this->queryChecked = true;
    }
  }

  function set($name, $value)
  {
    $this->checkQuery();
    if ($this->isUpdateQuery && $this->tbl->hasColumn($name) && $this->tbl->getColumn($name)->getType() == lmbDbTypeInfo::TYPE_BLOB)
      $this->setBlob($name, $value);
    else
      parent::set($name, $value);
  }

  protected function getTable()
  {
    if (is_null($this->tbl))
      $this->tbl = new lmbLinterTableInfo($this->connection->getDatabaseInfo(), $this->tableName);
  }

  function setBlob($name, $value)
  {
    $this->checkQuery();
    $this->hasBlobs = true;
    $this->blobs[$name] = $value;
    parent::setBlob($name, $value);
  }

  function setClob($name, $value)
  {
    $this->checkQuery();
    $this->hasBlobs = true;
    $this->blobs[$name] = $value;
    parent::setClob($name, $value);
  }
}


