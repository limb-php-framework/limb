<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    $package$
 */
lmb_require('limb/web_app/src/controller/lmbController.class.php');
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');

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

      $this->_validateAndSave();
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
      $this->_validateAndSave();
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

  protected function _validateAndSave()
  {
    $this->node->validate($this->error_list);
    $this->item->validate($this->error_list);

    $this->_onBeforeSave();

    if($this->error_list->isValid())
    {
      $this->node->saveSkipValidation();
      $this->item->saveSkipValidation();
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
    $this->performCommand('limb/cms/src/command/lmbCmsDeleteNodeCommand');
  }

  protected function _initCreateForm() {}
  protected function _onBeforeSave() {}
}

?>
