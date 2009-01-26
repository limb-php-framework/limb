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
 * abstract class lmbDbTableInfo.
 *
 * @package dbal
 * @version $Id: lmbDbTableInfo.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
abstract class lmbDbTableInfo
{
  protected $name;
  protected $columns = array();
  protected $indexes = array();
  protected $canonicalHandler;
  protected $cached_columns = array();

  function __construct($name, $canonicalHandler = null)
  {
    $this->name = $this->canonicalizeIdentifier($name);

    if($canonicalHandler !== null &&
       $canonicalHandler != 'strtolower' &&
       $canonicalHandler != 'strtoupper')
    {
      throw new lmbDbException("Invalid identifier compatability function '$canonicalHandler'");
    }
    $this->canonicalHandler = $canonicalHandler;
  }

  function canonicalizeIdentifier($id)
  {
    if(!is_null($this->canonicalHandler))
      return $this->canonicalHandler($id);

    return $id;
  }

  function getCanonicalColumnName($name)
  {
    $name = $this->canonicalizeIdentifier($name);
    // quick check if they happen to use the same case.
    if(array_key_exists($name, $this->columns))
      return $name;

    // slow check
    foreach(array_keys($this->columns) as $key)
    {
      if(strcasecmp($name, $key) == 0)
        return $key;
    }
    return $name;
  }

  function getName()
  {
    return $this->name;
  }

  abstract function loadColumns();
  abstract function loadIndexes();

  function hasColumn($name)
  {
    $old_name = $name;
    if(isset($this->cached_columns[$old_name]))
      return true;

    $this->loadColumns();
    $name = $this->getCanonicalColumnName($name);
    if(array_key_exists($name, $this->columns))
    {
      $this->cached_columns[$old_name] = $this->columns[$name];
      return true;
    }
    else
      return false;
  }

  function getColumn($name)
  {
    $old_name = $name;
    if(isset($this->cached_columns[$old_name]))
      return $this->cached_columns[$old_name];

    $this->loadColumns();
    $name = $this->getCanonicalColumnName($name);
    if(!array_key_exists($name, $this->columns))
    {
      throw new lmbDbException("Column '$name' does not exist");
    }

    $this->cached_columns[$old_name] = $this->columns[$name];
    return $this->cached_columns[$old_name];
  }

  function getColumnList()
  {
    $this->loadColumns();
    $result = array();
    foreach(array_keys($this->columns) as $name)
      $result[$name] = $name;
    return $result;
  }
  
  function getColumns()
  {
    $columns = array();
    foreach ($this->getColumnList() as $column_name)
      $columns[$column_name] = $this->getColumn($column_name);
    	
    return $columns;
  }

  function hasIndex($name)
  {
    $this->loadIndexes();
    return array_key_exists($name, $this->indexes);
  }

  function getIndex($name)
  {
    $this->loadIndexes();
    if(!array_key_exists($name, $this->indexes))
    {
      throw new lmbDbException("Index '$name' does not exist");
    }

    return $this->indexes[$name];
  }

  function getIndexList()
  {
    $this->loadIndexes();
    $result = array();
    foreach(array_keys($this->indexes) as $name)
      $result[$name] = $name;
    return $result;
  }

  function getIndexForColumn($column)
  {
    if(is_object($column))
      $column = $column->getName();

    $this->loadIndexes();
    foreach ($this->indexes as $index)
    {
      if($column == $index->getColumnName())
        return $index;
    }
    return null;
  }
}


