<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbCollectionDecorator.class.php');
lmb_require('limb/core/src/lmbClassPath.class.php');
lmb_require('limb/core/src/lmbSet.class.php');

/**
 * class lmbARRecordSetAttachDecorator. This class is a part of eager fetching functionality 
 *
 * @package active_record
 * @version $Id$
 */
class lmbARRecordSetAttachDecorator extends lmbCollectionDecorator
{
  protected $base_object;
  protected $conn;
  protected $attach_relations = array();

  function __construct($record_set, $base_object, $conn = null, $attach_relations = array())
  {
    $this->base_object = $base_object;
    $this->conn = $conn;
    $this->attach_relations = $attach_relations;

    parent :: __construct($record_set);
  }
  
  function rewind()
  {
    foreach($this->attach_relations as $relation_name => $params)
    {
      $relation_type = $this->base_object->getRelationType($relation_name);
      $relation_info = $this->base_object->getRelationInfo($relation_name);
      
      $relation_class = $relation_info['class'];
      $relation_object = new $relation_class(null, $this->conn);
      
      switch($relation_type)
      {
        case lmbActiveRecord :: HAS_ONE:
        case lmbActiveRecord :: MANY_BELONGS_TO:
          $ids = lmbArrayHelper :: getColumnValues($relation_info['field'], $this->iterator);
          $attached_objects = lmbActiveRecord :: findByIds($relation_class, $ids, $params, $this->conn);
          $this->loaded_attaches[$relation_name] = lmbCollection :: toFlatArray($attached_objects, 
                                                                                $key_field = $relation_object->getPrimaryKeyName(),
                                                                                $export_each = false); 
        break;
        case lmbActiveRecord :: BELONGS_TO:
          $ids = lmbArrayHelper :: getColumnValues($this->base_object->getPrimaryKeyName(), $this->iterator);
          
          $criteria = lmbSQLCriteria :: in($relation_info['field'], $ids);
          $params['criteria'] = isset($params['criteria']) ? $params['criteria']->addAnd($criteria) : $criteria;
          $attached_objects = lmbActiveRecord :: find($relation_class, $params, $this->conn);
          $this->loaded_attaches[$relation_name] = lmbCollection :: toFlatArray($attached_objects, 
                                                                                $key_field = $relation_info['field'], 
                                                                                $export_each = false); 
        break;
        case lmbActiveRecord :: HAS_MANY:
          if(isset($params['sort']))
            $params['sort'] = array($relation_info['field'] => 'ASC') + $params['sort']; 
          else
            $params['sort'] = array($relation_info['field'] => 'ASC');
            
          $query = lmbAROneToManyCollection :: createFullARQueryForRelation($relation_info, $this->conn, $params);
          
          $ids = lmbArrayHelper :: getColumnValues($this->base_object->getPrimaryKeyName(), $this->iterator);
          $query->addCriteria(lmbSQLCriteria :: in($relation_info['field'], $ids));
          
          $attached_objects = $query->fetch();
          
          foreach($attached_objects as $attached_object)
            $this->loaded_attaches[$relation_name][$attached_object->get($relation_info['field'])][] = $attached_object; 
        break;
        case lmbActiveRecord :: HAS_MANY_TO_MANY:
          if(isset($params['sort']))
            $params['sort'] = array($relation_info['field'] => 'ASC') + $params['sort']; 
          else
            $params['sort'] = array($relation_info['field'] => 'ASC');
            
          $query = lmbARManyToManyCollection :: createFullARQueryForRelation($relation_info, $this->conn, $params);
          $query->addField($relation_info['table']. '.' . $relation_info['field'], "link__id");
          
          $ids = lmbArrayHelper :: getColumnValues($this->base_object->getPrimaryKeyName(), $this->iterator);
          $query->addCriteria(lmbSQLCriteria :: in($relation_info['field'], $ids));
          
          $attached_objects = $query->fetch();
          
          foreach($attached_objects as $attached_object)
            $this->loaded_attaches[$relation_name][$attached_object->get("link__id")][] = $attached_object; 
        break;
      }
    }
    
    parent :: rewind();
  }
  
  function current()
  {
    $record = parent :: current();

    foreach($this->attach_relations as $relation_name => $params)
    {
      $relation_type = $this->base_object->getRelationType($relation_name);
      $relation_info = $this->base_object->getRelationInfo($relation_name);
      
      switch($relation_type)
      {
        case lmbActiveRecord :: HAS_ONE:
        case lmbActiveRecord :: MANY_BELONGS_TO:
          $record->set($relation_name, $this->loaded_attaches[$relation_name][$record->get($relation_info['field'])]);
        break;
        case lmbActiveRecord :: BELONGS_TO:
        case lmbActiveRecord :: HAS_MANY:
        case lmbActiveRecord :: HAS_MANY_TO_MANY:
          $record->set($relation_name, $this->loaded_attaches[$relation_name][$record->get($this->base_object->getPrimaryKeyName())]);
        break;
      }
    }
    return $record;
  }

  function at($pos)
  {
    throw new lmbException('at() is not implemented in lmbARRecordSetAttachDecorator. Please consider using getArray() instead');
  }
}


