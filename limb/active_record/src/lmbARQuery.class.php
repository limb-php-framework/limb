<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/query/lmbSelectRawQuery.class.php');
lmb_require('limb/active_record/src/lmbARRecordSetAttachDecorator.class.php');
lmb_require('limb/active_record/src/lmbARRecordSetJoinDecorator.class.php');

class lmbARQuery extends lmbSelectRawQuery
{
  protected $base_class_name;
  protected $base_object;
  protected $join_relations = array();
  protected $attach_relations = array();
  protected $sort_params = array();
  
  function __construct($base_class_name, $conn, $sql = '')
  {
    $this->base_class_name = $base_class_name;
    $this->base_object = new $this->base_class_name(null, $conn);

    if(!$sql)
    {
      parent :: __construct($conn);
      $this->addTable($this->base_object->getTableName());
      $this->_addFieldsForObject($this->base_object);
    }
    else
    {
      parent :: __construct($sql, $conn);
    }
  }
  
  function eagerJoin($relation_name, $params = array())
  {
    $this->join_relations[$relation_name] = $params;
    return $this;
  }

  //should be removed before release
  function joinRelation($relation_name, $params = array())
  {
    return $this->eagerJoin($relation_name, $params);
  }
  
  function eagerAttach($relation_name, $params = array())
  {
    $this->attach_relations[$relation_name] = $params;
    return $this;
  }
  
  //should be removed before release
  function attachRelation($relation_name, $params = array())
  {
    return $this->eagerAttach($relation_name, $params);
  }
  
  protected function _addFieldsForObject($object, $table_name = '', $prefix = '')
  {
    $fields = $object->getDbTable()->getColumnsForSelect($table_name, $object->getLazyAttributes(), $prefix);
    foreach($fields as $field => $alias)
      $this->addField($field, $alias);
  }
  
  function addOrder($field, $type='ASC')
  {
    if(is_array($field))
      $this->sort_params = $this->sort_params + $field;
    else
      $this->sort_params[$field] = $type;
  }
  
  function fetch($decorate = true)
  {
    $this->_applyJoins($this->base_object, $this->join_relations);
    
    $rs = parent :: fetch();

    if($decorate)
      $rs = new lmbARRecordSetDecorator($rs, $this->base_class_name, $this->_conn);
    
    $rs = $this->_decorateWithJoinDecorator($rs);
    
    $rs =  $this->_decorateWithAttachDecorator($rs);
    
    $rs->sort($this->sort_params);
    
    return $rs;
  }
  
  protected function _applyJoins($base_object, $joins, $parent_relation_name = '')
  {
    if(is_string($joins))
      $joins = array($joins => array());
    
    if($parent_relation_name)
      $prefix = $parent_relation_name . '__';
    else
    {
      $parent_relation_name = $base_object->getTableName();
      $prefix = '';
    }
    
    foreach($joins as $relation_name => $params)
    {
      $relation_info = $base_object->getRelationInfo($relation_name);
      
      if(!$relation_info || !isset($relation_info['class']))
        throw new lmbException('Relation info "' . $relation_name .'" not found in "' . get_class($base_object) . '" or does not contain "class" property');
      
      $class_name = $relation_info['class'];
      $object = new $class_name(null, $this->_conn);
      $this->_addFieldsForObject($object, $prefix . $relation_name, $prefix . $relation_name . '__');
      
      $relation_type = $base_object->getRelationType($relation_name);
      switch($relation_type)
      {
        case lmbActiveRecord :: HAS_ONE:
        case lmbActiveRecord :: MANY_BELONGS_TO:
          $this->addLeftJoin($object->getTableName(), 
                             $object->getPrimaryKeyName(),
                             $parent_relation_name, 
                             $relation_info['field'],
                             $prefix . $relation_name);
        break;
        case lmbActiveRecord :: BELONGS_TO:
          $this->addLeftJoin($object->getTableName(), 
                             $relation_info['field'],
                             $parent_relation_name, 
                             $base_object->getPrimaryKeyName(),
                             $prefix . $relation_name);
        break;
        default:
           throw new lmbARException('"' . $relation_name . '" has a wrong relation type for JOIN operation');
        break;
      }
      
      if(isset($params['join']))
        $this->_applyJoins($object, $params['join'], $prefix . $relation_name);
    }
  }

  protected function _decorateWithJoinDecorator($rs)
  {
    if(count($this->join_relations))
      return new lmbARRecordSetJoinDecorator($rs, $this->base_object, $this->_conn, $this->join_relations);
    else
      return $rs;
  }
  
  protected function _decorateWithAttachDecorator($rs)
  {
    if(count($this->attach_relations))
      return new lmbARRecordSetAttachDecorator($rs, $this->base_object, $this->_conn, $this->attach_relations);
    else
      return $rs;
  }
  
  static function create($class_name, $params = array(), $conn = null, $sql = '')
  {
    if(!$conn)
      $conn = lmbToolkit :: instance()->getDefaultDbConnection();
    
    $object = new $class_name;
    $query = new lmbARQuery($class_name, $conn, $sql);

    if(isset($params['criteria']) && $params['criteria'])
      $criteria = lmbSQLCriteria :: objectify($params['criteria']); 
    else
      $criteria = lmbSQLCriteria :: create();

    $has_class_criteria = false;
    if(isset($params['class']))
    {
      $filter_object = new $params['class'];
      $criteria = $filter_object->addClassCriteria($criteria);
      $has_class_criteria = true;
    }

    if(!$has_class_criteria)
      $object->addClassCriteria($criteria);
    
    $query->where($criteria);
    
    $sort_params = (isset($params['sort']) && $params['sort']) ? $params['sort'] : $object->getDefaultSortParams();
    $query->order($sort_params);

    $join = (isset($params['join']) && $params['join']) ? $params['join'] : array();
    if(!is_array($join))
      $join = explode(',', $join);
    
    foreach($join as $relation_name=> $params_or_relation_name)
    {
      if(is_numeric($relation_name))
        $query->eagerJoin(trim($params_or_relation_name));
      else
        $query->eagerJoin(trim($relation_name), $params_or_relation_name);
    }

    $attach = (isset($params['attach']) && $params['attach']) ? $params['attach'] : array();
    if(!is_array($attach))
      $attach = explode(',', $attach);
    
    foreach($attach as $relation_name => $params_or_relation_name)
    {
      if(is_numeric($relation_name))
        $query->eagerAttach(trim($params_or_relation_name));
      else
        $query->eagerAttach(trim($relation_name), $params_or_relation_name);
    }
    
    return $query;
  }
}
