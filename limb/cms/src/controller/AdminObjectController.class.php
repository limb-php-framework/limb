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

  protected $item = null;

  function __construct()
  {
    parent :: __construct();

    if(!$this->_object_class_name)
      throw new lmbException('Object class name is not specified');
  }

  function doCreate()
  {
    $this->item = new $this->_object_class_name();

    $this->useForm($this->_form_name);
    $this->setFormDatasource($this->item);

    if($this->request->hasPost())
    {
      $this->_import();
      $this->_validateAndSave();
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
      $this->_validateAndSave();
    }
    else
    {
      $this->_initEditForm();
    }
  }

  protected function _import()
  {
    $this->item->import($this->request);
  }

  protected function _validateAndSave()
  {
    $this->item->validate($this->error_list);

    $this->_onBeforeSave();

    if($this->error_list->isValid())
    {
      $this->item->saveSkipValidation();
      $this->closePopup();
    }
  }

  protected function _initCreateForm() {}
  protected function _initEditForm() {}
  protected function _onBeforeSave() {}

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
    $this->performCommand('limb/cms/src/command/lmbCmsDeleteObjectCommand', $this->_object_class_name);
  }
}

?>
