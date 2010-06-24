<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbARMetaInfo.
 *
 * @package active_record
 * @version $Id: lmbARMetaInfo.class.php 7783 2009-03-17 18:57:39Z pachanga $
 */
class lmbARMetaInfo
{
  protected $db_table = null;
  protected $db_column_names = array();
  protected $get_method_fields = array();
  protected $set_method_fields = array();
  protected $cast_methods = array();

  function __construct($active_record, $conn = null)
  {
    $table_name = $active_record->getTableName();
    $this->db_table = lmbToolkit :: instance()->createTableGateway($table_name, $conn);
    $this->db_column_names = $this->db_table->getColumnNames();
  }

  function getDbTable()
  {
    return $this->db_table;
  }

  function getDbColumnsNames()
  {
    return $this->db_column_names;
  }

  function hasColumn($name)
  {
    return isset($this->db_column_names[$name]);
  }

  function castDbValues($record)
  {
    $this->_loadCastMethods($record);

    $result = array();
    foreach($record->export() as $key => $value)
    {
      if(isset($this->cast_methods[$key]))
      {
        $method = $this->cast_methods[$key];
        if(method_exists($record, $method))
          $result[$key] = $record->$method($key);
        else
          $result[$key] = $value;
      }
      else
        $result[$key] = $value;
    }

    return $result;
  }

  protected function _loadCastMethods($record)
  {
    if(sizeof($this->cast_methods))
      return;

    static $accessors;

    if(!isset($accessors))
    {
      $typeinfo = new lmbDbTypeInfo();
      $accessors = $typeinfo->getColumnTypeGetters();
    }

    foreach($this->db_column_names as $name)
    {
      if($info = $this->db_table->getColumnInfo($name))
        $this->cast_methods[$name] = $accessors[$info->getType()];
      else
        $this->cast_methods[$name] = '';
    }
  }
}


