<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/drivers/lmbDbStatement.interface.php');

/**
 * class lmbLinterStatement.
 *
 * @package dbal
 * @version $Id: $
 */
class lmbLinterStatement implements lmbDbStatement
{
  protected $sql;
  protected $original_sql;
  protected $prepared_sql;
  protected $statement;
  protected $connection;
  protected $parameters = array();
  protected $queryId;
  protected $prepRequired = false;
  protected $prepParams = array();

  function __construct($connection, $sql)
  {
    $this->sql = $sql;
    $this->original_sql = $sql;
    $this->connection = $connection;
  }

  function setNull($name)
  {
    $this->parameters[$name] = null;
  }

  function setSmallInt($name, $value)
  {
    $this->parameters[$name] = is_null($value) ?  null : intval($value);
  }

  function setInteger($name, $value)
  {
    $this->parameters[$name] = is_null($value) ?  null : intval($value);
  }

  function setFloat($name, $value)
  {
    $this->parameters[$name] = is_null($value) ?
    null :
    floatval($value);
  }

  function setDouble($name, $value)
  {
    if(is_float($value) || is_integer($value))
      $this->parameters[$name] = $value;
    else if(is_string($value) && preg_match('/^(|-)\d+(|.\d+)$/', $value))
      $this->parameters[$name] = $value;
    else
      $this->parameters[$name] = null;
  }

  function setDecimal($name, $value)
  {
    if(is_float($value) || is_integer($value))
      $this->parameters[$name] = $value;
    else if(is_string($value) && preg_match('/^(|-)\d+(|.\d+)$/', $value))
      $this->parameters[$name] = $value;
    else
      $this->parameters[$name] = null;
  }

  function setBoolean($name, $value)
  {
    $this->parameters[$name] = is_null($value) ?
    null :(($value) ?  "TRUE" : "FALSE");
  }

  function setChar($name, $value)
  {
    $this->parameters[$name] = is_null($value) ?
    null : $value;
  }

  function setVarChar($name, $value)
  {
    $this->parameters[$name] = is_null($value) ?
    null : $value;
  }

  function setClob($name, $value)
  {
    $this->parameters[$name] = is_null($value) ?
    null : $value;
  }

  protected function _setDate($name, $value, $format)
  {
    if(is_int($value))
      $this->parameters[$name] = date($format, $value);
    else if(is_string($value))
      $this->parameters[$name] = date($format, strtotime($value));
    else
      $this->parameters[$name] = null;
  }

  function setDate($name, $value)
  {
    $this->_setDate($name, $value, 'Y-m-d');
  }

  function setTime($name, $value)
  {
    $this->_setDate($name, $value, 'Y-m-d H:i:s');
  }

  function setTimeStamp($name, $value)
  {
    $this->_setDate($name, $value, 'Y-m-d H:i:s');
  }

  function setBlob($name, $value)
  {
    $this->parameters[$name] = is_null($value) ?
    null : $value;
  }

  function nullDateValue()
  {
    return '00.00.0000 00:00:00';
  }
  
  function set($name, $value)
  {
    if(is_string($value))
      $this->setChar($name, $value);
    else if(is_int($value))
      $this->setInteger($name, $value);
    else if(is_bool($value))
      $this->setBoolean($name, $value);
    else if(is_float($value))
      $this->setFloat($name, $value);
    else
      $this->setNull($name);
  }

  function import($paramList)
  {
    foreach($paramList as $name=>$value)
    {
      $this->set($name, $value);
    }
  }

  function getSQL()
  {
    if (is_null($this->prepared_sql))
      return $this->sql;
    else
      return $this->prepared_sql;
  }

  function execute($sql = "")
  {
    if (!empty($sql))
    {
      $stored_sql = $this->sql;
      $this->sql = $sql;
    }
    $this->_prepareStatement();
    if ($this->prepRequired)
    {
      $this->queryId = $this->connection->executeStatement($this);
      $this->prepared_sql = null;
      if (isset($stored_sql))
      {
        $this->sql = $stored_sql;
      }
      //$res = linter_execute($this->queryId, $this->prepParams);
      //return $this->connection->pexecute($this->queryId, $this->prepParams);
    }
    else
    {
      $this->queryId = $this->connection->execute($this->getSQL());
      $this->prepared_sql = null;
      if (isset($stored_sql))
      {
        $this->sql = $stored_sql;
      }
    }
    return $this->queryId;
  }
  
  function getParams()
  {
    return $this->prepParams;
  }
  
  function setConnection($connection)
  {
    $this->connection = $connection;
  }
  
  function free()
  {
    $this->connection->closeCursor($this->queryId);    
  }
  
  protected function check_type($param)
  {
    if (is_string($param)) 
      return "'".$this->connection->escape($param)."'";
    else
      return $param;
  }
  
  protected function _prepareStatement()
  {
    $newsql = '';
    $iter = 0;
    $nulls = false;
    $sql = $this->sql;

    if (preg_match("#^select :(\w+):;?$#i", $sql, $m))
    {
      $this->prepRequired = false;
      if (isset($this->parameters[$m[1]]))
      {
        $sql = str_replace(":".$m[1].":", $this->check_type($this->parameters[$m[1]]), $sql);
        $this->prepared_sql = $sql;
        return true;
      }
      else
      {
        $this->prepared_sql = "select null;";
        return true;
      }
    }
    while(preg_match('/^(\'[^\']*?\')|(--[^(\n)]*?\n)|(:(?-U)\w+:(?U))|.+/Us', $sql, $matches))
    {
      if(isset($matches[3]))
      {
        $param = str_replace(':', '', $matches[0]);

        if(!array_key_exists($param, $this->parameters))
          $this->parameters[$param] = null;
          
        if (is_null($this->parameters[$param]))
        {
          $newsql .= 'null';
          $nulls = true;
        }
        else
        {
          $this->prepParams[] = $this->parameters[$param];
          $newsql .= ":p_$param";
          $iter++;
        }
      }
      else
        $newsql .= $matches[0];

      $sql = substr($sql, strlen($matches[0]));
    }
    
    if ($iter > 0)
      $this->prepRequired = true;
    else
      $this->prepRequired = false;
      
    $this->prepared_sql = $newsql;
      
    return true;
  }

  function addOrder($sort_params)
  {
    if(preg_match('~(?<=FROM).+\s+ORDER\s+BY\s+~i', $this->sql))
      $this->sql .= ',';
    else
      $this->sql .= ' ORDER BY ';

    foreach($sort_params as $field => $order)
      $this->sql .= $this->connection->quoteIdentifier($field) . " $order,";

    $this->sql = rtrim($this->sql, ',');
  }
  
  function addLimit($offset, $limit)
  {
        $this->sql .= ' LIMIT ' . $offset . ', ' . $limit;
  }
  
  function count()
  {
    if (!$this->queryId)
      $this->execute();
      
    if ($this->queryId == lmbLinterConnection::LINTER_EMPTY_DATASET)
      return 0;
      
    return linter_get_cursor_opt($this->queryId, CO_ROW_COUNT);
  }
    
  
  
}


