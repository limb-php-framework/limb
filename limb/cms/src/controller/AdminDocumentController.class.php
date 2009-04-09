<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2009 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    cms
 */
lmb_require('limb/cms/src/controller/lmbAdminObjectController.class.php');
lmb_require('limb/cms/src/model/lmbCmsDocument.class.php');

class AdminDocumentController extends lmbAdminObjectController
{
  protected $_object_class_name = 'lmbCmsDocument';

  function doDisplay()
  {
    if(!$id = $this->request->getInteger('id')  ){
      $this->is_root = true;
      $criteria = new lmbSQLCriteria('parent_id > 0');
      $criteria->addAnd(new lmbSQLCriteria('level = 1'));
      $this->item = lmbCmsDocument :: findRoot();
    }
    else {
      $this->is_root = false;
      if(!$this->item = $this->_getObjectByRequestedId())
        return $this->forwardTo404();
      $criteria = new lmbSQLCriteria('parent_id = ' . $this->item->getId());
    }

    $this->items = lmbActiveRecord :: find($this->_object_class_name, array('criteria' => $criteria, 'sort'=>array('priority'=>'ASC')));
    $this->_applySortParams();
  }

  function doPriority()
  {
    if($this->request->has('parent_id'))
      $this->_changeItemsPriority('lmbCmsDocument', 'parent_id', $this->request->get('parent_id'));

    $this->_endDialog();
  }

  function doCreate()
  {
    if(!$this->parent = $this->_getObjectByRequestedId())
      $this->forwardTo404();

    $this->item = new $this->_object_class_name();

    $this->_onCreate();

    $this->useForm($this->_form_name);
    $this->setFormDatasource($this->item);

    if($this->request->hasPost())
    {
      $this->_import();
      $this->item->setParent($this->parent);
      $this->_validateAndSave($create = true);
    }
    else
      $this->_initCreateForm();
  }

  protected function _onBeforeImport()
  {
    $this->request->set('identifier', trim($this->request->get('identifier')));
    $this->request->set('title', trim($this->request->get('title')));
  }

  protected function _validateAndSave($is_create = false)
  {
    try
    {
      parent :: _validateAndSave($is_create);
    }
    catch (lmbException $e)
    {
      $this->error_list->addError('Документ со значением поля "Идентификатор" уже существует на данном уровне вложения');
    }
  }

}


