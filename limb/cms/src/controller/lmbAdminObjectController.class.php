<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cms/src/controller/lmbObjectController.class.php');
lmb_require('limb/datetime/src/lmbDateTime.class.php');

/**
 * abstract class AdminObjectController.
 *
 * @package cms
 * @version $Id$
 */
abstract class lmbAdminObjectController extends lmbObjectController
{
  protected $_form_name = 'object_form';
  protected $_popup = true;
  protected $_back_url = array();

  protected function _passLocalAttributesToView()
  {
    //passing back_url string into view
    if(is_array($this->_back_url))
      $this->back_url = $this->toolkit->getRoutesUrl($this->_back_url);
    else
      $this->back_url = $this->_back_url;

    parent :: _passLocalAttributesToView();
  }


  function doDisplay()
  {
    $this->items = lmbActiveRecord::find($this->_object_class_name);
    $this->_applySortParams();
  }

  protected function _applySortParams()
  {
    $sort = $this->toolkit->getRequest()->getGetFiltered('sort',FILTER_SANITIZE_SPECIAL_CHARS, false);

    $direction = $this->toolkit->getRequest()->getGet('direction');
    if(!in_array($direction, array('asc','desc')))
      $direction = 'asc';

    if($sort==false) return;
    $this->items->sort(array($sort=>$direction));
  }

  function doCreate()
  {
    $this->item = new $this->_object_class_name();
    $this->_onCreate();

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
    if(!$this->item = $this->_getObjectByRequestedId())
      return $this->forwardTo404();
    $this->_onUpdate();
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
    if(!$this->request->hasPost())
      $ids = $this->request->get('ids');
    else
      $ids = $this->request->getPost('ids');

    if(!is_array($ids))
      $ids = array($ids);

    $this->items = lmbActiveRecord::findByIds($this->_object_class_name, $ids);

    if(!$this->request->hasPost())
      return;

    $this->_onBeforeDelete();

    foreach ($this->items as $item)
      $item->destroy();

    $this->_onAfterDelete();

    $this->_endDialog();
  }

  function doRevertPublish()
  {
    if($this->request->has('ids'))
      $ids = $this->request->get('ids');
    elseif($this->request->has('id'))
      $ids = array($this->request->get('id'));
    else
      return;

    $info_object = new $this->_object_class_name();

    foreach($ids as $id)
      lmbDBAL :: execute('UPDATE ' . $info_object->getTableName() . ' SET is_published = IF(is_published > 0, 0, 1) WHERE id = ' . lmbToolkit :: instance()->getDefaultDbConnection()->escape($id));

    $this->_endDialog();
  }

  function doPublish()
  {
    if(!$item = $this->_getObjectByRequestedId())
      return $this->forwardTo404();

    $this->_onBeforePublish();
    $item->setIsPublished(1);
    $item->save();
    $this->_onAfterPublish();

    $this->_endDialog();
  }

  function doUnpublish()
  {
    if(!$item = $this->_getObjectByRequestedId())
      return $this->forwardTo404();

    $this->_onBeforeUnpublish();
    $item->setIsPublished(0);
    $item->save();
    $this->_onAfterUnpublish();

    $this->_endDialog();
  }

  function doPriority()
  {
    $this->_changeItemsPriority($this->_object_class_name);
    $this->_endDialog();
  }

  protected function _import()
  {
    $this->_onBeforeImport();
    $this->item->import($this->request);
    if($this->request->hasFiles()) {
	  foreach ($this->request->getFiles() as $field => $file)
	    $this->item->set($field, $file->getName());
    }
    $this->_onAfterImport();
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
        $this->_onBeforeUpdate();

      $this->_onBeforeSave();
      $this->item->saveSkipValidation();
      $this->_onAfterSave();

      if($is_create)
        $this->_onAfterCreate();
      else
        $this->_onAfterUpdate();

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

  protected function _changeItemsPriority($model, $where_field, $where_field_value)
  {
    $priority_items = $this->request->get('priority_items');

    $info_item = new $model();
    $sql = 'SELECT id, priority FROM ' . $info_item->getTableName() . ' WHERE ' . $where_field . '=' . $where_field_value;
    $current_priorities_object = lmbDBAL :: fetch($sql);
    $current_priorities_object = $current_priorities_object->getArray();

    $current_priorities = array();
    foreach($current_priorities_object as $item)
      $current_priorities[$item->get('id')] = $item->get('priority');

    foreach($priority_items as $id=>$priority)
      $current_priorities[$id] = $priority;

    asort($current_priorities);

    $i = 10;

    $table_name = $info_item->getTableName();

    foreach($current_priorities as $id => $priority)
    {
      $sql = "UPDATE " . $table_name . " SET priority='" . $i . "' WHERE id='". $id . "'";
      lmbDBAL :: execute($sql);
      $i += 10;
    }

  }

  function closePopup()
  {
    if(!$this->in_popup)
      return;

    $this->response->write('<html><script>if(self.parent){self.parent.focus();self.parent.location.reload();}</script></html>');
  }

  protected function _initCreateForm() {}
  protected function _initEditForm() {}

  protected function _onBeforeSave() {}
  protected function _onAfterSave() {}

  protected function _onBeforeCreate() {}
  protected function _onCreate() {}
  protected function _onAfterCreate() {}

  protected function _onBeforeUpdate() {}
  protected function _onUpdate() {}
  protected function _onAfterUpdate() {}

  protected function _onBeforeDelete() {}
  protected function _onAfterDelete() {}

  protected function _onBeforeValidate() {}
  protected function _onAfterValidate() {}

  protected function _onBeforeImport() {}
  protected function _onAfterImport() {}

  protected function _onBeforePublish() {}
  protected function _onAfterPublish() {}

  protected function _onBeforeUnpublish() {}
  protected function _onAfterUnpublish() {}
}
