<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsNode.class.php 5725 2007-04-20 11:21:43Z pachanga $
 * @package    cms
 */
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/tree/src/tree/lmbMaterializedPathTree.class.php');
lmb_require('limb/cms/src/model/lmbCmsClassName.class.php');
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');

class lmbCmsNode extends lmbActiveRecord
{
  protected $_db_table_name = 'node';
  protected $_default_sort_params = array('priority' => 'ASC');
  protected $_is_being_destroyed = false;

  protected $object;
  protected $url_path;
  protected $tree;
  protected $controller_name;

  protected $_has_one = array('parent' => array('field' => 'parent_id',
                                                'class' => 'lmbCmsNode',
                                                'can_be_null' => true,
                                                'cascade_delete' => false));

  protected $_has_many = array('kids' => array('field' => 'parent_id',
                                               'class' => 'lmbCmsNode'));

  function __construct($magic_params = null)
  {
    $this->tree = lmbToolkit :: instance()->getCmsTree();

    parent :: __construct($magic_params);
  }

  protected function _createValidator()
  {
    $validator = new lmbValidator();
    $validator->addRequiredRule('title');
    $validator->addRequiredRule('identifier');
    return $validator;
  }

  protected function _onAfterSave()
  {
    if(is_object($this->object))
    {
      $this->object->registerOnAfterSaveCallback($this, 'updateNodeToObjectLink');
      $this->object->save($this->_error_list);
    }
  }

  function updateNodeToObjectLink($object)
  {
    $this->_setRaw('object_id', $object_id = $object->getId());
    $this->_setRaw('object_class_id', $object_class_id = lmbCmsClassName :: generateIdFor($object));
    $this->_updateDbRecord(array('object_id' => $object_id,
                                 'object_class_id' => $object_class_id));
  }

  protected function _insertDbRecord($values)
  {
    if($this->getParent() && $parent_id = $this->getParent()->getId())
      return $this->tree->createNode($parent_id, $values);
    else
    {
      if(!$root_id = $this->tree->getRootNode())
        $root_id = $this->tree->initTree();

      return $this->tree->createNode($root_id, $values);
    }
  }

  protected function _updateDbRecord($values)
  {
    return $this->tree->updateNode($this->getId(), $values);
  }

  protected function _onBeforeDestroy()
  {
    if($object = $this->getObject())
    {
      $object->node = $this;
      $object->destroy();
    }
  }

  protected function _deleteDbRecord()
  {
    $this->tree->deleteNode($this->getId());
  }

  static function findByPath($class_name, $path)
  {
    $object = new $class_name();
    if($object->loadByPath($path))
      return lmbActiveRecord :: findById('lmbCmsNode', $object->getId());
  }

  function loadByPath($path)
  {
    if(!$node = $this->tree->getNodeByPath($path))
      return false;

    $this->import($node);
    return true;
  }

  function getObject()
  {
    if(!isset($this->object_id) || !$this->object_id)
      return null;

    $class_name = lmbActiveRecord :: findById('lmbCmsClassName', $this->object_class_id);
    return lmbActiveRecord :: findById($class_name->title, $this->object_id);
  }

  function getControllerName()
  {
    if(!$this->controller_id)
      return '';

    if(!$this->controller_name)
    {
      $class_name = lmbActiveRecord :: findById('lmbCmsClassName', $this->controller_id);
      $this->controller_name = $class_name->title;
    }

    return $this->controller_name;
  }

  function setControllerName($controller_name)
  {
    $this->controller_name = $controller_name;
    $this->_setRaw('controller_id', $controler_id = lmbCmsClassName :: generateIdFor($this->controller_name));
  }

  function getUrlPath()
  {
    if(isset($this->url_path))
      return $this->url_path;

    if(!($parent_path = $this->tree->getPathToNode($this->parent_id)))
      return '/' . $this->getIdentifier();

    $this->url_path = rtrim($parent_path, '/') . '/' . $this->getIdentifier();
    return $this->url_path;
  }

  function getParents()
  {
    return $this->decorateRecordSet($this->tree->getParents($this->getId()));
  }

  function getRoots()
  {
    return lmbActiveRecord :: decorateRecordSet($this->tree->getChildren('/'), 'lmbCmsNode');
  }

  function getRootNodes()
  {
    return $this->getRoots();
  }

  function generateIdentifier($parent_id)
  {
    $identifier = lmbToolkit :: instance()->getCmsTree()->getMaxChildIdentifier($parent_id);

    if($identifier === false)
      return 1;

    if(preg_match('/(.*?)(\d+)$/', $identifier, $matches))
      $new_identifier = $matches[1] . ($matches[2] + 1);
    else
      $new_identifier = $identifier . '1';

    return $new_identifier;
  }
}

?>
