<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActiveRecord.class.php 5855 2007-05-10 10:30:43Z pachanga $
 * @package    active_record
 */
lmb_require('limb/core/src/lmbObject.class.php');
lmb_require('limb/core/src/lmbDelegate.class.php');
lmb_require('limb/dbal/src/lmbTableGateway.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLCriteria.class.php');
lmb_require('limb/dbal/src/drivers/lmbDbTypeInfo.class.php');
lmb_require('limb/validation/src/exception/lmbValidationException.class.php');
lmb_require('limb/validation/src/lmbValidator.class.php');
lmb_require('limb/validation/src/lmbErrorList.class.php');
lmb_require('limb/core/src/lmbCollection.class.php');
lmb_require('limb/active_record/src/lmbARException.class.php');
lmb_require('limb/active_record/src/lmbARNotFoundException.class.php');
lmb_require('limb/active_record/src/lmbARRecordSetDecorator.class.php');
lmb_require('limb/active_record/src/lmbAROneToManyCollection.class.php');
lmb_require('limb/active_record/src/lmbARManyToManyCollection.class.php');

/**
 * Base class responsible for ActiveRecord design pattern implementation. Inspired by Rails ActiveRecord class.
 *
 * @version $Id: lmbActiveRecord.class.php 5855 2007-05-10 10:30:43Z pachanga $
 */
class lmbActiveRecord extends lmbObject
{
  /**
   * @var string database column name used to store object class name for single table inheritance
   */
  protected static $_inheritance_field = 'kind';
  /**
   * @var array global event listeners which receieve events from ALL lmbActiveRecord instances
   */
  protected static $_global_listeners = array();
  /**
   * @var object lmbTableGateway instance used to access underlying db table
   */
  protected $_db_table;
  /**
   * @var string name of class database table to store instance fields, if not set lmbActiveRecord tries to guess it
   */
  protected $_db_table_name;
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
  protected $_default_sort_params;
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

  /**
   * @var array event listeners attached to the concrete object instance
   */
  protected $_listeners = array();

  /**
   * Note, this property is not guarded with "_" prefix since we need it to be imported/exported
   * @var array An array of attached value objects
   */
  protected $raw_value_objects = array();

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
  function __construct($magic_params = null)
  {
    parent :: __construct();

    $this->_db_meta_info = lmbToolkit :: instance()->getActiveRecordMetaInfo($this);

    $this->_db_table = $this->_db_meta_info->getDbTable();
    $this->_db_table_name = $this->_db_table->getTableName();

    if(is_int($magic_params))
      $this->loadById($magic_params);
    elseif(is_array($magic_params) || is_object($magic_params))
      $this->import($magic_params);
  }

  /**
   *  Sets database resource identifier used for database access
   *  @param string DSN, e.g. mysql://root:secret@localhost/mydb
   */
  static function setDefaultDSN($dsn)
  {
    lmb_require('limb/dbal/toolkit.inc.php');
    lmbToolkit :: instance()->setDefaultDbDSN($dsn);
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
    if($this->_error_list)
      return $this->_error_list;

    $this->_error_list = new lmbErrorList();
    return $this->_error_list;
  }
  /**
   *  Returns relation info array defined during class declaration
   *  @return array
   */
  function getRelationInfo($relation)
  {
    $relations = $this->_getAllRelations();
    if(isset($relations[$relation]))
      return $relations[$relation];
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
   *  Returns default sort params
   *  @return array
   */
  function getDefaultSortParams()
  {
    if(!$this->_default_sort_params)
      $this->_default_sort_params = array($this->_db_table_name . '.id' => 'ASC');

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
   *  @return object
   */
  protected function _createValidator()
  {
    return new lmbValidator();
  }
  /**
   *  Returns validator for create operations only.
   *  @see _createValidator()
   *  @return object
   */
  protected function _createInsertValidator()
  {
    return $this->_createValidator();
  }
  /**
   *  Returns validator for update operations only.
   *  @see _createValidator()
   *  @return object
   */
  protected function _createUpdateValidator()
  {
    return $this->_createValidator();
  }

  protected function _savePreRelations()
  {
    foreach($this->_has_one as $property => $info)
      $this->_savePreRelationObject($property, $info);

    foreach($this->_many_belongs_to as $property => $info)
      $this->_savePreRelationObject($property, $info);
  }

  protected function _savePreRelationObject($property, $info)
  {
    $object = $this->_getRaw($property);
    if(is_object($object))
    {
      $object->save($this->getErrorList());
      $object_id = $object->getId();
      if($this->_getRaw($info['field']) != $object_id)
        $this->_setRaw($info['field'], $object->getId());
    }
    elseif(is_null($object) && $this->isDirtyProperty($property) &&
           isset($info['can_be_null']) && $info['can_be_null'])
      $this->_setRaw($info['field'], null);
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
      $collection->save($this->getErrorList());
  }

  protected function _savePostRelationObject($property, $info)
  {
    $object = $this->_getRaw($property);
    if(is_object($object))
    {
      $object->set($info['field'], $this->getId());
      $object->save($this->getErrorList());
    }
  }

  protected function __call($method, $args = array())
  {
    if($property = $this->_mapGetToProperty($method))
      return $this->get($property);

    if($property = $this->mapAddToProperty($method))
    {
      $this->_addToProperty($property, $args[0]);
      return;
    }
    return parent :: __call($method, $args);
  }

  protected function _addToProperty($property, $value)
  {
    $collection = $this->get($property);
    if(!is_object($collection))
      throw new lmbARException("Collection object info for property '$property' is missing");

    $collection->add($value);
  }

  protected function _izLazyAttribute($property)
  {
    return in_array($property, $this->_lazy_attributes);
  }

  protected function _hasLazyAttributes()
  {
    if(!$this->_lazy_attributes)
      return false;

    foreach($this->_lazy_attributes as $attribute)
      if(!$this->hasAttribute($attribute))
        return true;

    return false;
  }

  protected function _loadLazyAttribute($property)
  {
    $record = $this->_db_table->selectRecordById($this->getId(), array($property));
    $processed = $this->_decodeDbValues($record);
    $this->_setDbValue($property, $processed[$property]);
  }

  protected function _loadLazyAttributes()
  {
    foreach($this->_lazy_attributes as $attribute)
      $this->_loadLazyAttribute($attribute);
  }
  /**
   *  Generic magic getter for any attribute
   *  @param string property name
   *  @return mixed
   */
  function get($property)
  {
    if(!$this->isNew() && $this->_izLazyAttribute($property) && !$this->hasAttribute($property))
      $this->_loadLazyAttribute($property);

    if($this->_hasValueObjectRelation($property))
      return $this->_getValueObject($property);

    $value = parent :: get($property);

    if(isset($value))
      return $value;

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
    $exists = false;
  }

  function createRelationCollection($relation, $criteria = null)
  {
    $info = $this->getRelationInfo($relation);

    if(isset($info['collection']))
      return new $info['collection']($relation, $this, $criteria);
    elseif($this->_hasOneToManyRelation($relation))
      return new lmbAROneToManyCollection($relation, $this, $criteria);
    else if($this->_hasManyToManyRelation($relation))
      return new lmbARManyToManyCollection($relation, $this, $criteria);
  }

  protected function _hasCollectionRelation($relation)
  {
    return $this->_hasOneToManyRelation($relation) ||
           $this->_hasManyToManyRelation($relation);
  }
  /**
   *  Generic magis getter for any attribute
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
      parent :: set($property, $value);
  }

  protected function _setRaw($property, $value)
  {
    parent :: _setRaw($property, $value);

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
    foreach($this->_getAllRelations() as $property => $info)
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

  protected function _hasValueObjectRelation($property)
  {
    return isset($this->_composed_of[$property]);
  }

  protected function _loadBelongsToObject($property)
  {
    return lmbActiveRecord :: findFirst($this->_belongs_to[$property]['class'],
                                        array('criteria' => $this->_belongs_to[$property]['field'] . ' = ' . (int)$this->getId()));
  }

  protected function _loadManyBelongsToObject($property)
  {
    $value = $this->_getRaw($this->_many_belongs_to[$property]['field']);
    if(!$value && $this->_canManyBelongsToObjectBeNull($property))
      return null;

    return lmbActiveRecord :: findById($this->_many_belongs_to[$property]['class'],
                                       $this->get($this->_many_belongs_to[$property]['field']));
  }

  protected function _loadOneToOneObject($property)
  {
    $value = $this->_getRaw($this->_has_one[$property]['field']);
    if(!$value && $this->_canHasOneObjectBeNull($property))
      return null;

    return lmbActiveRecord :: findById($this->_has_one[$property]['class'],
                                       $this->get($this->_has_one[$property]['field']));
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

  protected function _loadValueObject($property)
  {
    if(!isset($this->raw_value_objects[$this->_composed_of[$property]['field']]))
      return null;

    $value = $this->raw_value_objects[$this->_composed_of[$property]['field']];

    return $this->_createValueObject($this->_composed_of[$property]['class'],
                                     $value);
  }

  protected function _createValueObject($class, $value)
  {
    $object = new $class($value);
    return $object;
  }

  protected function _mapMethodToClass($method)
  {
    return substr($method, 3);
  }

  protected function _getValueObject($property)
  {
    $value = $this->_getRaw($property);
    if(!is_object($value))
    {
      $object = $this->_loadValueObject($property);
      $this->_setRaw($property, $object);
      return $object;
    }
    return $value;
  }

  protected function _doSave($need_validation)
  {
    if($this->_is_being_saved)
      return;

    $this->_is_being_saved = true;

    $this->_savePreRelations();

    $this->_onBeforeSave();

    $this->_invokeListeners(self :: ON_BEFORE_SAVE);

    if(!$this->isNew() && $this->isDirty())
    {
      $this->_onBeforeUpdate();

      $this->_invokeListeners(self :: ON_BEFORE_UPDATE);

      if($need_validation && !$this->_validateUpdate())
        throw new lmbValidationException('ActiveRecord "' . get_class($this) . '" validation failed',
                                         $this->getErrorList());

      $this->_onSave();

      $this->_onUpdate();

      $this->_invokeListeners(self :: ON_UPDATE);

      $this->_updateDbRecord($this->_propertiesToDbFields());

      $this->_onAfterUpdate();

      $this->_invokeListeners(self :: ON_AFTER_UPDATE);
    }
    elseif($this->isNew())
    {
      $this->_onBeforeCreate();

      $this->_invokeListeners(self :: ON_BEFORE_CREATE);

      if($need_validation && !$this->_validateInsert())
        throw new lmbValidationException('ActiveRecord "' . get_class($this) . '" validation failed',
                                         $this->getErrorList());

      $this->_onSave();

      $this->_onCreate();

      $this->_invokeListeners(self :: ON_CREATE);

      $new_id = $this->_insertDbRecord($this->_propertiesToDbFields());
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

    return $this->getId();
  }

  protected function _updateDbRecord($values)
  {
    return $this->_db_table->updateById($this->id, $values);
  }

  protected function _insertDbRecord($values)
  {
    return $this->_db_table->insert($values);
  }

  protected function _propertiesToDbFields()
  {
    $fields = $this->export();

    if($this->isNew() && $this->_isInheritable())
      $fields[self :: $_inheritance_field] = $this->_getInheritancePath();

    foreach($this->_composed_of as $property => $info)
    {
      $object = $this->_getValueObject($property);
      if(is_object($object))
      {
        $method = $info['getter'];
        $fields[$info['field']] = $object->$method();
      }
    }
    return $fields;
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
    $this->remove('id');
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
    $validator->setErrorList($this->getErrorList());
    return $validator->validate($this);
  }

  protected static function _isCriteria($params)
  {
    if(is_object($params) || is_string($params))
      return true;

    if(is_array($params) && sizeof($params))
    {
      foreach($params as $key => $value)
      {
        if(!is_int($key) || $value == 'first')
          return false;
      }
      return true;
    }
    return false;
  }

  /**
   *  Finds one instance of object in database, this method is actually a wrapper around find()
   *  @see find()
   *  @param string class name of the object
   *  @param mixed misc magic params
   *  @return object|null
   */
  static function findFirst($class_name, $magic_params = array())
  {
    $params = array();
    if(self :: _isCriteria($magic_params))
      $params = array('first', 'criteria' => $magic_params);
    elseif(is_array($magic_params))
    {
      $params = $magic_params;
      array_push($params, 'first');
    }
    if(!class_exists($class_name, true))
      throw new lmbARException("Could not find class '$class_name'");

    $obj = new $class_name();
    return $obj->_findFirst($params);
  }
  /**
   *  lmbActiveRecord :: findFirst() convenience alias
   *  @see findFirst()
   *  @param string class name of the object
   *  @param mixed misc magic params
   *  @return object|null
   */
  static function findOne($class_name, $magic_params = array())
  {
    return self :: findFirst($class_name, $magic_params);
  }
  /**
   *  Userland filter for findFirst() static method
   *  @see findFirst()
   *  @param mixed misc magic params
   *  @return object|null
   */
  protected function _findFirst($params)
  {
    return lmbActiveRecord :: find(get_class($this), $params);
  }
  /**
   *  Finds one instance of object in database using object id, this method is actually a wrapper around find()
   *  @see find()
   *  @param string class name of the object
   *  @param integer object id
   *  @return object|null
   */
  static function findById($class_name, $id)
  {
    if(!class_exists($class_name, true))
      throw new lmbARException("Could not find class '$class_name'");

    $obj = new $class_name();
    return $obj->_findById($id);
  }
  /**
   *  Userland filter for findById() static method
   *  @see findById()
   *  @param integer object id
   *  @return object
   */
  protected function _findById($id)
  {
    if($object = lmbActiveRecord :: find(get_class($this), array('first', 'criteria' => 'id=' . (int)$id)))
      return $object;
    else
      throw new lmbARNotFoundException(get_class($this), $id);
  }
  /**
   *  Finds a collection of objects in database using array of object ids, this method is actually a wrapper around find()
   *  @see find()
   *  @param string class name of the object
   *  @param array object ids
   *  @param mixed misc magic params
   *  @return iterator
   */
  static function findByIds($class_name, $ids, $params = array())
  {
    if(!class_exists($class_name, true))
      throw new lmbARException("Could not find class '$class_name'");

    $obj = new $class_name();
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
      $params['criteria'] = new lmbSQLFieldCriteria('id', $ids, lmbSQLFieldCriteria :: IN);
      return lmbActiveRecord :: find(get_class($this), $params);
    }
  }
  /**
   *  Implements WACT template datasource component interface, this method simply calls find()
   *  @see find()
   *  @param mixed misc magic params
   *  @return iterator
   */
  function getDataset($magic_params = array())
  {
    return lmbActiveRecord :: find(get_class($this), $magic_params);
  }
  /**
   *  Finds a collection of objects in database using raw SQL
   *  @param string class name of the object
   *  @param string SQL
   *  @return iterator
   */
  static function findBySql($class_name, $sql)
  {
    $conn = lmbToolkit :: instance()->getDefaultDbConnection();
    $stmt = $conn->newStatement($sql);
    return lmbActiveRecord :: decorateRecordSet($stmt->getRecordSet(), $class_name);
  }
  /**
   *  Finds first object in database using raw SQL
   *  @param string class name of the object
   *  @param string SQL
   *  @return object
   */
  static function findFirstBySql($class_name, $sql)
  {
    $rs = self :: findBySql($class_name, $sql);
    $rs->paginate(0, 1);
    $rs->rewind();
    if($rs->valid())
      return $rs->current();
  }
  /**
   *  Alias for findFirstBySql
   *  @see findFirstBySql()
   *  @return object
   */
  static function findOneBySql($class_name, $sql)
  {
    return self :: findFirstBySql($class_name, $sql);
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
   *  $books = lmbActiveRecord :: find('Book', array('criteria' => 'name="hey"',
   *                                                 'sort' => array('id' => 'desc'),
   *                                                 'limit' => 3));
   *  //returns a collection of all Book objects in database
   *  $books = lmbActiveRecord :: find('Book');
   *  //returns one object with specified id
   *  $books = lmbActiveRecord :: find('Book', 1);
   *  //returns a collection of objects which match plain text criteria
   *  $books = lmbActiveRecord :: find('Book', 'name="hey"');
   *  //returns a collection of objects which match criteria with placeholders
   *  $books = lmbActiveRecord :: find('Book', array('name=? and author=?', 'hey', 'bob'));
   *  //returns a collection of objects which match object criteria
   *  $books = lmbActiveRecord :: find('Book',
   *                                    new lmbSQLFieldCriteria('name', 'hey'));
   *  </code>
   *  @param string class name of the object
   *  @param mixed misc magic params
   *  @return iterator
   */
  static function find($class_name, $magic_params = array())
  {
    if(self :: _isCriteria($magic_params))
      $params = array('criteria' => $magic_params);
    elseif(is_int($magic_params))
      return self :: findById($class_name, $magic_params);
    elseif(!is_array($magic_params))
      throw new lmbARException("Invalid magic params", array($magic_params));
    else
      $params = $magic_params;

    if(!class_exists($class_name, true))
      throw new lmbARException("Could not find class '$class_name'");

    $obj = new $class_name();
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
    $criteria = isset($params['criteria']) ? $params['criteria'] : null;
    $sort_params = isset($params['sort']) ? $params['sort'] : array();
    $rs = $this->decorateRecordSet($this->findAllRecords($criteria, $sort_params));

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
    if(!count($sort_params))
      $sort_params = $this->_default_sort_params;

    return $this->_db_table->select($this->addClassCriteria($criteria), $sort_params, $this->_getColumnsForSelect());
  }
  /**
   *  Adds class name criterion to passed in criteria
   *  @param string|object criteria
   *  @return object
   */
  function addClassCriteria($criteria)
  {
    if($this->_isInheritable())
      return lmbSQLCriteria :: objectify($criteria)->addAnd(array(self :: $_inheritance_field .
                                                                  $this->getInheritanceCondition()));

    return $criteria;
  }

  function getInheritanceCondition()
  {
    return ' LIKE "' . $this->_getInheritancePath() . '%"';
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
    return end(self :: decodeInheritancePath($obj[self :: $_inheritance_field]));
  }

  /**
   *  Loads current object with data from database, overwrites any previous data, marks object dirty and unsets new status
   *  @param integer object id
   */
  function loadById($id)
  {
    $object = lmbActiveRecord :: findById(get_class($this), $id);
    $this->importRaw($object->exportRaw());
    $this->_resetDirty();
    $this->_is_new = false;
  }
  /**
   *  Loads current object with data from database record, overwrites any previous data, marks object dirty and unsets new status
   *  @param object database record object
   */
  function loadFromRecord($record)
  {
    $decoded = $this->_decodeDbValues($record);

    foreach($decoded as $key => $value)
      $this->_setDbValue($key, $value);

    $this->_resetDirty();
    $this->_is_new = false;
    return true;
  }

  protected function _setDbValue($key, $value)
  {
    if($this->_hasValueObjectRelation($key))
      $this->raw_value_objects[$key] = $value;
    else
      parent :: _setRaw($key, $value);
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
    if($id = $this->_getRaw('id'))
      return (int)$id;
  }
  /**
   *  Sets id of an object typecasted to integer explicitly, be carefull using this method since
   *  it may break relations if used improperly
   *  @param integer
   */
  function setId($id)
  {
    $this->_setRaw('id', (int)$id);
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
   */
  static function delete($class_name, $criteria = null)
  {
    $params = array();
    if($criteria)
      $params = array('criteria' => $criteria);

    $rs = lmbActiveRecord :: find($class_name, $params);
    foreach($rs as $object)
      $object->destroy();
  }

  static function update($class_name, $set, $criteria = null)
  {
    $object = new $class_name();
    $db_table = $object->getDbTable();
    $db_table->update($set, $criteria);
  }

  protected function _getColumnsForSelect()
  {
    return $this->_db_table->getColumnsForSelect('', $this->_lazy_attributes);
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
      if($collection = $this->get($property))
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

  protected function _createSQLStatement($sql)
  {
    $conn = lmbToolkit :: instance()->getDefaultDbConnection();
    return $conn->newStatement($sql);
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
   *  corresponding lmbActiveRecord object. This method can be used both statically and as
   *  instance. If used statically you must pass $class argument.
   *  @see lmbARRecordSetDecorator
   *  @todo Split this method - it is a hack since it can be called both from instance and statically
   *  @param iterator
   *  @param string
   */
  function decorateRecordSet($rs, $class = null)
  {
    return new lmbARRecordSetDecorator($rs, $class ? $class : get_class($this));
  }

  function __clone()
  {
    $this->remove('id');
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
        $this->_importValueObject($property, $value);
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
  }
  /**
   *  Plain import of data into object
   *  @see lmbObject::import()
   *  @param array
   */
  function importRaw($source)
  {
    parent :: import($source);
  }

  protected function _canImportProperty($property)
  {
    if($this->isNew())
      return true;

    if($property == 'id')
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
          $objects[] = new $class((int)$item);
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
      $obj = new $class((int)$value);
      $this->set($property, $obj);
    }
    elseif(is_object($value))
      $this->set($property, $value);
    elseif(is_null($value) || strcasecmp($value, 'null') === 0)
      $this->set($property, null);
  }

  protected function _importValueObject($property, $obj)
  {
    if(!is_object($obj))
      $this->set($property, $this->_createValueObject($this->_composed_of[$property]['class'], $obj));
    else
      $this->set($property, $obj);
  }
  /**
   *  Exports object data with lazy properties resolved
   *  @return array
   */
  function export()
  {
    if(!$this->isNew() && $this->_hasLazyAttributes())
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

  function registerGlobalOnBeforeSaveCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_BEFORE_SAVE, $args);
  }

  function registerGlobalOnAfterSaveCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_AFTER_SAVE, $args);
  }

  function registerGlobalOnBeforeUpdateCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_BEFORE_UPDATE, $args);
  }

  function registerGlobalOnUpdateCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_UPDATE, $args);
  }

  function registerGlobalOnAfterUpdateCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_AFTER_UPDATE, $args);
  }

  function registerGlobalOnBeforeCreateCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_BEFORE_CREATE, $args);
  }

  function registerGlobalOnCreateCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_CREATE, $args);
  }

  function registerGlobalOnAfterCreateCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_AFTER_CREATE, $args);
  }

  function registerGlobalOnBeforeDestroyCallback($callback)
  {
    $args = func_get_args();
    self :: registerGlobalCallback(self :: ON_BEFORE_DESTROY, $args);
  }

  function registerGlobalOnAfterDestroyCallback($callback)
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
}

?>
