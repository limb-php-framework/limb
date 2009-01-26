<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/web_app/src/controller/lmbController.class.php');

/**
 * abstract class AdminNodeController.
 *
 * @package cms
 * @version $Id: AdminNodeController.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
abstract class AdminNodeController extends lmbController
{
  protected $_form_id = 'node_form';
  protected $_controller_name = 'node';
  protected $_node_class_name = 'lmbCmsNode';

  protected $node = null;

  protected function _initNode($id = null)
  {
    $this->node = new $this->_node_class_name($id);
  }

  function doCreate()
  {
    $this->node = new $this->_node_class_name();
    $this->useForm($this->_form_id);
    $this->setFormDatasource($this->request);

    if($this->request->hasPost())
    {
      $this->node->setControllerName($this->_controller_name);
      $this->_import();

      if($this->request->get('auto_identifier'))
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
    $this->useForm($this->_form_id);
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
  }

  protected function _validateAndSave($is_create = false)
  {
    $this->_onBeforeValidate();
    $this->node->validate($this->error_list);
    $this->_onAfterValidate();

    if($this->error_list->isValid())
    {
      if($is_create)
        $this->_onBeforeCreate();
      else
        $this->_onBeforeEdit();

      $this->_onBeforeSave();
      $this->node->saveSkipValidation();
      $this->_onAfterSave();

      if($is_create)
        $this->_onAfterCreate();
      else
        $this->_onAfterEdit();

      $this->closePopup();
    }
  }

  protected function _initCreateForm(){}

  protected function _initEditForm()
  {
    $this->request->merge($this->node->export());
    $this->request->set('node', $this->node);
  }

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

  function doDelete()
  {
    if($this->request->hasPost())
      $this->_onBeforeDelete();
    $this->performCommand('limb/cms/src/command/lmbCmsDeleteNodeCommand');
    if($this->request->hasPost())
      $this->_onAfterDelete();
  }
}


