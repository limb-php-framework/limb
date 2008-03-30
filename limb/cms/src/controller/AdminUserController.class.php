<?php
lmb_require('limb/web_app/src/controller/lmbController.class.php');
lmb_require('limb/validation/src/rule/lmbMatchRule.class.php');
lmb_require('limb/cms/src/model/lmbCmsUser.class.php');

class AdminUserController extends lmbController
{
  protected $_form_id = 'user_form';

  function doCreate()
  {
    $item = new lmbCmsUser();
    $this->useForm($this->_form_id);
    $this->setFormDatasource($item);

    if($this->request->hasPost())
    {
      $this->_import($item);

      $this->_validatePasswordField();

      $this->_validateAndSave($item);
    }
  }

  function doEdit()
  {
    $item = new lmbCmsUser((int)$this->request->get('id'));
    $this->useForm($this->_form_id);
    $this->setFormDatasource($item);

    if($this->request->hasPost())
    {
      $this->_import($item);
      $this->_validateAndSave($item);
    }
  }

  function doDetail()
  {
    if($id=$this->request->get('id'))
    {
      $this->view->set('user', new lmbCmsUser((int)$id));
    }
  }

  protected function _import($item)
  {
    $item->import($this->request->export());
  }

  protected function _validateAndSave($item)
  {
    $item->validate($this->error_list);

    if($this->error_list->isValid())
    {
      $item->saveSkipValidation();
      $this->closePopup();
    }
  }

  function doDelete()
  {
    if(($this->request->hasPost())&&($this->request->get('delete')))
    {
      foreach($this->request->getArray('ids') as $id)
      {
        $item = new lmbCmsUser((int)$id);
        $item->destroy();
      }
      $this->closePopup();
    }

  }

  function doChangePassword()
  {
    if(!$this->request->hasPost())
      return;

    $this->useForm('user_form');
    $this->setFormDatasource($this->request);

    $this->_validatePasswordField();

    if(!$this->error_list->isValid())
      return;

    $user = new lmbCmsUser($this->request->getInteger('id'));
    $user->setPassword($this->request->get('password'));

    if($user->trySave($this->error_list))
      $this->closePopup();
  }

  function _validatePasswordField()
  {
    $validator = new lmbValidator();
    $validator->addRequiredRule('repeat_password');
    $validator->addRule(new lmbMatchRule('password', 'repeat_password'));
    $validator->setErrorList($this->error_list);
    $validator->validate($this->request);
  }
}

