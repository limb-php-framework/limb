<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbCollectionDecorator.class.php');
lmb_require('limb/core/src/lmbClassPath.class.php');
lmb_require('limb/core/src/lmbSet.class.php');

/**
 * class lmbARRecordSetDecorator.
 *
 * @package active_record
 * @version $Id: lmbARRecordSetDecorator.class.php 6691 2008-01-15 14:55:59Z serega $
 */
class lmbARRecordSetJoinDecorator extends lmbCollectionDecorator
{
  protected $base_object;
  protected $conn;
  protected $join_relations = array();
  protected $prefix;

  function __construct($record_set, $base_object, $conn = null, $join_relations = array(), $prefix = '')
  {
    $this->base_object = $base_object;
    $this->conn = $conn;
    if(is_string($join_relations))
      $join_relations = array($join_relations => array());
    $this->join_relations = $join_relations;
    $this->prefix = $prefix;

    parent :: __construct($record_set);
  }

  function rewind()
  {
    foreach($this->join_relations as $relation_name => $params)
    {
      $relation_info = $this->base_object->getRelationInfo($relation_name);

      $object = new $relation_info['class'];
      if(isset($params['join']))
        $this->iterator = new lmbARRecordSetJoinDecorator($this->iterator, $object, $this->conn, $params['join'], $this->prefix . $relation_name . '__');
      if(isset($params['attach']))
        $this->iterator = new lmbARRecordSetAttachDecorator($this->iterator, $object, $this->conn, $params['attach'], $relation_name . '__');
    }

    parent :: rewind();
  }

  function current()
  {
    if(!$record = parent :: current())
      return null;

    $this->_extractPrefixedFieldsAsActiveRecords($record);

    return $record;
  }

  protected function _extractPrefixedFieldsAsActiveRecords($record)
  {
    foreach($this->join_relations as $relation_name => $params)
    {
      $relation_info = $this->base_object->getRelationInfo($relation_name);

      if(isset($relation_info['can_be_null']) && $relation_info['can_be_null'] && !$record->get($this->prefix . $relation_info['field']))
        return;

      $fields = new lmbSet();
      $prefix = $this->prefix . $relation_name . '__';
      
      if($record instanceof lmbActiveRecord)
        $data = $record->exportRaw();
      else
        $data = $record->export();

      foreach($data as $field => $value)
      {
        if(strpos($field, $prefix) === 0)
        {
          $non_prefixes_field_name = substr($field, strlen($prefix));
          $fields->set($non_prefixes_field_name, $value);
          $record->remove($field);
        }
      }

      $related_object = lmbARRecordSetDecorator :: createObjectFromRecord($fields, $relation_info['class'], $this->conn); 
      $record->set($this->prefix . $relation_name, $related_object);
    }
  }

  function at($pos)
  {
    if(!$record = parent :: at($pos))
      return null;

    $this->_extractPrefixedFieldsAsActiveRecords($record);

    return $record;
  }
}


