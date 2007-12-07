<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/web_app/src/controller/lmbController.class.php');
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');

/**
 * abstract class AdminNodeWithObjectController.
 *
 * @package cms
 * @version $Id$
 */
abstract class AdminNodeWithObjectController extends lmbController
{
  protected $_form_name = 'object_form';
  protected $_controller_name = '';
  protected $_node_class_name = 'lmbCmsNode';
  protected $_object_class_name = '';
  protected $_generate_identifier = false;

  protected $node = null;
  protected $item = null;

  function __construct()
  {
    parent :: __construct();

    if(!$this->_object_class_name || !$this->_controller_name)
      throw new lmbException('Object class name or(and) controller name is not specified');
  }

  function doCreate()
  {
    $this->node = new $this->_node_class_name();
    $this->item = new $this->_object_class_name();

    $this->useForm($this->_form_name);
    $this->setFormDatasource($this->request);

    if($this->request->hasPost())
    {
      $this->node->setControllerName($this->_controller_name);
      $this->node->setObject($this->item);
      $this->item->setNode($this->node);
      $this->_import();

      if($this->_generate_identifier || $this->request->get('auto_identifier'))
        $this->node->setIdentifier(lmbCmsNode :: generateIdentifier($this->request->get('parent')));

      $this->_validateAndSave(true);
    }
    else
    {
      $this->_initCreateForm();
    }
  }

  function doEdit()
  {
    $this->node = lmbActiveRecord :: findById($this->_node_class_name, $this->request->getInteger('id'));
    $this->item = $this->node->getObject();
    $this->useForm($this->_form_name);
    $this->setFormDatasource($this->request);

    if($this->request->hasPost())
    {
      $this->_import();
      $this->_validateAndSave(false);
    }
    else
    {
      $this->_initEditForm();
    }
  }

  protected function _import()
  {
    $this->node->import($this->request);
    $this->item->import($this->request);
  }

  protected function _validateAndSave($is_create = false)
  {
    $this->_onBeforeValidate();
    $this->node->validate($this->error_list);
    $this->item->validate($this->error_list);
    $this->_onAfterValidate();

    if($this->error_list->isValid())
    {
      if($is_create)
        $this->_onBeforeCreate();
      else
        $this->_onBeforeEdit();

      $this->_onBeforeSave();
      $this->node->saveSkipValidation();
      $this->item->saveSkipValidation();
      $this->_onAfterSave();

      if($is_create)
        $this->_onAfterCreate();
      else
        $this->_onAfterEdit();

      $this->closePopup();
    }
  }

  protected function _initEditForm()
  {
    $this->request->merge($this->node->export());
    $this->request->merge($this->item->export());
    $this->request->set('node', $this->node);
    $this->request->set('item', $this->item);
  }

  function performPublishCommand()
  {
    $this->performCommand('limb/cms/src/command/lmbCmsPublishObjectCommand', $this->_object_class_name);
  }

  function performUnpublishCommand()
  {
    $this->performCommand('limb/cms/src/command/lmbCmsUnpublishObjectCommand', $this->_object_class_name);
  }

  function doDelete()
  {
    if($this->request->hasPost())
      $this->_onBeforeDelete();
    $this->performCommand('limb/cms/src/command/lmbCmsDeleteNodeCommand');
    if($this->request->hasPost())
      $this->_onAfterDelete();
  }

  protected function _initCreateForm() {}
  protected function _onBeforeSave() {}
  protected function _onAfterSave() {}
  protected function _onBeforeCreate() {}
  protected function _onAfterCreate() {}
  protected function _onBeforeEdit() {}
  protected function _onAfterEdit() {}
  protected function _onBeforeDelete() {}
  protected function _onAfterDelete() {}
  protected function _onBeforeValidate() {}
  protected function _onAfterValidate() {}
}


