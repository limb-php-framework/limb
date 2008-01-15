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

class lmbARQuery extends lmbSelectRawQuery
{
  protected $_base_class_name;
  protected $_base_object;
  protected $_with = array();
  protected $_attach = array();
  
  function __construct($base_class_name, $conn, $sql = '')
  {
    $this->_base_class_name = $base_class_name;
    $this->_base_object = new $this->_base_class_name(null, $conn);

    if(!$sql)
    {
      parent :: __construct($conn);
      $this->addTable($this->_base_object->getTableName());
      $this->_addFieldsForObject($this->_base_object);
    }
    else
    {
      parent :: __construct($sql, $conn);
    }
  }
  
  function with($relation_name)
  {
    $relation_info = $this->_base_object->getRelationInfo($relation_name);
    $this->_with[$relation_name] = $relation_info;
    
    if(!$relation_info || !isset($relation_info['class']))
      throw new lmbException('Relation info "' . $relation_name .'" not found in "' . $this->_base_class_name . '" or does not contain "class" property');
    
    $class_name = $relation_info['class'];
    $object = new $class_name(null, $this->_conn);
    $this->_addFieldsForObject($object, $relation_name, $prefix = $relation_name . '__');
    
    $relation_type = $this->_base_object->getRelationType($relation_name);
    switch($relation_type)
    {
      case lmbActiveRecord :: HAS_ONE:
      case lmbActiveRecord :: MANY_BELONGS_TO:
        $this->addLeftJoin($object->getTableName(), 
                           $object->getPrimaryKeyName(),
                           $this->_base_object->getTableName(), 
                           $relation_info['field'],
                           $relation_name);
      break;
      case lmbActiveRecord :: BELONGS_TO:
        $this->addLeftJoin($object->getTableName(), 
                           $relation_info['field'],
                           $this->_base_object->getTableName(), 
                           $this->_base_object->getPrimaryKeyName(),
                           $relation_name);
      break;
    }
    return $this;
  }
  
  function attach($relation_name, $params = array())
  {
    $this->_attach[$relation_name] = $params;
    return $this;
  }
  
  protected function _addFieldsForObject($object, $table_name = '', $prefix = '')
  {
    $fields = $object->getDbTable()->getColumnsForSelect($table_name, $object->getLazyAttributes(), $prefix);
    foreach($fields as $field => $alias)
      $this->addField($field, $alias);
  }
  
  function fetch($decorate = true)
  {
    $rs = parent :: fetch();
    $rs = $this->_decorateWithAttachDecorator($rs);
    
    if($decorate)
      return new lmbARRecordSetDecorator($rs, $this->_base_class_name, $this->_conn, $this->_with);
    else
      return $rs;
  }
  
  protected function _decorateWithAttachDecorator($rs)
  {
    if(count($this->_attach))
      return new lmbARRecordSetAttachDecorator($rs, $this->_base_object, $this->_conn, $this->_attach);
    else
      return $rs;
  }
}
