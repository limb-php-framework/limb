<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/web_app/src/controller/lmbAbstractController.class.php');
lmb_require('limb/validation/src/lmbErrorList.class.php');
lmb_require('limb/validation/src/lmbValidator.class.php');
lmb_require('limb/view/src/lmbDummyView.class.php');

/**
 * class lmbController.
 *
 * @package web_app
 * @version $Id: lmbController.class.php 6455 2007-10-26 06:50:27Z serega $
 */
class lmbController extends lmbAbstractController
{
  protected $request;
  protected $response;
  protected $session;
  protected $view;
  protected $error_list;
  protected $validator;
  protected $form_id;
  protected $in_popup = true;

  function __construct()
  {
    parent :: __construct();

    $this->request = $this->toolkit->getRequest();
    $this->response = $this->toolkit->getResponse();
    $this->session = $this->toolkit->getSession();
    $this->view = $this->toolkit->getView();//this is a dummy view, which will be replaced with a concrete one
    $this->error_list = new lmbErrorList();
    $this->validator = new lmbValidator($this->error_list);
  }

  function getView()
  {
    return $this->view;
  }

  function validate($dataspace)
  {
    $this->validator->validate($dataspace);
    return $this->validator->isValid();
  }

  function actionExists($action)
  {
    if(method_exists($this, $this->_mapActionToMethod($action)))
      return true;

    if($this->_findTemplateForAction($action))
      return true;

    return false;
  }

  function performAction()
  {
    if(method_exists($this, $this->_mapCurrentActionToMethod()))
    {
      if($template_path = $this->_findTemplateForAction($this->current_action))
      {
        $this->setTemplate($template_path);
      }

      $method = $this->_mapCurrentActionToMethod($this->_mapCurrentActionToMethod());
      $res = $this->$method();

      $this->_passLocalAttributesToView();

      if(is_string($res))
        $this->response->write($res);
      elseif($this->response->isEmpty() && is_a($this->view, 'lmbDummyView'))
        $this->response->write('Default dummy output for controller "' .
                               get_class($this) . '" action "' . $this->current_action . '"');

      return $res;
    }
    elseif($template_path = $this->_findTemplateForAction($this->current_action))
    {
      $this->setTemplate($template_path);
      $this->_passLocalAttributesToView();
      return;
    }

    throw new lmbException('No method defined in controller "' .
                           $this->getName(). '" for action "' . $this->current_action . '" ' .
                           'and no appropriate template found');
  }

  function useForm($form_id)
  {
    $this->form_id = $form_id;
    $this->view->setFormErrors($form_id, $this->error_list);
  }

  function setTemplate($template_path)
  {
    $view = $this->toolkit->createViewByTemplate($template_path);
    //copying stuff from dummy view, do we need this?
    $view->copy($this->view);
    $view->setTemplate($template_path);
    $this->view = $view;
    $this->toolkit->setView($view);
  }

  protected function _passLocalAttributesToView()
  {
    foreach(get_object_vars($this) as $name => $value)
    {
      if($name{0} == '_')
        continue;
      $this->view->set($name, $value);
    }
  }

  function passToView($var, $value)
  {
    $this->view->set($var, $value);
  }

  function resetView()
  {
    $this->view->reset();
  }

  function setFormDatasource($datasource, $form_id = null)
  {
    if(!$form_id && !$this->form_id)
      throw new lmbException('There is no form id specified');

    if(!$form_id)
      $form_id = $this->form_id;

    $this->view->setFormDatasource($form_id, $datasource);
  }

  function redirect($params_or_url = array(), $route_url = null)
  {
    $this->toolkit->redirect($params_or_url, $route_url);
  }

  function forward($controller_name, $action)
  {
    $controller = $this->toolkit->createController($controller_name);
    $controller->setCurrentAction($action);
    return $controller->performAction();
  }

  function forwardTo404()
  {
    return $this->forward('not_found', 'display');
  }

  function forwardTo500()
  {
    return $this->forward('server_error', 'display');
  }

  function flashError($message)
  {
    $this->toolkit->flashError($message);
  }
  
  function flashErrorAndRedirect($message, $redirect = array())
  {
    $this->flashError($message);
    $this->redirect($redirect);
  }

  function flashMessage($message)
  {
    $this->toolkit->flashMessage($message);
  }
  
  function flashAndRedirect($message, $redirect = array())
  {
    $this->flashMessage($message);
    $this->redirect($redirect);
  }

  function flash($message)
  {
    $this->flashMessage($message);
  }

  function addError($message, $fields = array(), $values = array())
  {
    $this->error_list->addError($message, $fields, $values);
  }

  function closePopup()
  {
    if(!$this->in_popup)
      return;

    $this->response->write('<html><script>if(window.opener){window.opener.focus();window.opener.location.reload();window.close();}</script></html>');
  }

  protected function _mapCurrentActionToMethod()
  {
    return $this->_mapActionToMethod($this->current_action);
  }

  protected function _mapActionToMethod($action)
  {
    return lmb_camel_case('do_' . $action);
  }
}


