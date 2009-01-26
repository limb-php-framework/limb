<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/drivers/lmbDbManipulationStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbOciStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbOciClob.class.php');
lmb_require(dirname(__FILE__) . '/lmbOciBlob.class.php');

/**
 * class lmbOciManipulationStatement.
 *
 * @package dbal
 * @version $Id: lmbOciManipulationStatement.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbOciManipulationStatement extends lmbOciStatement implements lmbDbManipulationStatement
{
  protected $queryId;
  protected $lobs = array();
  protected $lobDescriptors = array();

  function execute()
  {
    if(!$this->lobs)
    {
      $this->queryId = parent :: execute();
      return $this->queryId;
    }

    $this->connection->beginTransaction();

    $this->queryId = parent :: execute();

    if($this->_saveLobs())
      $this->connection->commitTransaction();
    else
      $this->connection->rollbackTransaction();

    $this->_freeLobs();

    return $this->queryId;
  }

  protected function _saveLobs()
  {
    $result = true;
    foreach($this->lobDescriptors as $name => $descriptor)
    {
      if(!$descriptor->save($this->lobs[$name]->read()))
      {
        $result = false;
        break;
      }
    }
    return $result;
  }

  protected function _freeLobs()
  {
    foreach($this->lobDescriptors as $name => $descriptor)
      $descriptor->free();

    $this->lobs = array();
    $this->lobDescriptors = array();
  }

  function setClob($name, $value)
  {
    $this->lobs[$name] = new lmbOciClob($value);
    $this->hasChanged = true;
  }

  function setBlob($name, $value)
  {
    $this->lobs[$name] = new lmbOciBlob($value);
    $this->hasChanged = true;
  }

  function getAffectedRowCount()
  {
    if(is_resource($this->queryId))
      return oci_num_rows($this->queryId);
  }

  protected function _handleBindVars($sql)
  {
    return $this->_handleBindedLobs(parent :: _handleBindVars($sql));
  }

  protected function _handleBindedLobs($sql)
  {
    if(!$this->lobs)
      return $sql;

    $holder_to_field_map = array();

    foreach($this->lobs as $name => $lob)
    {
      if(array_key_exists($name, $this->parameters))
        unset($this->parameters[$name]);

      if(strpos($sql, ":p_$name") !== false)
        $holder_to_field_map[$name] = $this->_mapHolderToField($name, $sql);
    }

    $sql .= " RETURNING ";

    foreach($this->lobs as $name => $lob)
    {
      $sql = str_replace(":p_$name", $lob->getEmptyExpression(), $sql);
      $this->lobDescriptors[$name] = oci_new_descriptor($this->connection->getConnectionId(),
                                                        $lob->getDescriptorType());

      $sql .= "{$holder_to_field_map[$name]},";
    }

    $sql = rtrim($sql, ',');
    $sql .= " INTO ";

    foreach(array_keys($this->lobs) as $name)
      $sql .= ":p_$name,";

    return rtrim($sql, ',');
  }

  protected function _mapHolderToField($name, $sql)
  {
    return $name;
  }

  protected function _prepareStatement()
  {
    parent :: _prepareStatement();

    foreach(array_keys($this->lobDescriptors) as $name)
    {
      if(!oci_bind_by_name($this->statement,
                           ':p_' . $name,
                           $this->lobDescriptors[$name],
                           -1,
                           $this->lobs[$name]->getNativeType()))
        $this->connection->_raiseError($this->statement);
    }
  }
}


