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
 * class lmbOciStatement.
 *
 * @package dbal
 * @version $Id: lmbOciStatement.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbOciStatement implements lmbDbStatement
{
  protected $sql;
  protected $statement = null;
  protected $connection;
  protected $modified = false;
  protected $parameters = array();

  function __construct($connection, $sql)
  {
    $this->sql = $sql;
    $this->connection = $connection;
  }
  
  function setConnection($connection)
  {
    $this->connection = $connection;
  }

  function set($name, $value)
  {
    $this->parameters[$name] = $value;
    $this->modified = true;
  }

  function import($paramList)
  {
    foreach($paramList as $name=>$value)
      $this->set($name, $value);
  }

  function getSQL()
  {
    return $this->sql;
  }

  function getStatement()
  {
    if(!$this->statement || $this->hasChanged)
      $this->_prepareStatement();
    return $this->statement;
  }

  protected function _prepareStatement()
  {
    $this->statement = oci_parse($this->connection->getConnectionId(),
                                 $this->_handleBindVars($this->sql));

    if(!$this->statement)
    {
      $this->connection->_raiseError();
      return;
    }

    foreach(array_keys($this->parameters) as $name)
    {
      if(!oci_bind_by_name($this->statement, ':p_' . $name, $this->parameters[$name], -1))
        $this->connection->_raiseError($this->statement);
    }

    $this->hasChanged = false;
  }

  protected function _handleBindVars($sql)
  {
    $newsql = '';
    // Regex searches for bind vars in an SQL string
    // It ignores ':this:' (quotes), --:this: (one type of Oracle comment)
    // Needs to support /* this style of comment */
    // It's also pretty inefficient as matches "uninteresting" SQL character
    // by character. Need a "real" parser?
    while(preg_match('/^(\'[^\']*?\')|(--[^(\n)]*?\n)|(:(?-U)\w+:(?U))|.+/Us', $sql, $matches))
    {
      if(isset($matches[3]))
      {
        $param = str_replace(':', '', $matches[0]);

        if(!array_key_exists($param, $this->parameters))
          $this->parameters[$param] = null;

        $newsql .= ":p_$param";
      }
      else
        $newsql .= $matches[0];

      $sql = substr($sql, strlen($matches[0]));
    }
    return $newsql;
  }

  function execute()
  {
    return $this->connection->executeStatement($this);
  }

  function free()
  {
    if($this->statement)
    {
      oci_free_statement($this->statement);
      $this->statement = null;
    }
  }

  function setNull($name)
  {
    $this->parameters[$name] = null;
    $this->hasChanged = true;
  }

  function setSmallInt($name, $value)
  {
    $this->parameters[$name] = is_null($value) ? null : intval($value);
    $this->hasChanged = true;
  }

  function setInteger($name, $value)
  {
    $this->parameters[$name] = is_null($value) ? null : intval($value);
    $this->hasChanged = true;
  }

  function setFloat($name, $value)
  {
    $this->parameters[$name] = is_null($value) ? null : floatval($value);
    $this->hasChanged = true;
  }

  function setDouble($name, $value)
  {
    if(is_float($value) || is_integer($value))
      $this->parameters[$name] = $value;
    else if(is_string($value) && preg_match('/^(|-)\d+(|.\d+)$/', $value))
      $this->parameters[$name] = $value;
    else
      $this->parameters[$name] = null;
    $this->hasChanged = true;
  }

  function setDecimal($name, $value)
  {
    if(is_float($value) || is_integer($value))
      $this->parameters[$name] = $value;
    else if(is_string($value) && preg_match('/^(|-)\d+(|.\d+)$/', $value))
      $this->parameters[$name] = $value;
    else
      $this->parameters[$name] = null;
    $this->hasChanged = true;
  }

  function setBoolean($name, $value)
  {
    $this->parameters[$name] = is_null($value) ? null : (($value) ? 1 : 0);
    $this->hasChanged = true;
  }

  function setChar($name, $value)
  {
    $this->parameters[$name] = is_null($value) ? null : $value;
    $this->hasChanged = true;
  }

  function setVarChar($name, $value)
  {
    $this->parameters[$name] = is_null($value) ? null : $value;
    $this->hasChanged = true;
  }

  function setDate($name, $value)
  {
    $this->parameters[$name] = is_null($value) ? null : $value;
    $this->hasChanged = true;
  }

  function setTime($name, $value)
  {
    throw new lmbDbException(__METHOD__ . ' not implemented');
  }

  function setTimeStamp($name, $value)
  {
    $this->parameters[$name] = is_null($value) ? null : intval($value);
    $this->hasChanged = true;
  }

  function setBlob($name, $value)
  {
    throw new lmbDbException(__METHOD__ . ' not implemented');
  }

  function setClob($name, $value)
  {
    throw new lmbDbException(__METHOD__ . ' not implemented');
  }
}


