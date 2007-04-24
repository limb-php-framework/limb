<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFormCommand.class.php 5759 2007-04-24 10:19:49Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/command/lmbActionCommand.class.php');
lmb_require('limb/validation/src/lmbValidator.class.php');
lmb_require('limb/validation/src/lmbErrorList.class.php');
lmb_require('limb/core/src/exception/lmbDelegateHaltException.class.php');
lmb_require('limb/core/src/lmbDelegate.class.php');

class lmbFormCommand extends lmbActionCommand
{
  const ON_BEFORE             = 1;
  const ON_SHOW               = 2;
  const ON_AFTER              = 3;
  const ON_BEFORE_VALIDATE    = 4;
  const ON_AFTER_VALIDATE     = 5;
  const ON_VALID              = 6;
  const ON_ERROR              = 7;

  protected $form_id;
  protected $listeners = array();

  protected $validator;
  protected $error_list;

  function __construct($template_path = '', $form_id = '', $validator = null)
  {
    parent :: __construct($template_path);

    if($form_id)
      $this->form_id = $form_id;
    else
      $this->form_id = $this->_guessFormId();

    $this->error_list = new lmbErrorList();
    $this->validator = $validator ? $validator : new lmbValidator($this->error_list);
  }

  protected function _guessFormId()
  {
    $controller = $this->toolkit->getDispatchedController();
    return $controller->getName() . '_form';
  }

  function perform()
  {
    parent :: perform();

    try
    {
      $this->_doPerform();
    }
    catch(lmbDelegateHaltException $e){}
  }

  protected function _doPerform()
  {
    $this->toolkit->getView()->setFormErrors($this->form_id, $this->error_list);

    $this->_onBefore();
    $this->_invokeListeners(self :: ON_BEFORE);

    if(!$this->isSubmitted())
    {
      $this->_onShow();
      $this->_invokeListeners(self :: ON_SHOW);
    }
    else
    {
      $this->_onBeforeValidate();
      $this->_invokeListeners(self :: ON_BEFORE_VALIDATE);

      $this->_validate();

      if(!$this->isValid())
      {
        $this->_onError();
        $this->_invokeListeners(self :: ON_ERROR);
      }
      else
      {
        $this->_onValid();
        $this->_invokeListeners(self :: ON_VALID);
      }

      $this->_onAfterValidate();
      $this->_invokeListeners(self :: ON_AFTER_VALIDATE);
    }

    $this->_onAfter();
    $this->_invokeListeners(self :: ON_AFTER);
  }

  function setFormDatasource($datasource)
  {
    $this->toolkit->getView()->setFormDatasource($this->form_id, $datasource);
  }

  /**
   * @deprecated
   */
  function setViewFormDatasource($datasource)
  {
    $this->setFormDatasource($datasource);
  }

  function getViewFormDatasource()
  {
    return $this->toolkit->getView()->getFormDatasource($this->form_id);
  }

  protected function _onBefore(){}
  protected function _onShow(){}
  protected function _onBeforeValidate(){}
  protected function _onValid(){}
  protected function _onError(){}
  protected function _onAfterValidate(){}
  protected function _onAfter(){}

  function setErrorList($error_list)
  {
    $this->error_list = $error_list;
  }

  protected function _invokeListeners($type)
  {
    if(isset($this->listeners[$type]))
      lmbDelegate :: invokeChain($this->listeners[$type], array($this));
  }

  protected function _validate()
  {
    $this->validator->setErrorList($this->error_list);
    $this->validator->validate($this->request);
  }

  function getFormId()
  {
    return $this->form_id;
  }

  function getValidator()
  {
    return $this->validator;
  }

  function isSubmitted()
  {
    return $this->request->hasPost();
  }

  function isValid()
  {
    return $this->error_list->isValid();
  }

  function getErrorList()
  {
    return $this->error_list;
  }

  function addError($message, $fields = array(), $values = array())
  {
    $this->error_list->addError($message, $fields, $values);
  }

  protected function _getRequestData()
  {
    return $this->request->export();
  }

  function registerCallback($type, $callback)
  {
    $this->listeners[$type][] = $callback;
  }

  function registerOnBeforeCallback($object, $method)
  {
    $this->registerCallback(self :: ON_BEFORE, new lmbDelegate($object, $method));
  }

  function registerOnShowCallback($object, $method)
  {
    $this->registerCallback(self :: ON_SHOW, new lmbDelegate($object, $method));
  }

  function registerOnBeforeValidateCallback($object, $method)
  {
    $this->registerCallback(self :: ON_BEFORE_VALIDATE, new lmbDelegate($object, $method));
  }

  function registerOnAfterValidateCallback($object, $method)
  {
    $this->registerCallback(self :: ON_AFTER_VALIDATE, new lmbDelegate($object, $method));
  }

  function registerOnValidCallback($object, $method)
  {
    $this->registerCallback(self :: ON_VALID, new lmbDelegate($object, $method));
  }

  function registerOnErrorCallback($object, $method)
  {
    $this->registerCallback(self :: ON_ERROR, new lmbDelegate($object, $method));
  }

  function registerOnAfterCallback($object, $method)
  {
    $this->registerCallback(self :: ON_AFTER, new lmbDelegate($object, $method));
  }

  function halt()
  {
    throw new lmbDelegateHaltException('Delegate was halted');
  }
}
?>
