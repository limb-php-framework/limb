<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/web_app/src/controller/lmbController.class.php');

/**
 * abstract class AdminObjectController.
 *
 * @package cms
 * @version $Id$
 */
abstract class AdminObjectController extends lmbController
{
  protected $_form_name = 'object_form';
  protected $_object_class_name = '';
  protected $_popup = true;
  protected $_back_url = array();

  protected $item = null;

  function __construct()
  {
    parent :: __construct();

    if(!$this->_object_class_name)
      throw new lmbException('Object class name is not specified');
  }

  protected function _passLocalAttributesToView()
  {
    //passing back_url string into view
    if(is_array($this->_back_url))
      $this->back_url = $this->toolkit->getRoutesUrl($this->_back_url);
    else
      $this->back_url = $this->_back_url;

    parent :: _passLocalAttributesToView();
  }

  function doCreate()
  {
    $this->item = new $this->_object_class_name();

    $this->useForm($this->_form_name);
    $this->setFormDatasource($this->item);

    if($this->request->hasPost())
    {
      $this->_import();
      $this->_validateAndSave(true);
    }
    else
    {
      $this->_initCreateForm();
    }
  }

  function doEdit()
  {
    $this->item = lmbActiveRecord :: findById($this->_object_class_name, $this->request->getInteger('id'));
    $this->useForm($this->_form_name);
    $this->setFormDatasource($this->item);

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

  function doDelete()
  {
    if($this->request->hasPost())
      $this->_onBeforeDelete();

    if($this->request->get('delete'))
    {
      foreach($this->request->getArray('ids') as $id)
      {
        $item = new $this->_object_class_name((int)$id);
        $item->destroy();
      }
      $this->_endDialog();
      $this->_onAfterDelete();
    }
  }

  function performPublishCommand()
  {
    $this->performCommand('limb/cms/src/command/lmbCmsPublishObjectCommand', $this->_object_class_name);
  }

  function performUnpublishCommand()
  {
    $this->performCommand('limb/cms/src/command/lmbCmsUnpublishObjectCommand', $this->_object_class_name);
  }

  protected function _import()
  {
    $this->item->import($this->request);
  }

  protected function _validateAndSave($is_create = false)
  {
    $this->_onBeforeValidate();
    $this->item->validate($this->error_list);
    $this->_onAfterValidate();

    if($this->error_list->isValid())
    {
      if($is_create)
        $this->_onBeforeCreate();
      else
        $this->_onBeforeEdit();

      $this->_onBeforeSave();
      $this->item->saveSkipValidation();
      $this->_onAfterSave();

      if($is_create)
        $this->_onAfterCreate();
      else
        $this->_onAfterEdit();

      $this->_endDialog();
    }
  }

  protected function _endDialog()
  {
    if($this->_popup)
      $this->closePopup();
    else
      $this->redirect($this->_back_url);
  }

  protected function _initCreateForm() {}
  protected function _initEditForm() {}
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


