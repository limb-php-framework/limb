<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbObject.class.php');
lmb_require('limb/core/src/lmbDelegate.class.php');
lmb_require('limb/core/src/lmbCollection.class.php');
lmb_require('limb/dbal/src/lmbTableGateway.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLCriteria.class.php');
lmb_require('limb/dbal/src/drivers/lmbDbTypeInfo.class.php');
lmb_require('limb/dbal/toolkit.inc.php');
lmb_require('limb/validation/src/lmbValidator.class.php');
lmb_require('limb/validation/src/lmbErrorList.class.php');
lmb_require('limb/validation/src/exception/lmbValidationException.class.php');
lmb_require('limb/active_record/src/lmbARException.class.php');
lmb_require('limb/active_record/src/lmbARNotFoundException.class.php');
lmb_require('limb/active_record/src/lmbAROneToManyCollection.class.php');
lmb_require('limb/active_record/src/lmbARManyToManyCollection.class.php');
lmb_require('limb/active_record/src/lmbARQuery.class.php');
lmb_require('limb/active_record/src/lmbARRecordSetDecorator.class.php');
lmb_require('limb/active_record/src/lmbARMetaInfo.class.php');

/**
 * Base class responsible for ActiveRecord design pattern implementation. Inspired by Rails ActiveRecord class.
 *
 * @version $Id: lmbActiveRecord.class.php 8108 2010-01-28 00:46:47Z korchasa $
 * @package active_record
 */
class lmbActiveRecord extends lmbObject
{
  /**
   * @var string database column name used to store object class name for single table inheritance
   */
  protected static $_inheritance_field = 'kind';
  /**
   * @var string database column name used to store object's create time
   */
  protected $_ctime_field = 'ctime';
  /**
   * @var string database column name used to store object's update time
   */
  protected $_utime_field = 'utime';
  /**
   * @var array global event listeners which receieve events from ALL lmbActiveRecord instances
   */
  protected static $_global_listeners = array();
  /**
   * @var object database connection which is shared by all lmbActiveRecord instances
   *             if no connection passed explicitly into constructor
   */
  protected static $_default_db_conn;

  protected static $_metas = array();

  /**
   * @var lmbDbConnection object current object's database connection
   * @see lmbDbConnection
   */
  protected $_db_conn;

  /**
   * @var string current database connection dsn
   */
  protected $_db_conn_dsn;

  /**
   * @var object lmbTableGateway instance used to access underlying db table
   */
  protected $_db_table;
  /**
   * @var string name of the primary key
   */
  protected $_primary_key_name = 'id';
  /**
   * @var string name of class database table to store instance fields, if not set lmbActiveRecord tries to guess it
   */
  protected $_db_table_name;
  /**
   * @var array list of database table fields
   */
  protected $_db_table_fields = array();
  /**
   * @var boolean reflects new or loaded status of an object
   */
  protected $_is_new = true;
  /**
   * @var object error list instance used to store validation errors
   */
  protected $_error_list;
  /**
   * @var array all has-one relations of an object
   */
  protected $_has_one = array();
  /**
   * @var array all belongs-to relations of an object
   */
  protected $_belongs_to = array();
  /**
   * @var array all many-belongs-to relations of an object
   */
  protected $_many_belongs_to = array();
  /**
   * @var array all has-many relations of an object
   */
  protected $_has_many = array();
  /**
   * @var array all has-many-to-many relations of an object
   */
  protected $_has_many_to_many = array();
  /**
   * @var array all value object relations of an object
   */
  protected $_composed_of = array();
  /**
   * @var array all relations of an object
   */
  protected $_relations = array();
  /**
   * @var array all relations of an object to any other object. Like "has one", "many belongs to", "belongs_to".
   */
  protected $_single_object_relations = array();
  /**
   * @var boolean true during the object's saving procedure
   */
  protected $_is_being_saved = false;
  /**
   * @var boolean true during the object's removal procedure
   */
  protected $_is_being_destroyed = false;
  /**
   * @var boolean object's dirtiness status
   */
  protected $_is_dirty = false;
  /**
   * @var boolean we can explicitly mark object inheritable or not, if not set lmbActiveRecord looks if inheritance field is present in db
   */
  protected $_is_inheritable;
  /**
   * @var array array of attributes which should not be loaded at once but only on demand
   */
  protected $_lazy_attributes = array();
  /**
   * @var array array of dirty(changed) attributes of an object
   */
  protected $_dirty_props = array();
  /**
   * @var array sort params used to order objects during database retrieval
   */
  protected $_default_sort_params = array();
  /**
   * @var object database metainfo object
   */
  protected $_db_meta_info;

  /**#@+
   * Event type constants
   */
  const ON_BEFORE_SAVE             = 1;
  const ON_AFTER_SAVE              = 2;
  const ON_BEFORE_UPDATE           = 3;
  const ON_UPDATE                  = 4;
  const ON_AFTER_UPDATE            = 5;
  const ON_BEFORE_CREATE           = 6;
  const ON_CREATE                  = 7;
  const ON_AFTER_CREATE            = 8;
  const ON_BEFORE_DESTROY          = 9;
  const ON_AFTER_DESTROY           = 10;
  /**#@-*/

  /**#@+
   * Relation type constants
   */
  const HAS_ONE             = 1;
  const HAS_MANY            = 2;
  const HAS_MANY_TO_MANY    = 3;
  const BELONGS_TO          = 4;
  const MANY_BELONGS_TO     = 5;
  /**#@-*/

  /**
   * @var array event listeners attached to the concrete object instance
   */
  protected $_listeners = array();

  /**
   *  Constructor.
   *  Creates an instance of lmbActiveRecord object in different ways depending on passed argument
   *  <code>
   *  //plain vanilla instance
   *  $b = new Book();
   *  //fills instance with passed properties
   *  $b = new Book(array('title' => 'Alice in Wonderland'));
   *  //tries to load instance from database using 1 as a primary key identifier
   *  $b = new Book(1);
   *  </code>
   *  @param array|integer Depending on argument type the new object is filled with properties or loaded from database
   */
  function __construct($magic_params = null, $conn = null)
  {
    if(is_object($conn))
      $this->_db_conn = $conn;
    else
      $this->_db_conn = self :: getDefaultConnection();

    $this->_db_conn_dsn = $this->_db_conn->getDsnString();

    $class_name = get_class($this);

    if(!$this->_db_table_name)
      $this->_db_table_name = lmb_under_scores($class_name);

    if(isset(self :: $_metas[$class_name]))
      $meta = self :: $_metas[$class_name];
    else
    {
      $meta = new lmbARMetaInfo($this, $this->_db_conn);
      self :: $_metas[$class_name] = $meta;
    }
    $this->_db_meta_info = $meta;

    $this->_db_table_fields = $this->_db_meta_info->getDbColumnsNames();

    $this->_db_table = $this->_db_meta_info->getDbTable();
    $this->_db_table->setConnection($this->_db_conn);
    $this->_db_table->setPrimaryKeyName($this->_primary_key_name);
    $this->_error_list = new lmbErrorList();

    $this->_defineRelations();

    // For optimization purposes
    $this->_relations = $this->_getAllRelations();
    $this->_single_object_relations = $this->_getSingleObjectRelations();

    if($magic_params)
    {
      if(is_int($magic_params))
        $this->loadById($magic_params);
      elseif(is_array($magic_params) || is_object($magic_params))
        $this->import($magic_params);
    }
  }
  /**
   *  Sets database resource identifier used for database access
   *  @param string DSN, e.g. mysql://root:secret@localhost/mydb
   */
  static function setDefaultDSN($dsn)
  {
    self :: $_default_db_conn = lmbToolkit :: instance()->createDbConnection($dsn);
  }
  /**
   *  Sets default database connection object
   *  @param object instance of concrete lmbDbConnection interface implementation
   *  @return object previous connection object
   *  @see lmbDbConnection
   */
  static function setDefaultConnection($conn)
  {
    $prev = self :: $_default_db_conn;
    self :: $_default_db_conn = $conn;
    return $prev;
  }
  /**
   *  Returns current default database connection object
   *  @return object instance of concrete lmbDbConnection interface implementation
   *  @see lmbDbConnection
   */
  static function getDefaultConnection()
  {
    if(is_object(self :: $_default_db_conn))
      return self :: $_default_db_conn;
    return lmbToolkit :: instance()->getDefaultDbConnection();
  }
  /**
   *  Returns current single table inheritance column name
   *  @return string
   */
  static function getInheritanceField()
  {
    return self :: $_inheritance_field;
  }
  /**
   *  Allows to override default single table inheritance column name
   *  @param string
   */
  static function setInheritanceField($field)
  {
    return self :: $_inheritance_field = $field;
  }
  /**
   *  Returns name of database table
   *  @return string
   */
  function getTableName()
  {
    return $this->_db_table_name;
  }
  /**
   *  Returns primary key name of the database table
   *  @return string
   */
  function getPrimaryKeyName()
  {
    return $this->_primary_key_name;
  }
  /**
   *  Returns table gateway instance used for all db interactions
   *  @return object
   */
  function getDbTable()
  {
    return $this->_db_table;
  }
  /**
   *  Returns error list object with all validation errors
   *  @return object
   */
  function getErrorList()
  {
    return $this->_error_list;
  }

  function setErrorList($error_list)
  {
    $this->_error_list = $error_list;
  }

  protected function _defineRelations(){}

  protected function _hasOne($relation_name, $info)
  {
    $this->_has_one[$relation_name] = $info;
  }

  protected function _hasMany($relation_name, $info)
  {
    $this->_has_many[$relation_name] = $info;
  }

  protected function _hasManyToMany($relation_name, $info)
  {
    $this->_has_many_to_many[$relation_name] = $info;
  }

  protected function _belongsTo($relation_name, $info)
  {
    $this->_belongs_to[$relation_name] = $info;
  }

  protected function _manyBelongsTo($relation_name, $info)
  {
    $this->_many_belongs_to[$relation_name] = $info;
  }

  protected function _composedOf($relation_name, $info)
  {
    $this->_composed_of[$relation_name] = $info;
  }

  /**
   *  Returns relation info array defined during class declaration
   *  @return array
   */
  function getRelationInfo($relation)
  {
    if(isset($this->_relations[$relation]))
      return $this->_relations[$relation];
  }

  function getRelationType($relation)
  {
    if(isset($this->_has_one[$relation]))
      return self :: HAS_ONE;

    if(isset($this->_has_many[$relation]))
      return self :: HAS_MANY;

    if(isset($this->_has_many_to_many[$relation]))
      return self :: HAS_MANY_TO_MANY;

    if(isset($this->_belongs_to[$relation]))
      return self :: BELONGS_TO;

    if(isset($this->_many_belongs_to[$relation]))
      return self :: MANY_BELONGS_TO;
  }

  protected function _getAllRelations()
  {
     return array_merge($this->_has_one,
                        $this->_has_many,
                        $this->_has_many_to_many,
                        $this->_belongs_to,
                        $this->_many_belongs_to,
                        $this->_composed_of);
  }

  protected function _getSingleObjectRelations()
  {
     return array_merge($this->_has_one,
                        $this->_belongs_to,
                        $this->_many_belongs_to,
                        $this->_composed_of);
  }

  /**
   *  Returns all relations info for one-to-many
   *  @return array
   */
  function getOneToManyRelationsInfo()
  {
    return $this->_has_many;
  }
  /**
   *  Returns all relations info for many-to-many
   *  @return array
   */
  function getManyToManyRelationsInfo()
  {
    return $this->_has_many_to_many;
  }
  /**
   *  Returns all relations info for belongs-to
   *  @return array
   */
  function getBelongsToRelationsInfo()
  {
    return $this->_belongs_to;
  }
  /**
   *  Returns all relations info for many-belongs-to
   *  @return array
   */
  function getManyBelongsToRelationsInfo()
  {
    return $this->_many_belongs_to;
  }
  /**
   *  Returns all relations info for composed_of
   *  @return array
   */
  function getComposedOfRelationsInfo()
  {
    return $this->_composed_of;
  }
  /**
   *  Returns default sort params
   *  @return array
   */
  function getDefaultSortParams()
  {
    if(!$this->_default_sort_params)
      $this->_default_sort_params = array($this->_db_table_name . '.' . $this->_primary_key_name => 'ASC');

    return $this->_default_sort_params;
  }

  protected function _createTableObjectByAlias($class_path_alias)
  {
    $class_path = new lmbClassPath($class_path_alias);
    return $class_path->createObject();
  }
  /**
   *  Returns common validator for create and update operations. It should be overridden
   *  if you want to have a custom validator, e.g:
   *
   *  <code>
   *  $validator = new lmbValidator();
   *  $validator->addRequiredRule('title');
   *  return $validator;
   *  </code>
   *  @return lmbValidator
   */
  protected function _createValidator()
  {
    return new lmbValidator();
  }
  /**
   *  Returns validator for create operations only.
   *  @see _createValidator()
   *  @return lmbValidator
   */
  protected function _createInsertValidator()
  {
    return $this->_createValidator();
  }
  /**
   *  Returns validator for update operations only.
   *  @see _createValidator()
   *  @return lmbValidator
   */
  protected function _createUpdateValidator()
  {
    return $this->_createValidator();
  }

  protected function _savePreRelations()
  {
    foreach($this->_has_one as $property => $info)
      $this->_savePreRelationObject($property, $info, true);

    foreach($this->_many_belongs_to as $property => $info)
      $this->_savePreRelationObject($property, $info, false);
  }

  protected function _savePreRelationObject($property, $info, $save_relation_obj = true)
  {
    if($this->isDirtyProperty($info['field']) && !$this->isDirtyProperty($property))
    {
      $value = $this->_getRaw($info['field']);
      if(is_null($value))
        $this->_setRaw($property, null);
      return;
    }

    $object = $this->_getRaw($property);
    if(is_object($object))
    {
      if($object->isNew() || (!$object->isNew() && $save_relation_obj))
        $object->save($this->_error_list);
      $object_id = $object->getId();
      if($this->_getRaw($info['field']) != $object_id)
      {
        $this->_setRaw($info['field'], $object->getId());
        $this->_markDirtyProperty($info['field']);
      }
    }
    elseif(is_null($object) && $this->isDirtyProperty($property) &&
           isset($info['can_be_null']) && $info['can_be_null'])
    {
      $this->_setRaw($info['field'], null);
      $this->_markDirtyProperty($info['field']);
    }
  }

  protected function _savePostRelations()
  {
    foreach($this->_has_many as $property => $info)
      $this->_savePostRelationCollection($property, $info);

    foreach($this->_has_many_to_many as $property => $info)
      $this->_savePostRelationCollection($property, $info);

    foreach($this->_belongs_to as $property => $info)
      $this->_savePostRelationObject($property, $info);
  }

  protected function _savePostRelationCollection($property, $info)
  {
    $collection = $this->_getRaw($property);
    if(is_object($collection))
      $collection->save($this->_error_list);
  }

  protected function _savePostRelationObject($property, $info)
  {
    $object = $this->_getRaw($property);
    if(is_object($object))
    {
      $object->set($info['field'], $this->getId());
      $object->save($this->_error_list);
    }
  }

  public function __call($method, $args = array())
  {
    try
    {
      return parent :: __call($method, $args);
    }
    catch(lmbNoSuchMethodException $e)
    {
      if($property = $this->mapAddToProperty($method))
        $this->_addToProperty($property, $args[0]);
      else
        throw $e;
    }
  }

  protected function _addToProperty($property, $value)
  {
    $collection = $this->get($property);
    if(!is_object($collection))
      throw new lmbARException("Collection object info for property '{$property}' is missing");

    $collection->add($value);
  }

  protected function _izLazyAttribute($property)
  {
    return in_array($property, $this->_lazy_attributes);
  }

  protected function _loadLazyAttribute($property)
  {
    $record = $this->_db_table->selectRecordById($this->getId(), array($property));
    $processed = $this->_decodeDbValues($record);
    $this->_setRaw($property, $processed[$property]);
  }

  protected function _loadLazyAttributes()
  {
    foreach($this->_lazy_attributes as $attribute)
    {
      if(!parent :: has($attribute))
        $this->_loadLazyAttribute($attribute);
    }
  }

  function getLazyAttributes()
  {
    return $this->_lazy_attributes;
  }

  function setLazyAttributes($lazy_attributes)
  {
    $this->_lazy_attributes = $lazy_attributes;

    //primary key should never be lazy
    unset($this->_lazy_attributes[$this->getPrimaryKeyName()]);
  }

  function setLazyAttributesExcept($non_lazy_attributes)
  {
    $this->setLazyAttributes(array_diff($this->_db_table_fields, $non_lazy_attributes));
  }

  /**
   * Check, that property with given name exists;
   * @param string $property property name
   * @return bool
   */
  function has($property)
  {
    return parent :: has($property)
           || in_array($property, $this->_db_table_fields)
           || $this->_izLazyAttribute($property)
           || $this->_hasRelation($property);
  }

  /**
   *  Generic magic getter for any attribute
   *  @param string property name
   *  @return mixed
   */
  function get($property, $default = LIMB_UNDEFINED)
  {
    if(!$this->isNew() && $this->_izLazyAttribute($property) && !parent :: has($property))
      $this->_loadLazyAttribute($property);

    if($this->_hasAggregatedObjectRelation($property))
    {
      if($aggregated_object = $this->_getAggregatedObject($property))
        return $aggregated_object;

      return (LIMB_UNDEFINED !== $default) ? $default : $aggregated_object;
    }

    try
    {
      return parent :: get($property);
    }
    catch(lmbNoSuchPropertyException $e) {}

    if(LIMB_UNDEFINED !== $default)
      return $default;

    if(in_array($property, $this->_db_table_fields))
      return null;

    if($this->isNew() && $this->_hasSingleObjectRelation($property))
      return null;

    if(!$this->isNew() && $this->_hasBelongsToRelation($property))
    {
      $object = $this->_loadBelongsToObject($property);
      $this->_setRaw($property, $object);
      return $object;
    }

    if(!$this->isNew() && $this->_hasManyBelongsToRelation($property))
    {
      $object = $this->_loadManyBelongsToObject($property);
      $this->_setRaw($property, $object);
      return $object;
    }

    if(!$this->isNew() && $this->_hasOneToOneRelation($property))
    {
      $object = $this->_loadOneToOneObject($property);
      $this->_setRaw($property, $object);
      return $object;
    }

    if($this->_hasCollectionRelation($property))
    {
      $collection = $this->createRelationCollection($property);
      $this->_setRaw($property, $collection);
      return $collection;
    }

    throw $e;
  }

  /**
   * Create collection of related objects
   * @param array $relation
   * @param string|object $criteria
   * @return lmbCollectionInterface
   */
  function createRelationCollection($relation, $criteria = null)
  {
    $info = $this->getRelationInfo($relation);

    if(isset($info['collection']))
      return new $info['collection']($relation, $this, $criteria, $this->_db_conn);
    elseif($this->_hasOneToManyRelation($relation))
      return new lmbAROneToManyCollection($relation, $this, $criteria, $this->_db_conn);
    elseif($this->_hasManyToManyRelation($relation))
      return new lmbARManyToManyCollection($relation, $this, $criteria, $this->_db_conn);
  }

  protected function _hasRelation($property)
  {
    return isset($this->_relations[$property]);
  }

  protected function _hasSingleObjectRelation($property)
  {
    return isset($this->_single_object_relations[$property]);
  }

  protected function _hasCollectionRelation($relation)
  {
    return $this->_hasOneToManyRelation($relation) ||
           $this->_hasManyToManyRelation($relation);
  }

  /**
   *  Generic magic setter for any attribute
   *  @param string property name
   *  @param mixed property value
   */
  function set($property, $value)
  {
    if($this->_hasCollectionRelation($property))
    {
      if($this->isNew())
      {
        $collection = $this->createRelationCollection($property);
        $this->_setRaw($property, $collection);
      }
      else
        $collection = $this->get($property);

      $collection->set($value);
    }
    else
      $this->_setARField($property, $value);
  }

  function _setARField($property, $value)
  {
    $old_value = $this->_getRaw($property);
    parent :: set($property, $value);

    // if property is a table field and was not really changed, don't mark it dirty
    if(!($this->_db_meta_info->hasColumn($property) && ($old_value === $value)))
      $this->_markDirtyProperty($property);
  }

  protected function _markDirtyProperty($property)
  {
    if(!$this->_canPropertyBeDirty($property))
      return;

    $this->_is_dirty = true;
    $this->_dirty_props[$property] = 1;
  }

  protected function _canPropertyBeDirty($property)
  {
    if($this->_db_meta_info->hasColumn($property))
      return true;

    if($this->_isAggregatedObjectProperty($property))
      return true;

    if($this->_canRelationPropertyBeDirty($property, $this->_many_belongs_to))
      return true;

    if($this->_canRelationPropertyBeDirty($property, $this->_has_one))
      return true;

    return false;
  }

  protected function _canRelationPropertyBeDirty($property, $info)
  {
    if(!isset($info[$property]))
      return false;

    if(($object = $this->_getRaw($property)) &&
       ($object->getId() == $this->_getRaw($info[$property]['field'])))
      return false;
    else
      return true;
  }

  function resetDirty()
  {
    $this->_resetDirty();
  }

  protected function _resetDirty()
  {
    $this->_is_dirty = false;
    $this->_dirty_props = array();
  }
  /**
   *  Marks object as dirty
   */
  function markDirty()
  {
    $this->_is_dirty = true;
  }
  /**
   *  Returns object's dirtiness status
   *  @return boolean
   */
  function isDirty()
  {
    return $this->_is_dirty;
  }
  /**
   *  Returns object's property dirtiness status
   *  @param string
   *  @return boolean
   */
  function isDirtyProperty($property)
  {
    return isset($this->_dirty_props[$property]);
  }

  /**
   *  reset object's property dirtiness status
   *  @param string
   */
  function resetPropertyDirtiness($property)
  {
    if(isset($this->_dirty_props[$property]))
      unset($this->_dirty_props[$property]);
  }

  /**
   *  Maps property name to "addTo" form, e.g. "property_name" => "addToPropertyName"
   *  @param string
   *  @return string
   */
  function mapPropertyToAddToMethod($property)
  {
    return 'addTo' . lmb_camel_case($property);
  }
  /**
   *  Maps "addTo" to property, e.g. "addToPropertyName" => "property_name"
   *  @param string
   *  @return string
   */
  function mapAddToProperty($method)
  {
    if(substr($method, 0, 5) == 'addTo')
      return lmb_under_scores(substr($method, 5));
  }
  /**
   *  Maps database field to property name
   *  @param string
   *  @return string
   */
  function mapFieldToProperty($field)
  {
    foreach($this->_relations as $property => $info)
    {
      if(isset($info['field']) && $info['field'] == $field)
        return $property;
    }
  }

  protected function _hasBelongsToRelation($property)
  {
    return isset($this->_belongs_to[$property]);
  }

  protected function _hasManyBelongsToRelation($property)
  {
    return isset($this->_many_belongs_to[$property]);
  }

  protected function _hasOneToOneRelation($property)
  {
    return isset($this->_has_one[$property]);
  }

  protected function _hasOneToManyRelation($property)
  {
    return isset($this->_has_many[$property]);
  }

  protected function _hasManyToManyRelation($property)
  {
    return isset($this->_has_many_to_many[$property]);
  }

  protected function _hasAggregatedObjectRelation($property)
  {
    return isset($this->_composed_of[$property]);
  }

  protected function _loadBelongsToObject($property)
  {
    return self :: findFirst($this->_belongs_to[$property]['class'],
                             array('criteria' => $this->_db_conn->quoteIdentifier($this->_belongs_to[$property]['field']) . ' = ' . (int)$this->getId()),
                             $this->_db_conn);
  }

  protected function _loadManyBelongsToObject($property)
  {
    $value = $this->_getRaw($this->_many_belongs_to[$property]['field']);
    if(!$value && $this->_canManyBelongsToObjectBeNull($property))
      return null;

    if(isset($this->_many_belongs_to[$property]['throw_exception_on_not_found']))
      $throw_exception = $this->_many_belongs_to[$property]['throw_exception_on_not_found'];
    else
      $throw_exception = true;

    return self :: findById($this->_many_belongs_to[$property]['class'],
                            $this->get($this->_many_belongs_to[$property]['field']),
                            $throw_exception,
                            $this->_db_conn);
  }

  protected function _loadOneToOneObject($property)
  {
    $value = $this->_getRaw($this->_has_one[$property]['field']);
    if(!$value && $this->_canHasOneObjectBeNull($property))
      return null;

    if(isset($this->_has_one[$property]['throw_exception_on_not_found']))
      $throw_exception = $this->_has_one[$property]['throw_exception_on_not_found'];
    else
      $throw_exception = true;

    return self :: findById($this->_has_one[$property]['class'],
                            $this->get($this->_has_one[$property]['field']),
                            $throw_exception,
                            $this->_db_conn);
  }

  protected function _canHasOneObjectBeNull($property)
  {
    return isset($this->_has_one[$property]['can_be_null']) &&
           $this->_has_one[$property]['can_be_null'];
  }

  protected function _canManyBelongsToObjectBeNull($property)
  {
    return isset($this->_many_belongs_to[$property]['can_be_null']) &&
           $this->_many_belongs_to[$property]['can_be_null'];
  }



  protected function _mapMethodToClass($method)
  {
    return substr($method, 3);
  }

  protected function _doSave($need_validation)
  {
    if($this->_is_being_saved)
      return;

    try
    {
      $this->_is_being_saved = true;

      $this->_onBeforeSave();

      $this->_invokeListeners(self :: ON_BEFORE_SAVE);

      //_savePreRelations makes the object dirty if related objects were changed
      //in case of the new object we don't care since it will be saved anyway
      //but in case of the existing object we must make sure it's dirty
      //otherwise it won't be saved
      if(!$this->isNew())
        $this->_savePreRelations();

      $this->_checkDirtinessOfAggregatedObjectsFields();

      if(!$this->isNew() && $this->isDirty())
      {
        $this->_onBeforeUpdate();

        $this->_invokeListeners(self :: ON_BEFORE_UPDATE);

        if($need_validation && !$this->_validateUpdate())
        {
          $this->_is_being_saved = false;
          throw new lmbValidationException('ActiveRecord "' . get_class($this) . '" validation failed',
                                           $this->_error_list);
        }

        $this->_onSave();

        $this->_onUpdate();

        $this->_invokeListeners(self :: ON_UPDATE);

        $this->_setAutoTimes();

        $this->_updateDbRecord($this->_mapPropertiesToDbFields($only_dirty_properties = true));

        $this->_onAfterUpdate();

        $this->_invokeListeners(self :: ON_AFTER_UPDATE);
      }
      elseif($this->isNew())
      {
        $this->_onBeforeCreate();

        $this->_invokeListeners(self :: ON_BEFORE_CREATE);

        $this->_savePreRelations();

        if($need_validation && !$this->_validateInsert())
        {
          $this->_is_being_saved = false;
          throw new lmbValidationException('ActiveRecord "' . get_class($this) . '" validation failed',
                                           $this->_error_list);
        }

        $this->_onSave();

        $this->_onCreate();

        $this->_invokeListeners(self :: ON_CREATE);

        $this->_setAutoTimes();

        $new_id = $this->_insertDbRecord($this->_mapPropertiesToDbFields());
        $this->_is_new = false;
        $this->setId($new_id);

        $this->_onAfterCreate();

        $this->_invokeListeners(self :: ON_AFTER_CREATE);
      }

      $this->_onAfterSave();

      $this->_invokeListeners(self :: ON_AFTER_SAVE);

      $this->_savePostRelations();

      $this->_resetDirty();

      $this->_is_being_saved = false;
    }
    catch(Exception $e)
    {
      $this->_db_conn->rollbackTransaction();
      throw $e;
    }

    return $this->getId();
  }

  protected function _updateDbRecord($values)
  {
    if(count($values))
      return $this->_db_table->updateById($this->id, $values);
  }

  protected function _insertDbRecord($values)
  {
    return $this->_db_table->insert($values);
  }

  protected function _mapPropertiesToDbFields($only_dirty_properties = false)
  {
    $fields = $this->export();

    if($only_dirty_properties)
    {
      foreach($fields as $field => $value)
      {
        if(!$this->isDirtyProperty($field))
          unset($fields[$field]);
      }
    }

    foreach($fields as $field => $value)
    {
      if($this->_isAggregatedObjectProperty($field))
      {
        unset($fields[$field]);
        $fields = array_merge($fields, $this->_getDbFieldsFromAggregatedObject($field, $value));
      }
    }

    if($this->isNew() && $this->_isInheritable())
      $fields[self :: $_inheritance_field] = $this->_getInheritancePath();

    return $fields;
  }

  function _isAggregatedObjectProperty($property)
  {
    return array_key_exists($property, $this->_composed_of);
  }

  function _getDbFieldsFromAggregatedObject($property, $object)
  {
    $info = $this->_composed_of[$property];

    if(!is_object($object))
      return array();

    // for bc
    if(isset($info['getter']))
    {
      $method = $info['getter'];
      return array($property => $object->$method());
    }

    if(!isset($info['mapping']))
      $mapping = array($property => $property);
    else
      $mapping = $info['mapping'];

    $fields = array();
    foreach($mapping as $aggregate_field => $ar_field)
    {
      if($ar_field == $this->getPrimaryKeyName())
        continue;

      $fields[$ar_field] = $object->get($aggregate_field);
    }
    return $fields;
  }

  protected function _checkDirtinessOfAggregatedObjectsFields()
  {
    foreach($this->_composed_of as $property => $info)
      if(isset($info['mapping']))
      {
        $this->_markDirtyProperty($property);
      }
  }

  protected function _loadAggregatedObject($property, $value = LIMB_UNDEFINED)
  {
    // for BC
    if(isset($this->_composed_of[$property]['getter']))
    {
      if ($value === LIMB_UNDEFINED)
      {
        if(!$value = $this->_getRaw($property))
          return $value;
      }

      $class = $this->_composed_of[$property]['class'];
      $object = new $class($value);
      return $object;
    }

    $class = $this->_composed_of[$property]['class'];
    $object = new $class();

    if(isset($this->_composed_of[$property]['mapping']))
      $mapping = $this->_composed_of[$property]['mapping'];
    else
      $mapping = array($property => $property);

    foreach($mapping as $aggregate_field => $ar_field)
      $object->set($aggregate_field, $this->_getRaw($ar_field));

    if(isset($this->_composed_of[$property]['setup_method']))
    {
      $setup_method =$this->_composed_of[$property]['setup_method'];
      $object = $this->$setup_method($object);
    }

    return $object;
  }

  protected function _getAggregatedObject($property)
  {
    if(parent :: has($property))
    {
      $value = $this->_getRaw($property);

      if(is_object($value))
        return $value;
    }

    $object = $this->_loadAggregatedObject($property);
    $this->_setRaw($property, $object);

    return $object;
  }

  protected function _setAutoTimes()
  {
    if($this->isNew() && $this->_hasCreateTime())
      $this->_setRaw($this->_ctime_field, $this->_makeCreateTime());

    if($this->_hasUpdateTime())
    {
      $this->_setRaw($this->_utime_field, $this->_makeUpdateTime());
      $this->_markDirtyProperty($this->_utime_field);
    }
  }

  protected function _makeCreateTime()
  {
    return time();
  }

  protected function _makeUpdateTime()
  {
    return time();
  }

  protected function _hasUpdateTime()
  {
    return $this->_db_meta_info->hasColumn($this->_utime_field);
  }

  protected function _hasCreateTime()
  {
    return $this->_db_meta_info->hasColumn($this->_ctime_field);
  }

  protected function _isInheritable()
  {
    if(!is_null($this->_is_inheritable))
      return $this->_is_inheritable;

    $this->_is_inheritable = $this->_db_meta_info->hasColumn(self :: $_inheritance_field);
    return $this->_is_inheritable;
  }
  /**
   *  Validates object and saves into database, throws exception if there were any errors
   *  @param object error list object which will receive all validation errors
   *  @return integer id of the saved object
   */
  function save($error_list = null)
  {
    if($error_list)
      $this->_error_list = $error_list;

    return $this->_doSave(true);
  }
  /**
   *  Saves object into database skipping any validation, throws exception if there were any errors
   *  @return integer id of the saved object
   */
  function saveSkipValidation()
  {
    return $this->_doSave(false);
  }
  /**
   *  Validates object and saves into database, catches all exceptions if there were any errors
   *  @param object error list object which will receive all validation errors
   *  @return boolean success status of operation
   */
  function trySave($error_list = null)
  {
    try
    {
      $this->save($error_list);
    }
    catch(lmbValidationException $e)
    {
      return false;
    }
    catch(Exception $e)
    {
      if($error_list)
        $error_list->addError('ActiveRecord :: save() exception: ' . $e->getMessage());
      return false;
    }
    return true;
  }
  /**
   *  Returns whether object is new
   *  @return boolean
   */
  function isNew()
  {
    return ($this->_is_new || !$this->getId());
  }
  /**
   *  Forces object to be new or not
   *  @param boolean new status
   */
  function setIsNew($value = true)
  {
    $this->_is_new = (boolean)$value;
  }
  /**
   *  Detaches object by making it new and removing its identity
   */
  function detach()
  {
    $this->setIsNew();
    $this->remove($this->_primary_key_name);
  }
  /**
   *  Validates object
   *  @param object error list object which will receive all validation errors
   *  @return boolean validation status
   */
  function validate($error_list = null)
  {
    if($error_list)
      $this->_error_list = $error_list;

    if($this->isNew())
      return $this->_validateInsert();
    else
      return $this->_validateUpdate();
  }

  protected function _onBeforeUpdate(){}

  protected function _onBeforeCreate(){}

  protected function _onBeforeSave(){}

  protected function _onBeforeDestroy(){}

  protected function _onAfterSave(){}

  protected function _onUpdate(){}

  protected function _onCreate(){}

  protected function _onSave(){}

  protected function _onAfterUpdate(){}

  protected function _onAfterCreate(){}

  protected function _onAfterDestroy(){}

  protected function _onValidate(){}

  protected function _onAfterImport(){}

  protected function _validateInsert()
  {
    return $this->_validate($this->_createInsertValidator());
  }

  protected function _validateUpdate()
  {
    return $this->_validate($this->_createUpdateValidator());
  }

  protected function _validate($validator)
  {
    if($old_error_list = $validator->getErrorList())
      $this->_error_list->join($old_error_list);
    $validator->setErrorList($this->_error_list);
    $validator->validate($this);

    $this->_onValidate();

    return $this->_error_list->isValid();
  }

  protected function _addError($message, $fields = array(), $values = array())
  {
    $this->_error_list->addError($message, $fields, $values);
  }

  /**
   * Check record is valid
   * @return bool
   */
  function isValid()
  {
    return $this->_error_list->isValid();
  }

  protected static function _isCriteria($params)
  {
    if(is_object($params) || is_string($params))
      return true;

    if(is_array($params) && sizeof($params))
    {
      foreach($params as $key => $value)
      {
        //remove obsolete check for 'first' property
        if(!is_int($key) || $value == 'first')
          return false;
      }
      return true;
    }
    return false;
  }

  static protected function _isClass($name)
  {
    if(!is_scalar($name) || !$name)
      return false;

    return ctype_alnum("$name") && !ctype_digit("$name");
  }

  static protected function _getCallingClass()
  {
    if(function_exists('get_called_class'))
      return get_called_class();

    //once PHP-5.3 LSB patch is available we'll use get_called_class
    //currently it's a quite a slow implementation and it doesn't
    //recognize multiline function calls

    $trace = debug_backtrace();
    $back = $trace[1];
    $method = $back['function'];
    $fp = fopen($back['file'], 'r');

    for($i=0; $i<$back['line']-1; $i++)
      fgets($fp);

    $line = fgets($fp);
    fclose($fp);

    if(!preg_match('~(\w+)\s*::\s*' . $method . '\s*\(~', $line, $m))
      throw new lmbARException("Static calling class not found!(using multiline static method call?)");
    if($m[1] == 'lmbActiveRecord')
      throw new lmbARException("Found static class can't be lmbActiveRecord!");
    return $m[1];
  }

  /**
   *  Finds one instance of object in database, this method is actually a wrapper around find()
   *  @see find()
   *  @param string class name of the object
   *  @param mixed misc magic params
   *  @param object database connection object
   *  @return lmbActiveRecord|null
   */
  static function findFirst($class_name = null, $magic_params = null, $conn = null)
  {
    if(!self :: _isClass($class_name))
    {
      $conn = $magic_params;
      $magic_params = $class_name ? $class_name : array();
      $class_name = self :: _getCallingClass();
    }

    $params = array();
    if(self :: _isCriteria($magic_params))
      $params = array('first', 'criteria' => $magic_params);
    elseif(is_null($magic_params))
      $params = array('first');
    elseif(is_array($magic_params))
    {
      $params = $magic_params;
      array_push($params, 'first');
    }

    if(!class_exists($class_name, true))
      throw new lmbARException("Could not find class '$class_name'");

    if(!is_object($conn))
      $conn = self :: getDefaultConnection();

    $obj = new $class_name(null, $conn);
    return $obj->_findFirst($params);
  }
  /**
   *  self :: findFirst() convenience alias
   *  @see findFirst()
   *  @param string class name of the object
   *  @param mixed misc magic params
   *  @return lmbActiveRecord|null
   */
  static function findOne($class_name = null, $magic_params = null, $conn = null)
  {
    if(!self :: _isClass($class_name))
    {
      $conn = $magic_params;
      $magic_params = $class_name ? $class_name : array();
      $class_name = self :: _getCallingClass();
    }
    return self :: findFirst($class_name, $magic_params, $conn);
  }
  /**
   *  Userland filter for findFirst() static method
   *  @see findFirst()
   *  @param mixed misc magic params
   *  @return object|null
   */
  protected function _findFirst($params)
  {
    return $this->_find($params);
  }
  /**
   *  Finds one instance of object in database using object id, this method is actually a wrapper around find()
   *  @see find()
   *  @param string class name of the object
   *  @param integer object id
   *  @param object database connection object
   *  @return lmbActiveRecord|null
   */
  static function findById($class_name, $id = null, $throw_exception = true, $conn = null)
  {
    if(!self :: _isClass($class_name))
    {
      $conn = $throw_exception;
      $throw_exception = $id;
      $id = $class_name;
      $class_name = self :: _getCallingClass();
    }

    if(!class_exists($class_name, true))
      throw new lmbARException("Could not find class '$class_name'");

    if(!is_object($conn))
      $conn = self :: getDefaultConnection();

    $obj = new $class_name(null, $conn);
    return $obj->_findById($id, $throw_exception);
  }
  /**
   *  Userland filter for findById() static method
   *  @see findById()
   *  @param integer object id
   *  @return object
   */
  protected function _findById($id_or_arr, $throw_exception)
  {
    if(is_array($id_or_arr))
    {
      if(!isset($id_or_arr['id']))
        throw new lmbARException("Criteria attribute 'id' is required for findById");

      $params = $id_or_arr;
      //avoiding possible recursion
      unset($params['id']);
      array_unshift($params, 'first');
      $id = (int)$id_or_arr['id'];
      $params['criteria'] = $this->_db_conn->quoteIdentifier($this->_primary_key_name) . '=' . $id;
    }
    else
    {
      $id = (int)$id_or_arr;
      $params = array('first', 'criteria' => $this->_db_conn->quoteIdentifier($this->_primary_key_name) . '=' . $id);
    }

    if($object = $this->_find($params))
      return $object;
    elseif($throw_exception)
      throw new lmbARNotFoundException(get_class($this), $id);
    else
      return null;
  }
  /**
   *  Finds a collection of objects in database using array of object ids, this method is actually a wrapper around find()
   *  @see find()
   *  @param string class name of the object
   *  @param array object ids
   *  @param mixed misc magic params
   *  @param object database connection object
   *  @return iterator
   */
  static function findByIds($class_name, $ids = null, $params = null, $conn = null)
  {
    if(!self :: _isClass($class_name))
    {
      $conn = $params;
      $params = $ids;
      $ids = $class_name;
      $class_name = self :: _getCallingClass();
    }

    if(!class_exists($class_name, true))
      throw new lmbARException("Could not find class '$class_name'");

    if(!is_object($conn))
      $conn = self :: getDefaultConnection();

    $obj = new $class_name(null, $conn);
    return $obj->_findByIds($ids, $params);
  }
  /**
   *  Userland filter for findByIds() static method
   *  @see findByIds()
   *  @param array object ids
   *  @param mixed misc magic params
   *  @return iterator
   */
  protected function _findByIds($ids, $params = array())
  {
    if(!is_array($ids) || !sizeof($ids))
      return new lmbCollection();
    else
    {
      $params['criteria'] = new lmbSQLFieldCriteria($this->_db_table_name .'.'.$this->_primary_key_name, $ids, lmbSQLFieldCriteria :: IN);
      return $this->_find($params);
    }
  }
  /**
   *  Implements datasource interface, this method simply calls find()
   *  @see find()
   *  @param mixed misc magic params
   *  @return iterator
   */
  function getDataset($magic_params = array())
  {
    return $this->_find($magic_params);
  }
  /**
   *  Finds a collection of objects in database using raw SQL
   *  @param string class name of the object
   *  @param string SQL
   *  @param object database connection object
   *  @return iterator
   */
  static function findBySql($class_name, $sql = null, $conn = null)
  {
    if(!self :: _isClass($class_name))
    {
      $conn = $sql;
      $sql = $class_name;
      $class_name = self :: _getCallingClass();
    }

    if(!is_object($conn))
      $conn = self :: getDefaultConnection();

    $stmt = $conn->newStatement($sql);
    return self :: decorateRecordSet($stmt->getRecordSet(), $class_name);
  }
  /**
   *  Finds first object in database using raw SQL
   *  @param string class name of the object
   *  @param string SQL
   *  @param object database connection object
   *  @return lmbActiveRecord
   */
  static function findFirstBySql($class_name, $sql = null, $conn = null)
  {
    if(!self :: _isClass($class_name))
    {
      $conn = $sql;
      $sql = $class_name;
      $class_name = self :: _getCallingClass();
    }

    $rs = self :: findBySql($class_name, $sql, $conn);
    $rs->paginate(0, 1);
    $rs->rewind();
    if($rs->valid())
      return $rs->current();
  }
  /**
   *  Alias for findFirstBySql
   *  @see findFirstBySql()
   *  @return lmbActiveRecord
   */
  static function findOneBySql($class_name, $sql = null, $conn = null)
  {
    if(!self :: _isClass($class_name))
    {
      $conn = $sql;
      $sql = $class_name;
      $class_name = self :: _getCallingClass();
    }
    return self :: findFirstBySql($class_name, $sql, $conn);
  }

  /**
   *  Generic objects finder.
   *  Using misc magic params it's possible to pass different search parameters.
   *  If passed as an array magic params can have the following properties:
   *   - <b>criteria</b> - apply specified criteria to collection can be a plain string or criteria object
   *   - <b>limit,offset</b> - apply limit,offset to collection
   *   - <b>sort</b>  - sort collection by specified fields, e.g array('id' => 'desc', 'name' => 'asc')
   *   - <b>first</b> - return the first object of collection
   *  Some examples:
   *  <code>
   *  //generic way to find a collection of objects using magic params,
   *  //in this case we want collection:
   *  // - to match 'name="hey"' criteria
   *  // - ordered by 'id' property using descendant sort
   *  // - limited to 3 items
   *  $books = self :: find('Book', array('criteria' => 'name="hey"',
   *                                                 'sort' => array('id' => 'desc'),
   *                                                 'limit' => 3));
   *  //returns a collection of all Book objects in database
   *  $books = self :: find('Book');
   *  //returns one object with specified id
   *  $books = self :: find('Book', 1);
   *  //returns a collection of objects which match plain text criteria
   *  $books = self :: find('Book', 'name="hey"');
   *  //returns a collection of objects which match criteria with placeholders
   *  $books = self :: find('Book', array('name=? and author=?', 'hey', 'bob'));
   *  //returns a collection of objects which match object criteria
   *  $books = self :: find('Book',
   *                                    new lmbSQLFieldCriteria('name', 'hey'));
   *  </code>
   *  @param string class name of the object
   *  @param mixed misc magic params
   *  @param object database connection object
   *  @return iterator
   */
  static function find($class_name = null, $magic_params = null, $conn = null)
  {
    if(!self :: _isClass($class_name))
    {
      $conn = $magic_params;
      $magic_params = $class_name ? $class_name : array();
      $class_name = self :: _getCallingClass();
    }

    if(!is_object($conn))
      $conn = self :: getDefaultConnection();

    if(self :: _isCriteria($magic_params))
      $params = array('criteria' => $magic_params);
    elseif(is_int($magic_params) || (is_array($magic_params) && isset($magic_params['id'])))
      return self :: findById($class_name, $magic_params, false, $conn);
    elseif(is_null($magic_params))
      $params = array();
    elseif(!is_array($magic_params))
      throw new lmbARException("Invalid magic params", array($magic_params));
    else
      $params = $magic_params;

    if(!class_exists($class_name, true))
      throw new lmbARException("Could not find class '$class_name'");

    $obj = new $class_name(null, $conn);

    return $obj->_find($params);
  }
  /**
   *  Userland filter for find() static method
   *  @see find()
   *  @param mixed misc magic params
   *  @return iterator
   */
  protected function _find($params = array())
  {
    if(isset($params['fields']) && is_array($params['fields']))
      $this->setLazyAttributesExcept($params['fields']);

    $query = lmbARQuery :: create($this, $params, $this->_db_conn);
    $rs = $query->fetch();

    $return_first = false;
    foreach(array_values($params) as $value)
    {
      if(is_string($value) && $value == 'first')
      {
        $return_first = true;
        $params['limit'] = 1;
        break;
      }
    }

    if(isset($params['limit']))
      $rs->paginate(isset($params['offset']) ? $params['offset'] : 0, $params['limit']);

    if($return_first)
    {
      $rs->rewind();
      if($rs->valid())
        return $rs->current();
    }
    else
      return $rs;
  }

  /**
   *  Finds a collection of records(not lmbActiveRecord objects!) from database table
   *  @param string|object filtering criteria
   *  @param array sort params
   *  @return iterator
   */
  function findAllRecords($criteria = null, $sort_params = array())
  {
    $query = lmbARQuery :: create(get_class($this), $params = array('criteria' => $criteria, 'sort' => $sort_params), $this->_db_conn);
    return $query->fetch($decorate = false);
  }

  /**
   *  Adds class name criterion to passed in criteria
   *  @param string|object criteria
   *  @return object
   */
  function addClassCriteria($criteria)
  {
    if(!is_object($criteria))
      $criteria = lmbSQLCriteria::objectify($criteria);

    if($this->_isInheritable())
      $criteria = $this->_getInheritanceCriteria()->add($criteria);

    return $criteria;
  }

  protected function _getInheritanceCriteria()
  {
    return lmbSQLCriteria::like($this->getInheritanceField(), $this->_getInheritancePath() . "%");
  }

  protected function _getInheritancePath()
  {
    $class = get_class($this);
    $path = "$class|";
    while($class = get_parent_class($class))
    {
      if($class == __CLASS__)
        break;
      $path = "$class|$path";
    }
    return $path;
  }

  static function decodeInheritancePath($path)
  {
    $items = explode('|', $path);
    array_pop($items);//removing last empty item
    return $items;
  }

  static function getInheritanceClass($obj)
  {
    $decoded_path = self :: decodeInheritancePath($obj[self :: $_inheritance_field]);
    return end($decoded_path);
  }

  /**
   *  Loads current object with data from database, overwrites any previous data, marks object dirty and unsets new status
   *  @param integer object id
   */
  function loadById($id)
  {
    $object = $this->_findById($id, true);
    $this->importRaw($object->exportRaw());
    $this->_resetDirty();
    $this->_is_new = false;
  }
  /**
   *  Loads current object with data from database record, overwrites any previous data, resets object dirtiness and unsets new status
   *  @param object database record object
   */
  function loadFromRecord($record)
  {
    $decoded = $this->_decodeDbValues($record);

    foreach($decoded as $key => $value)
      $this->_setRaw($key, $value);

    $this->_resetDirty();
    $this->_is_new = false;
    return true;
  }

  protected function _decodeDbValues($record)
  {
    return $this->_db_meta_info->castDbValues($record);
  }
  /**
   *  Returns id of object typecasted to integer explicitly
   *  @return integer
   */
  function getId()
  {
    if($id = $this->_getRaw($this->_primary_key_name))
      return $id;
  }

  /**
   *  Sets id of an object typecasted to integer explicitly, be carefull using this method since
   *  it may break relations if used improperly
   *  @param integer
   */
  function setId($id)
  {
    $this->_setRaw($this->_primary_key_name, (int)$id);
  }

  function getUpdateTime()
  {
    return $this->_getRaw($this->_utime_field);
  }

  function getCreateTime()
  {
    return $this->_getRaw($this->_ctime_field);
  }

  /**
   *  Destroys current object removing it from database as well, removes related objects if
   *  object was configured to do so. Throws exception if object doesn't have identity.
   */
  function destroy()
  {
    if($this->_is_being_destroyed)
      return;

    if(!$this->getId())
      throw new lmbARException('Id not set');

    $this->_is_being_destroyed = true;

    $this->_onBeforeDestroy();
    $this->_invokeListeners(self :: ON_BEFORE_DESTROY);

    $this->_removeOneToOneObjects();
    $this->_removeOneToManyObjects();
    $this->_removeManyToManyRecords();
    $this->_removeBelongsToRelations();

    $this->_deleteDbRecord();

    $this->_onAfterDestroy();
    $this->_invokeListeners(self :: ON_AFTER_DESTROY);

    $this->_is_being_destroyed = false;
  }

  protected function _deleteDbRecord()
  {
    $this->_db_table->deleteById($this->getId());
  }
  /**
   *  Finds all objects which satisfy the passed criteria and destroys them one by one
   *  @param string class name
   *  @param string|object search criteria, if not set all objects are removed
   *  @param object database connection object
   */
  static function delete($class_name = null, $criteria = null, $conn = null)
  {
    if(!self :: _isClass($class_name))
    {
      $conn = $criteria;
      $criteria = $class_name;
      $class_name = self :: _getCallingClass();
    }

    if(!is_object($conn))
      $conn = self :: getDefaultConnection();

    $params = array();
    if($criteria)
      $params = array('criteria' => $criteria);

    $rs = self :: find($class_name, $params, $conn);
    foreach($rs as $object)
      $object->destroy();
  }

  static function deleteRaw($class_name = null, $criteria = null, $conn = null)
  {
    if(!self :: _isClass($class_name))
    {
      $conn = $criteria;
      $criteria = $class_name;
      $class_name = self :: _getCallingClass();
    }

    if(!is_object($conn))
      $conn = self :: getDefaultConnection();

    $object = new $class_name(null, $conn);
    $db_table = $object->getDbTable();
    $db_table->delete($criteria);
  }

  static function updateRaw($class_name, $set = null, $criteria = null, $conn = null)
  {
    if(!self :: _isClass($class_name))
    {
      $conn = $criteria;
      $criteria = $set;
      $set = $class_name;
      $class_name = self :: _getCallingClass();
    }

    if(!is_object($conn))
      $conn = self :: getDefaultConnection();

    $object = new $class_name(null, $conn);
    $db_table = $object->getDbTable();
    $db_table->update($set, $criteria);
  }

  protected function _removeOneToOneObjects()
  {
    foreach($this->_has_one as $property => $info)
    {
      if(isset($info['cascade_delete']) && !$info['cascade_delete'])
        continue;

      if($object = $this->get($property))
        $object->destroy();
    }
  }

  protected function _removeOneToManyObjects()
  {
    foreach($this->_has_many as $property => $info)
    {
      $collection = $this->get($property);

      if(!$collection)
        continue;

      if(isset($info['nullify']) && $info['nullify'])
        $collection->nullify();
      else
        $collection->removeAll();
    }
  }

  protected function _removeManyToManyRecords()
  {
    foreach($this->_has_many_to_many as $property => $info)
    {
      if($collection = $this->get($property))
        $collection->removeAll();
    }
  }

  protected function _removeBelongsToRelations()
  {
    foreach($this->_belongs_to as $property => $info)
    {
      if($parent = $this->get($property))
      {
        $parent->set($info['field'], null);
        $parent->save();
      }
    }
  }

  protected function _createSQLStatement($sql)
  {
    return $this->_db_conn->newStatement($sql);
  }

  protected function _query($sql)
  {
    $stmt = $this->_createSQLStatement($sql);
    return $stmt->getRecordSet();
  }

  protected function _execute($sql)
  {
    $stmt = $this->_createSQLStatement($sql);
    return $stmt->execute();
  }
  /**
   *  Decorates database recordset with special decorator which converts each record into
   *  corresponding lmbActiveRecord object.
   *  @see lmbARRecordSetDecorator
   *  @param iterator
   *  @param string wrapper class name
   *  @param object database connection object
   */
  static function decorateRecordSet($rs, $class, $conn = null)
  {
    if(!is_object($conn))
      $conn = self :: getDefaultConnection();

    return new lmbARRecordSetDecorator($rs, $class, $conn);
  }

  function _decorateRecordSet($rs)
  {
    return new lmbARRecordSetDecorator($rs, get_class($this), $this->_db_conn);
  }

  function __clone()
  {
    $this->remove($this->_primary_key_name);
  }
  /**
   *  Imports magically data into object using relation info. This method is magic because it allows to
   *  import scalar data into objects. E.g:
   *  <code>
   *  //provided Book has Author many-to-one relation as 'author' property
   *  $book = new Book();
   *  //will try load Author with id = 2
   *  $book->import(array('title' => 'Alice in wonderand',
   *                      'author' => 2));
   *  //should print '2'
   *  echo $book->getAuthor()->getId();
   *  </code>
   *  @param array|object
   */
  function import($source)
  {
    lmb_assert_type($source, 'array');

    if(is_object($source))
    {
      if($source instanceof lmbActiveRecord)
      {
        $this->importRaw($source->exportRaw());
        $this->setIsNew($source->isNew());
      }
      else
        $this->import($source->export());
      return;
    }

    foreach($source as $property => $value)
    {
      if(isset($this->_composed_of[$property]))
        $this->_importAggregatedObject($property, $value);
      elseif(isset($this->_has_many[$property]))
        $this->_importCollection($property, $value, $this->_has_many[$property]['class']);
      elseif(isset($this->_has_many_to_many[$property]))
        $this->_importCollection($property, $value, $this->_has_many_to_many[$property]['class']);
      elseif(isset($this->_belongs_to[$property]))
        $this->_importEntity($property, $value, $this->_belongs_to[$property]['class']);
      elseif(isset($this->_many_belongs_to[$property]))
        $this->_importEntity($property, $value, $this->_many_belongs_to[$property]['class']);
      elseif(isset($this->_has_one[$property]))
        $this->_importEntity($property, $value, $this->_has_one[$property]['class']);
      elseif($this->_canImportProperty($property))
        $this->set($property, $value);
    }
    $this->_onAfterImport();
  }
  /**
   *  Plain import of data into object
   *  @see lmbObject::import()
   *  @param array
   */
  function importRaw($source)
  {
    parent :: import($source);

    $this->markDirty(true);
  }

  protected function _canImportProperty($property)
  {
    if($this->isNew())
      return true;

    if($property == $this->_primary_key_name)
      return false;

    return true;
  }

  protected function _importCollection($property, $value, $class)
  {
    if(is_array($value))
    {
      $objects = array();
      foreach($value as $item)
      {
        if(is_numeric($item))
          $objects[] = new $class((int)$item, $this->_db_conn);
        elseif(is_object($item))
          $objects[] = $item;
      }
      $this->get($property)->set($objects);
    }
  }

  protected function _importEntity($property, $value, $class)
  {
    if(is_numeric($value))
    {
      $obj = new $class((int)$value, $this->_db_conn);
      $this->set($property, $obj);
    }
    elseif(is_object($value))
      $this->set($property, $value);
    elseif(is_null($value) || strcasecmp($value, 'null') === 0 || ($value === ''))
      $this->set($property, null);
  }

  protected function _importAggregatedObject($property, $obj)
  {
    if(is_object($obj))
      return $this->set($property, $obj);

    $this->set($property, $this->_loadAggregatedObject($property, $obj));
  }
  /**
   *  Exports object data with lazy properties resolved
   *  @return array
   */
  function export()
  {
    if(!$this->isNew() && sizeof($this->_lazy_attributes))
      $this->_loadLazyAttributes();

    return parent :: export();
  }
  /**
   *  Plain export of object data(lazy properties not included if not loaded)
   *  @see lmbObject::export()
   *  @return array
   */
  function exportRaw()
  {
    return parent :: export();
  }
  /**
   *  Registers instance listener of specified type
   *  @param integer call back type
   *  @param object call back object
   */
  function registerCallback($type, $callback)
  {
    $this->_listeners[$type][] = lmbDelegate :: objectify($callback);
  }

  function registerOnBeforeSaveCallback($callback)
  {
    $args = func_get_args();
    $this->registerCallback(self :: ON_BEFORE_SAVE, $args);
  }

  function registerOnAfterSaveCallback($callback)
  {
    $args = func_get_args();
    $this->registerCallback(self :: ON_AFTER_SAVE, $args);
  }

  function registerOnBeforeUpdateCallback($callback)
  {
    $args = func_get_args();
    $this->registerCallback(self :: ON_BEFORE_UPDATE, $args);
  }

  function registerOnUpdateCallback($callback)
  {
    $args = func_get_args();
    $this->registerCallback(self :: ON_UPDATE, $args);
  }

  function registerOnAfterUpdateCallback($callback)
  {
    $args = func_get_args();
    $this->registerCallback(self :: ON_AFTER_UPDATE, $args);
  }

  function registerOnBeforeCreateCallback($callback)
  {
    $args = func_get_args();
    $this->registerCallback(self :: ON_BEFORE_CREATE, $args);
  }

  function registerOnCreateCallback($callback)
  {
    $args = func_get_args();
    $this->registerCallback(self :: ON_CREATE, $args);
  }

  function registerOnAfterCreateCallback($callback)
  {
    $args = func_get_args();
    $this->registerCallback(self :: ON_AFTER_CREATE, $args);
  }

  function registerOnBeforeDestroyCallback($callback)
  {
    $args = func_get_args();
    $this->registerCallback(self :: ON_BEFORE_DESTROY, $args);
  }

  function registerOnAfterDestroyCallback($callback)
  {
    $args = func_get_args();
    $this->registerCallback(self :: ON_AFTER_DESTROY, $args);
  }
  /**
   *  Registers global listener of specified type
   *  @param integer call back type
   *  @param object call back object
   */
  static function registerGlobalCallback($type, $callback)
  {
    self :: $_global_listeners[$type][] = lmbDelegate :: objectify($callback);
  }

  static function registerGlobalOnBeforeSaveCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_BEFORE_SAVE, $args);
  }

  static function registerGlobalOnAfterSaveCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_AFTER_SAVE, $args);
  }

  static function registerGlobalOnBeforeUpdateCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_BEFORE_UPDATE, $args);
  }

  static function registerGlobalOnUpdateCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_UPDATE, $args);
  }

  static function registerGlobalOnAfterUpdateCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_AFTER_UPDATE, $args);
  }

  static function registerGlobalOnBeforeCreateCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_BEFORE_CREATE, $args);
  }

  static function registerGlobalOnCreateCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_CREATE, $args);
  }

  static function registerGlobalOnAfterCreateCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_AFTER_CREATE, $args);
  }

  static function registerGlobalOnBeforeDestroyCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_BEFORE_DESTROY, $args);
  }

  static function registerGlobalOnAfterDestroyCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_AFTER_DESTROY, $args);
  }

  protected function _invokeListeners($type)
  {
    if(isset($this->_listeners[$type]))
      lmbDelegate :: invokeAll($this->_listeners[$type], array($this));

    if(isset(self :: $_global_listeners[$type]))
      lmbDelegate :: invokeAll(self :: $_global_listeners[$type], array($this));
  }

  function __wakeup()
  {
    $toolkit = lmbToolkit :: instance();
    $this->_db_conn = $toolkit->getDbConnectionByDsn($this->_db_conn_dsn);

    $this->_db_meta_info = $toolkit->getActiveRecordMetaInfo($this, $this->_db_conn);
    $this->_db_table_fields = $this->_db_meta_info->getDbColumnsNames();

    $this->_db_table = $this->_db_meta_info->getDbTable();
    $this->_db_table->setPrimaryKeyName($this->_primary_key_name);
    $this->_db_table_name = $this->_db_table->getTableName();
  }

  function __sleep()
  {
    $vars = array_keys(get_object_vars($this));
    $vars = array_diff($vars, array('_db_conn', '_db_table', '_db_meta_info'));
    return $vars;
  }
}


