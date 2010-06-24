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
 * class lmbSqliteStatement.
 *
 * @package dbal
 * @version $Id$
 */
class lmbSqliteStatement implements lmbDbStatement
{
  protected $statement;
  protected $connection;
  protected $parameters = array();

  function __construct($connection, $sql)
  {
    $this->statement = $sql;
    $this->connection = $connection;
  }

  function setConnection($connection)
  {
    $this->connection = $connection;
  }

  function setNull($name)
  {
    $this->parameters[$name] = 'null';
  }

  function setBit($name, $value)
  {
    $this->parameters[$name] = decbin($value);
  }

  function setSmallInt($name, $value)
  {
    if($value && !is_numeric($value))
      throw new lmbDbException("Can't convert given value to the small int", array('value' => $value));
    $this->parameters[$name] = is_null($value) ?  'null' : intval($value);
  }

  function setInteger($name, $value)
  {
    if($value && !is_numeric($value))
      throw new lmbDbException("Can't convert given value to the integer", array('value' => $value));
    $this->parameters[$name] = is_null($value) ?  'null' : intval($value);
  }

  function setFloat($name, $value)
  {
    if($value && !is_numeric($value))
      throw new lmbDbException("Can't convert given value to the float", array('value' => $value));
    $this->parameters[$name] = is_null($value) ? 'null' : floatval($value);
  }

  function setDouble($name, $value)
  {
    if(is_float($value) || is_integer($value))
      $this->parameters[$name] = $value;
    else if(is_string($value) && preg_match('/^(|-)\d+(|.\d+)$/', $value))
      $this->parameters[$name] = $value;
    else if(!$value)
      $this->parameters[$name] = 'null';
    else
      throw new lmbDbException("Can't convert given value to the double", array('value' => $value));
  }

  function setDecimal($name, $value)
  {
    if(is_float($value) || is_integer($value))
      $this->parameters[$name] = $value;
    else if(is_string($value) && preg_match('/^(|-)\d+(|.\d+)$/', $value))
      $this->parameters[$name] = $value;
    else if(!$value)
      $this->parameters[$name] = 'null';
    else
      throw new lmbDbException("Can't convert given value to the decimal", array('value' => $value));
  }

  function setBoolean($name, $value)
  {
    $this->parameters[$name] = is_null($value) ? 'null' :(($value) ?  '1' : '0');
  }

  function setChar($name, $value)
  {
    $this->parameters[$name] = is_null($value) ? 'null' : "'" . $this->_escape($value) . "'";
  }

  function setVarChar($name, $value)
  {
    $this->parameters[$name] = is_null($value) ? 'null' :  "'" . $this->_escape($value) . "'";
  }

  function setClob($name, $value)
  {
    $this->parameters[$name] = is_null($value) ? 'null' : "'" . $this->_escape($value) . "'";
  }

  protected function _setDate($name, $value, $format)
  {
    if(is_int($value))
      $this->parameters[$name] = "'" . date($format, $value) . "'";
    else if(is_string($value))
      $this->parameters[$name] = "'" . $this->_escape($value) . "'";
    else if(!$value)
      $this->parameters[$name] = 'null';
    else
      throw new lmbDbException("Can't convert given value to the date", array('value' => $value));
  }

  function setDate($name, $value)
  {
    $this->_setDate($name, $value, 'Y-m-d');
  }

  function setTime($name, $value)
  {
    $this->_setDate($name, $value, 'H:i:s');
  }

  function setTimeStamp($name, $value)
  {
    $this->_setDate($name, $value, 'Y-m-d H:i:s');
  }

  function setBlob($name, $value)
  {
    $this->setChar($name, $value);
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
      $this->set($name, $value);
  }

  function getSQL()
  {
    $sql = $this->statement;
    foreach($this->parameters as $key => $value)
    {
      $sql = str_replace(':' . $key . ':', $value, $sql);
    }
    return $sql;
  }

  function execute()
  {
    return $this->connection->executeStatement($this);
  }

  protected function _escape($value)
  {
    return sqlite_escape_string((string) $value);
  }
}


