<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMysqlStatement.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/drivers/lmbDbStatement.interface.php');

class lmbMysqlStatement implements lmbDbStatement
{
  protected $statement;
  protected $connection;
  protected $parameters = array();

  function __construct($connection, $sql)
  {
    $this->statement = $sql;
    $this->connection = $connection;
  }

  function setNull($name)
  {
    $this->parameters[$name] = 'null';
  }

  function setSmallInt($name, $value)
  {
    $this->parameters[$name] = is_null($value) ?  'null' : intval($value);
  }

  function setInteger($name, $value)
  {
    $this->parameters[$name] = is_null($value) ?  'null' : intval($value);
  }

  function setFloat($name, $value)
  {
    $this->parameters[$name] = is_null($value) ?
    'null' :
    floatval($value);
  }

  function setDouble($name, $value)
  {
    if(is_float($value) || is_integer($value))
    {
      $this->parameters[$name] = $value;
    }
    else if(is_string($value) && preg_match('/^(|-)\d+(|.\d+)$/', $value))
    {
      $this->parameters[$name] = $value;
    }
    else
    {
      $this->parameters[$name] = 'null';
    }
  }

  function setDecimal($name, $value)
  {
    if(is_float($value) || is_integer($value))
    {
      $this->parameters[$name] = $value;
    }
    else if(is_string($value) && preg_match('/^(|-)\d+(|.\d+)$/', $value))
    {
      $this->parameters[$name] = $value;
    }
    else
    {
      $this->parameters[$name] = 'null';
    }
  }

  function setBoolean($name, $value)
  {
    $this->parameters[$name] = is_null($value) ?
    'null' :(($value) ?  '1' : '0');
  }

  function setChar($name, $value)
  {
    $this->parameters[$name] = is_null($value) ?
    'null' :
    "'" . mysql_escape_string((string) $value) . "'";
  }

  function setVarChar($name, $value)
  {
    $this->parameters[$name] = is_null($value) ?
    'null' :
    "'" . mysql_escape_string((string) $value) . "'";
  }

  function setClob($name, $value)
  {
    $this->parameters[$name] = is_null($value) ?
    'null' :
    "'" . mysql_escape_string((string) $value) . "'";
  }

  protected function _setDate($name, $value, $format)
  {
    if(is_int($value))
    {
      $this->parameters[$name] = "'" . date($format, $value) . "'";
    }
    else if(is_string($value))
    {
      $this->parameters[$name] = "'" . mysql_escape_string((string) $value) . "'";
    }
    else
    {
      $this->parameters[$name] = 'null';
    }
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
    {
      $this->setChar($name, $value);
    }
    else if(is_int($value))
    {
      $this->setInteger($name, $value);
    }
    else if(is_bool($value))
    {
      $this->setBoolean($name, $value);
    }
    else if(is_float($value))
    {
      $this->setFloat($name, $value);
    }
    else
    {
      $this->setNull($name);
    }
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
    $sql = $this->statement;
    foreach($this->parameters as $key => $value)
    {
      $sql = str_replace(':' . $key . ':', $value, $sql);
    }
    return $sql;
  }

  function execute()
  {
    return (Boolean) $this->connection->execute($this->getSQL());
  }
}

?>
